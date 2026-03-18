<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Pour les installations existantes : le premier fondateur (par id) devient fondateur principal.
     * Tu peux ensuite modifier manuellement en base si besoin.
     */
    public function up(): void
    {
        $first = User::where('role', User::ROLE_FONDATEUR)->orderBy('id')->first();
        if ($first && ! $first->is_fondateur_principal) {
            $first->update(['is_fondateur_principal' => true]);
        }
    }

    public function down(): void
    {
        User::where('role', User::ROLE_FONDATEUR)->update(['is_fondateur_principal' => false]);
    }
};
