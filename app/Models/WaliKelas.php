<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaliKelas extends Model
{
    protected $table = 'wali_kelas';

    protected $fillable = [
        'nip',
        'ni_pppk',
        'nama_lengkap',
        'kelas_wali',
        'no_hp',
        'alamat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'wali_kelas_id');
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'wali_kelas_id');
    }
}
