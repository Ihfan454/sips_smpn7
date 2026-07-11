<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nuptk', 'nip');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('ni_pppk', 20)->nullable()->unique()->after('nip');
        });

        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->renameColumn('nip_pppk', 'nip');
        });

        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->string('ni_pppk', 20)->nullable()->unique()->after('nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ni_pppk');
            $table->renameColumn('nip', 'nuptk');
        });

        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->dropColumn('ni_pppk');
            $table->renameColumn('nip', 'nip_pppk');
        });
    }
};
