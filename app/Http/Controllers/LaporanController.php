<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal: awal bulan ini sampai hari ini
        $tanggal_mulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', Carbon::now()->toDateString());
        $kelas_selected = $request->input('kelas');
        $kategori_selected = $request->input('kategori');
        $search = $request->input('search');

        $list_kelas = \App\Models\Kelas::orderBy('nama', 'asc')->pluck('nama');

        // Query Pelanggaran
        $query = Pelanggaran::with('siswa');

        // Filter Rentang Tanggal
        if ($tanggal_mulai) {
            $query->whereDate('tanggal_pelanggaran', '>=', $tanggal_mulai);
        }
        if ($tanggal_selesai) {
            $query->whereDate('tanggal_pelanggaran', '<=', $tanggal_selesai);
        }

        // Filter Kelas
        if ($kelas_selected) {
            $query->whereHas('siswa', function ($q) use ($kelas_selected) {
                $q->where('kelas', $kelas_selected);
            });
        }

        // Filter Kategori
        if ($kategori_selected) {
            $query->where('kategori', $kategori_selected);
        }

        // Filter Pencarian Siswa
        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Dapatkan data pelanggaran yang terfilter
        $data_pelanggaran = $query->latest('tanggal_pelanggaran')
            ->latest('created_at')
            ->get();

        // Hitung Statistik Berdasarkan Hasil Filter
        $stats = [
            'total' => $data_pelanggaran->count(),
            'ringan' => $data_pelanggaran->where('kategori', 'ringan')->count(),
            'sedang' => $data_pelanggaran->where('kategori', 'sedang')->count(),
            'berat' => $data_pelanggaran->where('kategori', 'berat')->count(),
            'total_poin' => $data_pelanggaran->sum('poin'),
        ];

        return view('laporan', compact(
            'data_pelanggaran',
            'list_kelas',
            'tanggal_mulai',
            'tanggal_selesai',
            'kelas_selected',
            'kategori_selected',
            'search',
            'stats'
        ));
    }
}
