<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin BK',
            'email' => 'admin@smpn7jember.sch.id',
            'nip' => '198001012005011001',
            'password' => Hash::make('password'),
            'jabatan' => 'Guru BK',
            'role' => 'admin_bk',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->call([
            KategoriPelanggaranSeeder::class,
            WaliKelasSeeder::class,
            SiswaSeeder::class,
            PelanggaranSeeder::class,
        ]);
    }
}
