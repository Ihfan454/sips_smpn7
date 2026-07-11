<?php

namespace Database\Seeders;

use App\Models\WaliKelas;
use Illuminate\Database\Seeder;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        $wali_kelas = [
            ['nip' => '198001012005011001', 'nama_lengkap' => 'Budi Santoso, S.Pd.', 'kelas_wali' => '7A', 'no_hp' => '081234567001'],
            ['nip' => '198001012005011002', 'nama_lengkap' => 'Siti Aminah, S.Pd.', 'kelas_wali' => '7B', 'no_hp' => '081234567002'],
            ['nip' => '198001012005011003', 'nama_lengkap' => 'Ahmad Fauzi, S.Pd.', 'kelas_wali' => '8A', 'no_hp' => '081234567003'],
            ['nip' => '198001012005011004', 'nama_lengkap' => 'Dewi Lestari, S.Pd.', 'kelas_wali' => '9A', 'no_hp' => '081234567004'],
        ];

        foreach ($wali_kelas as $guru) {
            WaliKelas::create($guru);
        }
    }
}
