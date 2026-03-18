<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('equipes')->where('nom', 'like', '%Faucheuse%')->update(['est_partenaire' => true]);
    }

    public function down(): void
    {
        DB::table('equipes')->where('nom', 'like', '%Faucheuse%')->update(['est_partenaire' => false]);
    }
};
