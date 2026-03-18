<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * fee.kounasse.35 = Unions uniquement : ne doit pas apparaître dans Match partenaire (Faucheuse).
     */
    public function up(): void
    {
        $search = 'fee.kounasse.35';
        $normalized = str_replace('.', '', $search); // feekounasse35

        $createur = DB::table('createurs')
            ->where('pseudo_tiktok', 'like', '%' . $search . '%')
            ->orWhere('pseudo_tiktok', 'like', '%' . $normalized . '%')
            ->first();

        if (! $createur) {
            $user = DB::table('users')
                ->where('username', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . $normalized . '%')
                ->first();
            if ($user) {
                $createur = DB::table('createurs')->where('user_id', $user->id)->first();
            }
        }

        if ($createur) {
            DB::table('createurs')->where('id', $createur->id)->update(['est_partenaire' => false]);
        }
    }

    public function down(): void
    {
        // Pas de rollback pour ne pas remettre en partenaire par erreur
    }
};
