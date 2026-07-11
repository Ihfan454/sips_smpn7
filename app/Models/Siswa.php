<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas',
        'class_id',
        'tahun_ajaran',
        'no_hp',
        'alamat',
        'wali_kelas_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function kelasRelation(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(WaliKelas::class, 'wali_kelas_id');
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'siswa_id');
    }

    public function getTotalPoinAttribute(): int
    {
        return (int) $this->pelanggarans()->sum('poin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
