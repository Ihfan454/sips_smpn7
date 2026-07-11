<?php

namespace Database\Seeders;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class PelanggaranSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            return;
        }

        $pelanggarans = [
            ['nisn' => '0091234501', 'jenis' => 'Terlambat masuk sekolah', 'kategori' => 'ringan', 'poin' => 5, 'status' => 'selesai', 'days_ago' => 2],
            ['nisn' => '0091234503', 'jenis' => 'Keluar kelas tanpa izin', 'kategori' => 'sedang', 'poin' => 10, 'status' => 'proses', 'days_ago' => 5],
            ['nisn' => '0091234505', 'jenis' => 'Berbicara tidak sopan', 'kategori' => 'sedang', 'poin' => 15, 'status' => 'proses', 'days_ago' => 7],
            ['nisn' => '0081234501', 'jenis' => 'Bolos tanpa keterangan', 'kategori' => 'berat', 'poin' => 25, 'status' => 'selesai', 'days_ago' => 10],
            ['nisn' => '0081234503', 'jenis' => 'Berkelahi', 'kategori' => 'berat', 'poin' => 30, 'status' => 'proses', 'days_ago' => 3],
            ['nisn' => '0071234501', 'jenis' => 'Bullying', 'kategori' => 'berat', 'poin' => 40, 'status' => 'proses', 'days_ago' => 1],
            ['nisn' => '0071234503', 'jenis' => 'Menggunakan HP tanpa izin', 'kategori' => 'sedang', 'poin' => 10, 'status' => 'selesai', 'days_ago' => 14],
            ['nisn' => '0091234502', 'jenis' => 'Seragam tidak rapi', 'kategori' => 'ringan', 'poin' => 5, 'status' => 'selesai', 'days_ago' => 4],
            ['nisn' => '0081234502', 'jenis' => 'Membuang sampah sembarangan', 'kategori' => 'ringan', 'poin' => 5, 'status' => 'selesai', 'days_ago' => 6],
            ['nisn' => '0071234502', 'jenis' => 'Terlambat masuk sekolah', 'kategori' => 'ringan', 'poin' => 5, 'status' => 'selesai', 'days_ago' => 8],
        ];

        foreach ($pelanggarans as $item) {
            $siswa = Siswa::where('nisn', $item['nisn'])->first();
            if (! $siswa) {
                continue;
            }

            Pelanggaran::create([
                'siswa_id' => $siswa->id,
                'user_id' => $user->id,
                'jenis_pelanggaran' => $item['jenis'],
                'kategori' => $item['kategori'],
                'poin' => $item['poin'],
                'tanggal_pelanggaran' => now()->subDays($item['days_ago'])->toDateString(),
                'status' => $item['status'],
                'tindakan' => $item['status'] === 'selesai' ? 'Sudah ditangani oleh Guru BK' : null,
            ]);
        }
    }
}
