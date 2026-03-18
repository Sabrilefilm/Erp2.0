<?php

namespace App\Imports;

use App\Models\Createur;
use App\Support\FrenchExceptionMessage;
use App\Support\HeuresHelper;
use App\Models\CreateurStatMensuelle;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CreateursImport implements ToCollection, WithStartRow
{
    /** Structure fichier « Données du/de la créateur(trice) » :
     *  - Colonne C = Nom d'utilisateur du/de la créateur(trice) (username)
     *  - Colonne H = Diamants
     *  - Colonne I = Durée de LIVE (heures)
     *  - Colonne J = Jours de passage en LIVE valides
     */
    private const COL_USERNAME = 2;   // C - Nom d'utilisateur
    private const COL_DIAMANTS = 7;   // H
    private const COL_HEURES_L30D = 8;  // I - Durée de LIVE
    private const COL_JOURS_L30D = 9;   // J - Jours de passage en LIVE valides

    protected int $imported = 0;
    protected int $errors = 0;
    protected string $errorMessage = '';
    /** @var array<int, array{type: string, line: int, msg: string}> */
    protected array $logLines = [];
    protected int $annee;
    protected int $mois;

    public function __construct(?int $annee = null, ?int $mois = null)
    {
        $this->annee = $annee ?? (int) now()->format('Y');
        $this->mois = $mois ?? (int) now()->format('n');
    }

    /** Lire à partir de la ligne 1 ; la première ligne (si elle ressemble à un en-tête) sera ignorée. */
    public function startRow(): int
    {
        return 1;
    }

    /**
     * Récupération : C (username), H (diamants), I (heures), J (jours).
     * L'import ne modifie jamais equipe_id, agence ni rôles — uniquement heures, jours, diamants et historique mensuel.
     */
    public function collection(Collection $rows): void
    {
        $this->logLines[] = ['type' => 'info', 'line' => 0, 'msg' => 'Import — Colonnes C (username), H (diamants), I (heures), J (jours). Agences et rôles ne sont pas modifiés.'];
        $isCurrentMonth = $this->annee === (int) now()->format('Y') && $this->mois === (int) now()->format('n');
        if (! $isCurrentMonth) {
            $this->logLines[] = ['type' => 'info', 'line' => 0, 'msg' => 'Import pour un mois passé ('.$this->mois.'/'.$this->annee.'). Seul l\'historique mensuel est mis à jour ; l\'affichage « mois en cours » (fiche / Mes créateurs) ne change pas — pas d\'accumulation.'];
        }
        $excelLine = 0;
        foreach ($rows as $row) {
            $excelLine++;
            $row = $row instanceof Collection ? $row->all() : (is_array($row) ? $row : iterator_to_array($row));
            $username = trim((string) ($this->cell($row, self::COL_USERNAME, 'utilisateur') ?? ''));
            if ($username === '') {
                continue;
            }
            // Ignorer la ligne d'en-tête (titres comme "Nom d'utilisateur", "ID créateur", etc.)
            $lower = mb_strtolower($username);
            if (str_contains($lower, 'utilisateur') && (str_contains($lower, 'créateur') || str_contains($lower, 'nom'))) {
                continue;
            }
            if (str_contains($lower, 'id créateur') || $username === 'ID créateur(trice)') {
                continue;
            }
            $heuresMois = $this->heuresFromCell($this->cell($row, self::COL_HEURES_L30D, 'Durée de LIVE'));
            $joursMois = $this->numericFromCell($this->cell($row, self::COL_JOURS_L30D, 'Jours de passage en LIVE valides'));
            $diamants = $this->numericFromCell($this->cell($row, self::COL_DIAMANTS, 'Diamants'));
            // Heures et jours = live du mois : jours max 31, heures max 31*24. Vide → 0 par défaut.
            $joursMois = $this->clampJoursMois($joursMois, $excelLine);
            if ($joursMois === null) {
                $joursMois = 0;
            }
            $heuresMois = $this->clampHeuresMois($heuresMois, $excelLine);
            if ($heuresMois === null) {
                $heuresMois = 0.0;
            }
            try {
                $user = User::where('username', $username)->first()
                    ?? User::whereRaw('LOWER(TRIM(username)) = ?', [mb_strtolower(trim($username))])->first();
                if ($user) {
                    if ($user->role !== User::ROLE_CREATEUR) {
                        $this->logLines[] = ['type' => 'info', 'line' => $excelLine, 'msg' => 'Ligne '.$excelLine.' : username "'.$username.'" déjà utilisé pour un autre rôle ('.$user->getRoleLabel().'). Ligne passée.'];
                        continue;
                    }
                } else {
                    $email = $this->uniqueEmailForUsername($username);
                    $user = User::create([
                        'name' => $username,
                        'username' => $username,
                        'email' => $email,
                        'password' => Hash::make(Str::random(32)),
                        'role' => User::ROLE_CREATEUR,
                    ]);
                    $msgEmail = str_contains($email, '@import.agence.local')
                        ? 'Ligne '.$excelLine.' : utilisateur créé automatiquement pour "'.$username.'" (aucun email en Excel). Connexion possible après réinitialisation du mot de passe.'
                        : 'Ligne '.$excelLine.' : utilisateur créé automatiquement pour "'.$username.'" ('.$email.'). Connexion possible après réinitialisation du mot de passe.';
                    $this->logLines[] = ['type' => 'success', 'line' => $excelLine, 'msg' => $msgEmail];
                }
                $createur = Createur::where('user_id', $user->id)->first();
                if (! $createur) {
                    $createur = Createur::create([
                        'user_id' => $user->id,
                        'nom' => $user->name,
                        'email' => $user->email,
                        'heures_mois' => $isCurrentMonth && $heuresMois !== null ? (float) $heuresMois : null,
                        'jours_mois' => $isCurrentMonth && $joursMois !== null ? (int) $joursMois : null,
                        'diamants' => $isCurrentMonth && $diamants !== null ? (int) round($diamants) : null,
                        'date_import' => now(),
                    ]);
                } else {
                    $data = ['date_import' => now()];
                    if ($isCurrentMonth) {
                        $data['heures_mois'] = $heuresMois !== null ? (float) $heuresMois : null;
                        $data['jours_mois'] = $joursMois !== null ? (int) $joursMois : null;
                        $data['diamants'] = $diamants !== null ? (int) round($diamants) : null;
                    }
                    $createur->update($data);
                }
                CreateurStatMensuelle::updateOrCreate(
                    [
                        'createur_id' => $createur->id,
                        'annee' => $this->annee,
                        'mois' => $this->mois,
                    ],
                    [
                        'heures_stream' => $heuresMois !== null ? (float) $heuresMois : null,
                        'jours_stream' => $joursMois !== null ? (int) $joursMois : null,
                        'diamants' => $diamants !== null ? (int) round($diamants) : null,
                    ]
                );
                $this->imported++;
                $msg = sprintf(
                    'Ligne %d : %s — Jours=%s, Heures=%s, Diamants=%s',
                    $excelLine,
                    $username,
                    $joursMois !== null ? (string) $joursMois : '—',
                    $heuresMois !== null ? HeuresHelper::format((float) $heuresMois) : '—',
                    $diamants !== null ? number_format((int) $diamants, 0, ',', ' ') : '—'
                );
                $this->logLines[] = ['type' => 'success', 'line' => $excelLine, 'msg' => $msg];
            } catch (\Throwable $e) {
                $this->errors++;
                $messageFr = FrenchExceptionMessage::getMessage($e);
                $this->errorMessage .= $messageFr.' ; ';
                $this->logLines[] = ['type' => 'error', 'line' => $excelLine, 'msg' => 'Ligne '.$excelLine.' : '.$messageFr];
            }
        }
        $this->logLines[] = ['type' => 'info', 'line' => 0, 'msg' => sprintf('Import terminé. %d ligne(s) importée(s), %d erreur(s).', $this->imported, $this->errors)];
        if ($this->errors > 0) {
            $this->logLines[] = ['type' => 'solution', 'line' => 0, 'msg' => '💡 Colonne C = username exact. Colonne I (Durée de LIVE) : 7h30 ou 7.5. Colonnes J (jours), H (diamants) : nombres entiers.'];
        }
    }

    /**
     * Récupère la valeur d'une cellule par index (0-based) ou par clé d'en-tête si la ligne est associative.
     */
    private function cell(array $row, int $index, string $headerHint = ''): mixed
    {
        if (isset($row[$index])) {
            return $row[$index];
        }
        if ($headerHint !== '' && ! array_is_list($row)) {
            foreach (array_keys($row) as $key) {
                if (is_string($key) && stripos($key, $headerHint) !== false) {
                    return $row[$key] ?? null;
                }
            }
        }
        return null;
    }

    /**
     * Heures depuis une cellule : accepte "7h30", "7:30", "7.5", 7.5, etc.
     * Excel peut stocker la durée comme fraction de jour (ex. 0,104 = 2h30) : on convertit en heures.
     */
    private function heuresFromCell(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        // Format Excel "durée" : nombre entre 0 et 1 = fraction de jour (ex. 0,104166 ≈ 2h30)
        if (is_numeric($value)) {
            $v = (float) $value;
            if ($v > 0 && $v < 1) {
                return round($v * 24, 2);
            }
            if ($v >= 1) {
                return round($v, 2);
            }
        }
        $str = is_string($value) ? trim($value) : null;
        if ($str !== null && $str !== '') {
            $parsed = HeuresHelper::parse($str);
            if ($parsed !== null) {
                return $parsed;
            }
        }
        $num = $this->numericFromCell($value);
        if ($num !== null) {
            return $num > 0 && $num < 1 ? round($num * 24, 2) : round($num, 2);
        }
        return null;
    }

    /**
     * Valeur numérique depuis une cellule (Excel peut renvoyer float, string avec espaces ou virgule).
     * Accepte : 1234, "1 234", "1,5", "1.5", "50 000" (diamants), etc.
     */
    private function numericFromCell(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $str = is_string($value) ? $value : (string) $value;
        $str = trim(str_replace(["\u{00A0}", ' ', ','], ['', '', '.'], $str));
        if ($str === '') {
            return null;
        }
        if (is_numeric($str)) {
            return (float) $str;
        }
        return null;
    }

    /** Jours de live dans le mois : max 31 (impossible d'avoir plus de 31 jours dans un mois). */
    private function clampJoursMois(?float $value, int $excelLine): ?int
    {
        if ($value === null) {
            return null;
        }
        $v = (int) $value;
        if ($v < 0) {
            $this->logLines[] = ['type' => 'error', 'line' => $excelLine, 'msg' => 'Ligne '.$excelLine.' : jours invalide ('.$v.'), valeur négative ignorée.'];
            return null;
        }
        if ($v > 31) {
            $this->logLines[] = ['type' => 'solution', 'line' => $excelLine, 'msg' => 'Ligne '.$excelLine.' : jours invalide ('.$v.'), plafonné à 31 (max jours dans un mois).'];
            return 31;
        }
        return $v;
    }

    /** Heures de live dans le mois : max 31*24 = 744 (décimal autorisé pour les minutes). */
    private function clampHeuresMois(?float $value, int $excelLine): ?float
    {
        if ($value === null) {
            return null;
        }
        $v = (float) $value;
        if ($v < 0) {
            $this->logLines[] = ['type' => 'error', 'line' => $excelLine, 'msg' => 'Ligne '.$excelLine.' : heures invalide ('.$v.'), valeur négative ignorée.'];
            return null;
        }
        if ($v > 744) {
            $this->logLines[] = ['type' => 'solution', 'line' => $excelLine, 'msg' => 'Ligne '.$excelLine.' : heures invalide ('.$v.'), plafonné à 744 (max pour 31 jours).'];
            return 744.0;
        }
        return round($v, 2);
    }

    /**
     * Génère un email unique pour un nouveau créateur (évite les doublons en base).
     * Format : slug(username)@import.agence.local ou slug+2@... si déjà pris.
     */
    private function uniqueEmailForUsername(string $username): string
    {
        $prefix = Str::slug($username, '');
        if ($prefix === '') {
            $prefix = 'createur';
        }
        $base = $prefix.'@import.agence.local';
        if (! User::where('email', $base)->exists()) {
            return $base;
        }
        $i = 1;
        do {
            $email = $prefix.'+'.$i.'@import.agence.local';
            $i++;
        } while (User::where('email', $email)->exists());

        return $email;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /** @return array<int, array{type: string, line: int, msg: string}> */
    public function getLogLines(): array
    {
        return $this->logLines;
    }
}
