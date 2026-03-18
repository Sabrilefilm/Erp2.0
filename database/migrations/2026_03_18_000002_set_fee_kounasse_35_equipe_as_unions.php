<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * fee.kounasse.35 = personne Unions : s'assurer que son équipe n'est pas marquée partenaire.
     */
    public function up(): void
    {
        $search = 'fee.kounasse.35';
        $equipeId = null;

        $createur = DB::table('createurs')->where('pseudo_tiktok', 'like', '%' . $search . '%')
            ->orWhere('pseudo_tiktok', 'like', '%' . str_replace('.', '', $search) . '%')
            ->first();
        if ($createur && $createur->equipe_id) {
            $equipeId = $createur->equipe_id;
        }
        if ($equipeId === null) {
            $user = DB::table('users')->where('username', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . str_replace('.', '', $search) . '%')
                ->first();
            if ($user) {
                $createurByUser = DB::table('createurs')->where('user_id', $user->id)->first();
                if ($createurByUser && $createurByUser->equipe_id) {
                    $equipeId = $createurByUser->equipe_id;
                }
            }
        }

        if ($equipeId !== null) {
            DB::table('equipes')->where('id', $equipeId)->update(['est_partenaire' => false]);
        }
    }

    public function down(): void
    {
        // Pas de rollback automatique pour ne pas re-marquer partenaire par erreur
    }
};
