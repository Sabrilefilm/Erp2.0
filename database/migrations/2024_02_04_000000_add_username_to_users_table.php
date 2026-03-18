<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $column = $table->string('username')->nullable()->unique();
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $column->after('id');
            }
        });

        $used = [];
        foreach (\DB::table('users')->get() as $user) {
            $base = Str::slug($user->name, '');
            $username = $base;
            $i = 0;
            while (in_array($username, $used, true)) {
                $i++;
                $username = $base . $i;
            }
            $used[] = $username;
            \DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
