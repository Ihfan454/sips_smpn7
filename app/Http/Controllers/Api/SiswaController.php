<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Siswa::withSum('pelanggarans as total_poin', 'poin')
                      ->with('kelasRelation');

        if ($request->filled('kelas') && $request->kelas !== 'all') {
            $query->whereHas('kelasRelation', function($q) use ($request) {
                $q->where('nama', $request->kelas);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'aktif');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswas = $query->join('kelas', 'siswa.class_id', '=', 'kelas.id')
                        ->orderBy('kelas.nama', 'asc')
                        ->orderBy('siswa.nama_lengkap', 'asc')
                        ->select('siswa.*')
                        ->get()
                        ->map(fn (Siswa $siswa) => $this->formatSiswa($siswa));

        return response()->json(['data' => $siswas]);
    }

    public function stats(): JsonResponse
    {
        $query = Siswa::query();
        


        $siswas = $query->get();
        // Hanya hitung kelas yang memiliki setidaknya satu siswa
        $totalKelas = \App\Models\Kelas::has('siswas')->count();

        return response()->json([
            'total' => $siswas->count(),
            'aktif' => $siswas->where('is_active', true)->count(),
            'nonaktif' => $siswas->where('is_active', false)->count(),
            'total_kelas' => $totalKelas,
        ]);
    }

    /**
     * Kembalikan daftar kelas unik yang ada di database, diurutkan.
     */
    public function kelas(): JsonResponse
    {


        $kelas = \App\Models\Kelas::pluck('nama')->toArray();
        sort($kelas);

        return response()->json(['data' => array_values($kelas)]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk peran Anda.'], 403);
        }

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:20', 'unique:siswa,nis'],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswa,nisn'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['required', 'string', 'max:10'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        // Cari kelas_id yang sesuai nama kelas
        $kelasModel = \App\Models\Kelas::where('nama', $request->kelas)->first();
        $validated['class_id'] = $kelasModel ? $kelasModel->id : null;
        unset($validated['kelas']);

        $validated['tahun_ajaran'] = '2025/2026';
        $validated['is_active'] = $request->boolean('is_active', true);
        
        // Cari wali kelas yang sesuai kelasnya
        $wali = \App\Models\WaliKelas::where('kelas_wali', $request->kelas)->first();
        $validated['wali_kelas_id'] = $wali ? $wali->id : null;

        $siswa = Siswa::create($validated);

        return response()->json([
            'message' => 'Data siswa berhasil ditambahkan',
            'data' => $this->formatSiswa($siswa),
        ], 201);
    }

    public function update(Request $request, Siswa $siswa): JsonResponse
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk peran Anda.'], 403);
        }

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:20', 'unique:siswa,nis,'.$siswa->id],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswa,nisn,'.$siswa->id],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['required', 'string', 'max:10'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        // Cari kelas_id yang sesuai nama kelas
        $kelasModel = \App\Models\Kelas::where('nama', $request->kelas)->first();
        $validated['class_id'] = $kelasModel ? $kelasModel->id : null;
        unset($validated['kelas']);

        $validated['is_active'] = $request->boolean('is_active', true);
        
        // Cari wali kelas yang sesuai kelasnya
        $wali = \App\Models\WaliKelas::where('kelas_wali', $request->kelas)->first();
        $validated['wali_kelas_id'] = $wali ? $wali->id : null;

        $siswa->update($validated);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $this->formatSiswa($siswa->fresh()),
        ]);
    }

    public function destroy(Siswa $siswa): JsonResponse
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk peran Anda.'], 403);
        }

        $siswa->delete();

        return response()->json(['message' => 'Data siswa berhasil dihapus']);
    }

    public function bulkMoveClass(Request $request): JsonResponse
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk peran Anda.'], 403);
        }

        $validated = $request->validate([
            'kelas_asal' => ['required', 'string'],
            'kelas_tujuan' => ['required', 'string'],
        ]);

        $kelasAsal = \App\Models\Kelas::where('nama', $request->kelas_asal)->first();
        
        if (!$kelasAsal) {
            return response()->json(['message' => 'Kelas asal tidak ditemukan.'], 404);
        }
        
        $kelasTujuan = \App\Models\Kelas::firstOrCreate(['nama' => $request->kelas_tujuan]);
        $waliTujuan = \App\Models\WaliKelas::where('kelas_wali', $request->kelas_tujuan)->first();
        
        Siswa::where('class_id', $kelasAsal->id)->update([
            'class_id' => $kelasTujuan->id,
            'wali_kelas_id' => $waliTujuan ? $waliTujuan->id : null,
        ]);
        
        return response()->json([
            'message' => 'Semua siswa di kelas ' . $request->kelas_asal . ' berhasil dipindah ke ' . $request->kelas_tujuan
        ]);
    }

    public function export(Request $request)
    {
        $query = Siswa::with('kelasRelation');

        if ($request->filled('kelas') && $request->kelas !== 'all') {
            $query->whereHas('kelasRelation', function($q) use ($request) {
                $q->where('nama', $request->kelas);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'aktif');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswas = $query->leftJoin('kelas', 'siswa.class_id', '=', 'kelas.id')
                        ->orderBy('kelas.nama', 'asc')
                        ->orderBy('siswa.nama_lengkap', 'asc')
                        ->select('siswa.*')
                        ->get();

        $data = [
            ['NIS', 'NISN', 'Nama Lengkap', 'Kelas', 'L/P', 'No HP', 'Alamat', 'Status']
        ];

        foreach ($siswas as $siswa) {
            $data[] = [
                $siswa->nis,
                $siswa->nisn,
                $siswa->nama_lengkap,
                $siswa->kelasRelation ? $siswa->kelasRelation->nama : '-',
                $siswa->jenis_kelamin,
                $siswa->no_hp,
                $siswa->alamat,
                $siswa->is_active ? 'Aktif' : 'Nonaktif'
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs('Data_Siswa_' . date('Ymd_His') . '.xlsx');
        exit;
    }

    public function template()
    {
        $books = [
            ['NIS', 'NISN', 'Nama Lengkap', 'Kelas', 'L/P', 'No HP', 'Alamat'],
            ['12345', '0012345678', 'Budi Santoso', '7A', 'L', '08123456789', 'Jl. Contoh Alamat No 1'],
            ['12346', '0012345679', 'Siti Aminah', '7B', 'P', '08123456790', 'Jl. Contoh Alamat No 2'],
        ];

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($books);
        $xlsx->downloadAs('Template_Import_Siswa.xlsx');
        exit;
    }

    public function import(Request $request): JsonResponse
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk peran Anda.'], 403);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
            $berhasil = 0;
            $gagal = 0;

            foreach ($xlsx->sheetNames() as $sheetIndex => $sheetName) {
                $rows = $xlsx->rows($sheetIndex);
                if (count($rows) === 0) continue;

                $waliKelasNama = null;
                $kelasNamaHeader = null;
                $headerRowIndex = 0;

                // Cari baris header dan informasi Wali Kelas
                for ($i = 0; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    $cell0 = strtolower(trim($row[0] ?? ''));
                    $cell1 = strtolower(trim($row[1] ?? ''));
                    $cell2 = strtolower(trim($row[2] ?? ''));

                    // Jika menemukan judul kolom NIS, berarti ini baris header tabel
                    if ($cell0 === 'nis' || $cell1 === 'nis' || $cell2 === 'nis') {
                        $headerRowIndex = $i;
                        break;
                    }

                    // Ekstrak info Wali Kelas jika ada di baris-baris atas
                    foreach ($row as $cell) {
                        $val = trim($cell);
                        if (empty($val)) continue;

                        if (stripos($val, 'Wali Kelas') !== false) {
                            if (preg_match('/Wali Kelas\s+([A-Za-z0-9]+)/i', $val, $matches)) {
                                $kelasNamaHeader = strtoupper($matches[1]);
                            }
                        } else if (preg_match('/(?:S\.Pd|M\.Pd|S\.Ag|S\.Kom|M\.Si|S\.E|M\.A|S\.Si|M\.M|B\.A|S\.Sos|S\.T|S\.H|B\.Ed|M\.Ed)/i', $val)) {
                            $waliKelasNama = $val;
                        }
                    }
                }

                // Jika informasi Wali Kelas ditemukan, simpan ke database
                if ($kelasNamaHeader && $waliKelasNama) {
                    \App\Models\Kelas::firstOrCreate(['nama' => $kelasNamaHeader]);
                    \App\Models\WaliKelas::updateOrCreate(
                        ['kelas_wali' => $kelasNamaHeader],
                        ['nama_lengkap' => $waliKelasNama, 'is_active' => true]
                    );
                }

                // Proses data siswa mulai dari baris setelah header
                for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                    $row = $rows[$i];

                    // Pastikan baris tidak kosong dan sesuai format
                    if (count($row) < 5 || empty(trim($row[0])) || empty(trim($row[2]))) {
                        // Jangan hitung gagal jika barisnya memang kosong semua
                        if (!empty(trim(implode('', $row)))) {
                            $gagal++;
                        }
                        continue;
                    }

                    $nis = trim($row[0]);
                    $nisn = trim($row[1]);
                    $nama = trim($row[2]);
                    $kelasNama = trim($row[3]);
                    
                    // Fallback jika kolom kelas di Excel kosong, gunakan dari header sheet
                    if (empty($kelasNama) && $kelasNamaHeader) {
                        $kelasNama = $kelasNamaHeader;
                    }

                    $jk = strtoupper(trim($row[4])) === 'P' ? 'P' : 'L';
                    $noHp = isset($row[5]) ? trim($row[5]) : null;
                    $alamat = isset($row[6]) ? trim($row[6]) : null;

                    $kelas = \App\Models\Kelas::firstOrCreate(['nama' => $kelasNama]);
                    $waliTujuan = \App\Models\WaliKelas::where('kelas_wali', $kelasNama)->first();

                    Siswa::updateOrCreate(
                        ['nis' => $nis],
                        [
                            'nisn' => $nisn,
                            'nama_lengkap' => $nama,
                            'class_id' => $kelas->id,
                            'jenis_kelamin' => $jk,
                            'tahun_ajaran' => '2025/2026',
                            'no_hp' => $noHp,
                            'alamat' => $alamat,
                            'wali_kelas_id' => $waliTujuan ? $waliTujuan->id : null,
                            'is_active' => true,
                        ]
                    );
                    $berhasil++;
                }
            }

            return response()->json([
                'message' => "Import selesai! Berhasil: $berhasil siswa, Gagal/Dilewati: $gagal baris."
            ]);
        } else {
            return response()->json(['message' => \Shuchkin\SimpleXLSX::parseError()], 400);
        }
    }

    private function formatSiswa(Siswa $siswa): array
    {
        return [
            'id' => $siswa->id,
            'nis' => $siswa->nis,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama_lengkap,
            'kelas' => $siswa->kelasRelation ? $siswa->kelasRelation->nama : '-',
            'jk' => $siswa->jenis_kelamin,
            'noHp' => $siswa->no_hp,
            'alamat' => $siswa->alamat,
            'status' => $siswa->is_active ? 'aktif' : 'nonaktif',
            'poin' => (int) ($siswa->total_poin ?? $siswa->pelanggarans()->sum('poin')),
        ];
    }
}
