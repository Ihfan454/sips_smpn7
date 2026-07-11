<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelanggaran extends Model
{
    protected $table = 'pelanggaran';

    protected $fillable = [
        'siswa_id',
        'user_id',
        'wali_kelas_id',
        'jenis_pelanggaran',
        'kategori',
        'poin',
        'deskripsi',
        'tanggal_pelanggaran',
        'waktu_pelanggaran',
        'tempat_pelanggaran',
        'tindakan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pelanggaran' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(WaliKelas::class, 'wali_kelas_id');
    }
}
