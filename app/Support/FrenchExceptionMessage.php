<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class FrenchExceptionMessage
{
    /**
     * Retourne un message d'erreur en français pour l'affichage à l'utilisateur.
     */
    public static function getMessage(\Throwable $e): string
    {
        $msg = $e->getMessage();

        if ($e instanceof ValidationException) {
            return self::translateValidationMessage($e);
        }

        return self::translateGenericMessage($msg);
    }

    /**
     * Retourne une solution ou un conseil en français pour corriger l'erreur, ou null si aucune.
     */
    public static function getSolution(\Throwable $e): ?string
    {
        $msg = $e->getMessage();

        // Champ obligatoire / validation
        if (preg_match('/field\s+is\s+required|est obligatoire/i', $msg) || $e instanceof ValidationException) {
            return 'Renseignez les colonnes indiquées dans le fichier Excel. Chaque ligne (sauf les lignes vides) doit avoir au moins un email ou un pseudo TikTok. Supprimez les lignes entièrement vides en fin de fichier.';
        }
        // Email invalide
        if (stripos($msg, 'email') !== false && (stripos($msg, 'valid') !== false || stripos($msg, 'courriel') !== false)) {
            return 'Vérifiez le format des adresses email dans le fichier (ex. : nom@domaine.com).';
        }
        // Fichier
        if (stripos($msg, 'file') !== false || stripos($msg, 'fichier') !== false || stripos($msg, 'read') !== false || stripos($msg, 'open stream') !== false) {
            return 'Vérifiez que le fichier est bien au format .xlsx ou .xls, qu\'il n\'est pas ouvert dans un autre programme et qu\'il n\'est pas corrompu. Réessayez avec une copie du fichier.';
        }
        // Doublon
        if (stripos($msg, 'Duplicate') !== false || stripos($msg, 'Doublon') !== false) {
            return 'Deux lignes ont le même email et pseudo TikTok. Supprimez le doublon ou modifiez l\'une des deux lignes.';
        }
        // Clé étrangère / contrainte (agent, équipe, etc.)
        if (stripos($msg, 'Foreign key') !== false || stripos($msg, 'Integrity constraint') !== false || stripos($msg, 'Référence invalide') !== false || stripos($msg, 'Contrainte') !== false) {
            return 'Un agent, ambassadeur ou équipe indiqué dans le fichier n\'existe pas dans l\'application. Vérifiez les colonnes agent_email, ambassadeur_email et equipe, ou créez d\'abord ces utilisateurs/équipes.';
        }
        // SQL / base de données
        if (stripos($msg, 'SQLSTATE') !== false || stripos($msg, 'Erreur base de données') !== false) {
            return 'Problème de base de données. Vérifiez que le serveur est accessible. Si l\'erreur persiste, contactez l\'administrateur.';
        }
        // Erreur sur une ligne (depuis CreateursImport)
        if (stripos($msg, 'Ligne ') === 0 || stripos($msg, 'ligne ') !== false) {
            return 'Ouvrez le fichier Excel, repérez le numéro de ligne indiqué et corrigez ou complétez les données (nom, email ou pseudo_tiktok, etc.).';
        }

        return 'Vérifiez votre fichier Excel (colonnes, format des données) et consultez le modèle fourni. En cas de doute, contactez l\'administrateur.';
    }

    private static function translateValidationMessage(ValidationException $e): string
    {
        $errors = $e->errors();
        $messages = [];
        foreach ($errors as $msgs) {
            foreach ($msgs as $m) {
                $messages[] = self::translateGenericMessage($m);
            }
        }
        return implode(' ', $messages);
    }

    private static function translateGenericMessage(string $msg): string
    {
        $out = $msg;

        // "The 2.nom field is required. (and 20 more errors)" -> "Le champ nom (ligne 2) est obligatoire. (et 20 autre(s) erreur(s))"
        if (preg_match('/^The\s+(\d+)\.(\S+)\s+field\s+is\s+required\.\s*(?:\(and\s+(\d+)\s+more\s+errors?\))?/i', $out, $m)) {
            $suffix = isset($m[3]) ? ' (et ' . $m[3] . ' autre(s) erreur(s))' : '';
            return 'Le champ ' . $m[2] . ' (ligne ' . $m[1] . ') est obligatoire.' . $suffix;
        }
        // "(and 20 more errors)" seul ou en fin de phrase
        $out = preg_replace('/\(and\s+(\d+)\s+more\s+errors?\)/i', '(et $1 autre(s) erreur(s))', $out);
        // "The X.Y field is required"
        $out = preg_replace('/The\s+(\d+)\.(\S+)\s+field\s+is\s+required/i', 'Le champ $2 (ligne $1) est obligatoire', $out);
        // "The field X is required"
        $out = preg_replace('/The\s+(\S+)\s+field\s+is\s+required/i', 'Le champ $1 est obligatoire', $out);
        // "field is required"
        $out = str_ireplace('field is required', 'est obligatoire', $out);

        $replacements = [
            'The given data was invalid.' => 'Les données envoyées sont invalides.',
            'must only contain letters, numbers, dashes, and underscores' => 'ne doit contenir que des lettres, chiffres, tirets et underscores',
            'field must only contain letters, numbers, dashes, and underscores' => 'ne doit contenir que des lettres, chiffres, tirets et underscores',
            'field must only contain letters and numbers' => 'ne doit contenir que des lettres et des chiffres',
            'field must be a valid email address' => 'doit être une adresse courriel valide',
            'field must be a valid email' => 'doit être un courriel valide',
            'field may not be greater than' => 'ne doit pas dépasser',
            'field must be at least' => 'doit contenir au moins',
            'field must be an integer' => 'doit être un nombre entier',
            'field must be a number' => 'doit être un nombre',
            'field must be a string' => 'doit être du texte',
            'SQLSTATE[' => 'Erreur base de données : ',
            'Integrity constraint violation' => 'Contrainte non respectée (doublon ou référence invalide)',
            'Duplicate entry' => 'Doublon : cette entrée existe déjà',
            'Foreign key constraint fails' => 'Référence invalide (clé étrangère)',
            'Could not read file' => 'Impossible de lire le fichier',
            'File not found' => 'Fichier introuvable',
        ];

        foreach ($replacements as $en => $fr) {
            $out = str_ireplace($en, $fr, $out);
        }

        return $out;
    }
}
