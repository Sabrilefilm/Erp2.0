<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessagerieController extends Controller
{
    /**
     * Règles de confidentialité :
     * - Créateur   → peut uniquement écrire à son agent assigné
     * - Agent      → peut écrire à ses créateurs + son manager
     * - Staff      → peut écrire aux membres de son équipe / sous-ordonnés
     * - Fondateur  → peut accéder à toutes les conversations (vue complète)
     */

    public function index(Request $request)
    {
        $me = auth()->user();
        $roleFilter = $request->get('role');

        // Utilisateurs autorisés à contacter selon le rôle
        $allowedUsers = $this->getAllowedUsers($me, $roleFilter);

        [$conversations, $interlocutors, $unreadCounts] = $this->getConversations($me);

        return view('messagerie.index', compact('allowedUsers', 'conversations', 'interlocutors', 'unreadCounts', 'roleFilter'));
    }

    public function conversation(Request $request, User $user)
    {
        $me = auth()->user();

        // Vérification d'autorisation : peut-on parler à cet utilisateur ?
        $this->authorizeConversation($me, $user);

        // Marquer comme lus
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::betweenUsers($me->id, $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();

        $roleFilter   = $request->get('role');
        $allowedUsers = $this->getAllowedUsers($me, $roleFilter);
        [$conversations, $interlocutors, $unreadCounts] = $this->getConversations($me);

        return view('messagerie.index', compact('allowedUsers', 'conversations', 'interlocutors', 'unreadCounts', 'roleFilter', 'messages', 'user'));
    }

    /** Taille max pièce jointe : 1 Go */
    private const FICHIER_MAX_KB = 1024 * 1024;

    public function send(Request $request, User $user)
    {
        $me = auth()->user();
        $this->authorizeConversation($me, $user);

        $rules = [
            'contenu' => 'required|string|max:2000',
            'fichier' => 'nullable|file|max:' . self::FICHIER_MAX_KB,
        ];
        $request->validate($rules, [
            'fichier.max' => 'Le fichier ne doit pas dépasser 1 Go.',
        ]);

        $data = [
            'sender_id'   => $me->id,
            'receiver_id' => $user->id,
            'contenu'     => $request->contenu,
        ];

        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $path = $file->store('messagerie/fichiers', 'local');
            $data['fichier_path'] = $path;
            $data['fichier_nom'] = $file->getClientOriginalName();
        }

        Message::create($data);

        return redirect()->route('messagerie.conversation', $user)->with('sent', true);
    }

    /** Téléchargement de la pièce jointe d'un message (réservé à l'expéditeur ou au destinataire). */
    public function fichier(Message $message): StreamedResponse
    {
        $me = auth()->user();
        if ($message->sender_id !== $me->id && $message->receiver_id !== $me->id) {
            abort(403, 'Accès non autorisé à ce fichier.');
        }
        if (!$message->fichier_path || !Storage::disk('local')->exists($message->fichier_path)) {
            abort(404, 'Fichier introuvable.');
        }
        $nom = $message->fichier_nom ?? 'piece-jointe';
        return Storage::disk('local')->download($message->fichier_path, $nom);
    }

    public function markRead(Request $request, User $user)
    {
        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /** La suppression des conversations est désactivée : les messages restent conservés. */
    public function deleteConversation(Request $request, User $user)
    {
        $me = auth()->user();
        $this->authorizeConversation($me, $user);

        // Ne pas supprimer les messages : ils restent archivés.
        return redirect()->route('messagerie.index')->with('info', 'Les messages sont conservés et ne peuvent pas être supprimés.');
    }

    /** Formulaire d'envoi groupé. Les agents ne peuvent envoyer qu'à leurs créateurs. */
    public function groupeForm(Request $request)
    {
        $me           = auth()->user();
        $allowedUsers = $this->getAllowedUsersForGroupe($me);
        return view('messagerie.groupe', compact('allowedUsers'));
    }

    /** Envoi groupé (même message → plusieurs destinataires). Pièce jointe optionnelle, max 1 Go. */
    public function groupeSend(Request $request)
    {
        $me = auth()->user();
        $request->validate([
            'destinataires'   => 'required|array|min:1',
            'destinataires.*' => 'exists:users,id',
            'contenu'         => 'required|string|max:2000',
            'fichier'         => 'nullable|file|max:' . self::FICHIER_MAX_KB,
        ], [
            'fichier.max' => 'Le fichier ne doit pas dépasser 1 Go.',
        ]);

        $fichierPath = null;
        $fichierNom  = null;
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $fichierPath = $file->store('messagerie/fichiers', 'local');
            $fichierNom  = $file->getClientOriginalName();
        }

        $allowed = $this->getAllowedUsersForGroupe($me)->pluck('id')->toArray();
        $sent = 0;
        foreach ($request->destinataires as $id) {
            if (!in_array((int)$id, $allowed)) continue;
            Message::create([
                'sender_id'    => $me->id,
                'receiver_id'  => $id,
                'contenu'      => $request->contenu,
                'fichier_path' => $fichierPath,
                'fichier_nom'  => $fichierNom,
            ]);
            $sent++;
        }

        return redirect()->route('messagerie.index')
            ->with('success', "Message envoyé à {$sent} destinataire(s).");
    }

    // ──────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────

    /**
     * Liste des utilisateurs sélectionnables pour un message groupé.
     * Les agents ne peuvent envoyer qu'à leurs créateurs ; les autres rôles gardent getAllowedUsers.
     */
    private function getAllowedUsersForGroupe(User $me): \Illuminate\Support\Collection
    {
        if ($me->isAgent() || $me->isAmbassadeur()) {
            $userIds = Createur::where('agent_id', $me->id)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->values();
            return User::whereIn('id', $userIds)->orderBy('name')->get();
        }
        return $this->getAllowedUsers($me);
    }

    /**
     * Retourne la liste des utilisateurs que $me peut contacter.
     * Règle commune : le fondateur est toujours contactable par tout le monde.
     */
    private function getAllowedUsers(User $me, ?string $roleFilter = null): \Illuminate\Support\Collection
    {
        // Fondateur principal : voit tout le monde
        if ($me->isFondateurPrincipal()) {
            return User::where('id', '!=', $me->id)
                ->when($roleFilter, fn ($q) => $q->where('role', $roleFilter))
                ->orderBy('name')
                ->get();
        }

        // Fondateur principal (contactable par tout le monde)
        $fondateurPrincipalId = User::where('is_fondateur_principal', true)->value('id');

        // Fondateur sous-agence ou Directeur scopé à une agence : périmètre = son équipe
        $equipeAgenceId = $me->scopeToAgenceEquipeId();
        if ($equipeAgenceId !== null) {
            $agentIds = User::where('equipe_id', $equipeAgenceId)->whereIn('role', [User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER])->pluck('id');
            $createurUserIds = Createur::where('equipe_id', $equipeAgenceId)->whereNotNull('user_id')->pluck('user_id');
            $ids = User::where('equipe_id', $equipeAgenceId)->pluck('id')
                ->merge($createurUserIds)
                ->push($fondateurPrincipalId)
                ->filter()->unique()->values();
            return User::whereIn('id', $ids)->where('id', '!=', $me->id)
                ->when($roleFilter, fn ($q) => $q->where('role', $roleFilter))
                ->orderBy('name')
                ->get();
        }

        $fondateurId = $fondateurPrincipalId;

        // Créateur : son agent assigné + le fondateur (fiche par user_id ou email, sync user_id si besoin)
        if ($me->isCreateur()) {
            $fiche = $me->getCreateurFiche();
            $agentId = $fiche?->agent_id ?? $me->manager_id;
            $ids = collect([$agentId, $fondateurId])->filter()->unique()->values();
            return User::whereIn('id', $ids)->orderBy('name')->get();
        }

        // Agent / Ambassadeur : ses créateurs + son manager + le fondateur
        if ($me->isAgent() || $me->isAmbassadeur()) {
            $createurUserIds = Createur::where('agent_id', $me->id)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            $ids = $createurUserIds
                ->push($me->manager_id)
                ->push($fondateurId)
                ->filter()->unique()->values();

            return User::whereIn('id', $ids)
                ->when($roleFilter, fn ($q) => $q->where('role', $roleFilter))
                ->orderBy('name')
                ->get();
        }

        // Manager / Sous-manager : ses agents + leurs créateurs + son manager + le fondateur
        if ($me->isManageur() || $me->isSousManager()) {
            $agentIds = User::where('manager_id', $me->id)->pluck('id');

            $createurUserIds = Createur::whereIn('agent_id', $agentIds)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            $ids = $agentIds
                ->merge($createurUserIds)
                ->push($me->manager_id)
                ->push($fondateurId)
                ->filter()->unique()->values();

            return User::whereIn('id', $ids)
                ->when($roleFilter, fn ($q) => $q->where('role', $roleFilter))
                ->orderBy('name')
                ->get();
        }

        // Directeur / Sous-directeur sans agence assignée : tout le monde (y compris fondateur)
        if ($me->isDirecteur() || $me->isSousDirecteur()) {
            return User::where('id', '!=', $me->id)
                ->when($roleFilter, fn ($q) => $q->where('role', $roleFilter))
                ->orderBy('name')
                ->get();
        }

        return collect();
    }

    /**
     * Conversations existantes de $me (uniquement les siennes).
     */
    private function getConversations(User $me): array
    {
        // Fondateur principal : voit TOUTES les conversations de tout le monde
        if ($me->isFondateurPrincipal()) {
            $allMessages = Message::latest()->get();
            $conversations = $allMessages
                ->groupBy(fn ($msg) => implode('-', [min($msg->sender_id, $msg->receiver_id), max($msg->sender_id, $msg->receiver_id)]))
                ->map(fn ($msgs) => $msgs->sortByDesc('created_at')->first())
                ->sortByDesc('created_at');
        } else {
            // Dernier message en premier (conversation la plus récente en haut)
            $conversations = Message::where('sender_id', $me->id)
                ->orWhere('receiver_id', $me->id)
                ->latest()
                ->get()
                ->groupBy(fn ($msg) => $msg->sender_id === $me->id ? $msg->receiver_id : $msg->sender_id)
                ->map(fn ($msgs) => $msgs->sortByDesc('created_at')->first())
                ->sortByDesc('created_at');
        }

        $interlocutors = User::whereIn('id', $conversations->keys()->filter(fn ($id) => ! str_contains((string) $id, '-')))->get()->keyBy('id');

        $unreadCounts = Message::where('receiver_id', $me->id)
            ->whereNull('read_at')
            ->select('sender_id', DB::raw('count(*) as cnt'))
            ->groupBy('sender_id')
            ->pluck('cnt', 'sender_id');

        return [$conversations, $interlocutors, $unreadCounts];
    }

    /**
     * Vérifie que $me a le droit de parler à $target.
     */
    private function authorizeConversation(User $me, User $target): void
    {
        // Fondateur principal : accès total
        if ($me->isFondateurPrincipal()) return;

        $allowed = $this->getAllowedUsers($me);
        abort_unless($allowed->contains('id', $target->id), 403, 'Vous n\'êtes pas autorisé à contacter cet utilisateur.');
    }
}
