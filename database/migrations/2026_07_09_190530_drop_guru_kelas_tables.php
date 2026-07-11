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
        Schema::table('pelanggaran', function (Blueprint $table) {
            $table->dropForeign(['guru_kelas_id']);
            $table->dropColumn('guru_kelas_id');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['wali_kelas_id']);
            $table->dropColumn('wali_kelas_id');
        });

        Schema::dropIfExists('guru_kelas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented to prevent data inconsistency
    }
};
