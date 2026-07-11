<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $siswaQuery = Siswa::query();
        $pelanggaranQuery = Pelanggaran::query();

        $totalSiswa = $siswaQuery->count();
        $totalPelanggaran = $pelanggaranQuery->count();
        
        return response()->json([
            'total_siswa' => $totalSiswa,
            'total_pelanggaran' => $totalPelanggaran,
        ]);
    }

    public function recent(): JsonResponse
    {
        $query = Pelanggaran::with('siswa');

        $pelanggarans = $query->latest('tanggal_pelanggaran')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn (Pelanggaran $p) => [
                'siswa' => $p->siswa?->nama_lengkap ?? '-',
                'kelas' => $p->siswa?->kelas ?? '-',
                'pelanggaran' => $p->jenis_pelanggaran,
                'kategori' => ucfirst($p->kategori),
                'poin' => $p->poin,
                'tanggal' => $p->tanggal_pelanggaran->format('d/m/Y'),
            ]);

        return response()->json(['data' => $pelanggarans]);
    }

    /**
     * Endpoint khusus notifikasi real-time.
     * Mengembalikan pelanggaran yang masuk HARI INI.
     * Mendukung parameter ?after_id=X untuk polling inkremental.
     */
    public function notificationsToday(Request $request): JsonResponse
    {
        $query = Pelanggaran::with('siswa')
            ->whereDate('created_at', today());

        $query->latest('id');

        // Jika client mengirim after_id, hanya kembalikan data yang lebih baru
        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->after_id);
        }

        $items = $query->limit(20)->get()->map(fn (Pelanggaran $p) => [
            'id'          => $p->id,
            'siswa'       => $p->siswa?->nama_lengkap ?? '-',
            'kelas'       => $p->siswa?->kelas ?? '-',
            'pelanggaran' => $p->jenis_pelanggaran,
            'kategori'    => ucfirst($p->kategori),
            'poin'        => $p->poin,
            'waktu'       => $p->created_at->format('H:i'),
            'tanggal'     => $p->created_at->format('d/m/Y'),
        ]);

        $latestQuery = Pelanggaran::whereDate('created_at', today());
        $countQuery = Pelanggaran::whereDate('created_at', today());

        $latestId = $latestQuery->max('id') ?? 0;
        $totalToday = $countQuery->count();

        return response()->json([
            'data'      => $items,
            'count'     => $totalToday,
            'latest_id' => $latestId,
        ]);
    }

    public function charts(): JsonResponse
    {
        $jenisQuery = Pelanggaran::query();
        $kategoriQuery = Pelanggaran::query();

        $byJenis = $jenisQuery->select('jenis_pelanggaran', DB::raw('count(*) as frekuensi'))
            ->groupBy('jenis_pelanggaran')
            ->orderByDesc('frekuensi')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'nama' => $row->jenis_pelanggaran,
                'frekuensi' => (int) $row->frekuensi,
            ]);

        $byKategori = $kategoriQuery->select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->kategori => (int) $row->total]);

        return response()->json([
            'by_jenis' => $byJenis,
            'by_kategori' => [
                'ringan' => $byKategori->get('ringan', 0),
                'sedang' => $byKategori->get('sedang', 0),
                'berat'  => $byKategori->get('berat', 0),
            ],
        ]);
    }
}
