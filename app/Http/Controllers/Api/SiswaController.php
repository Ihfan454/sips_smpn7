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
        $query = Siswa::withSum('pelanggarans as total_poin', 'poin');



        if ($request->filled('kelas') && $request->kelas !== 'all') {
            $query->where('kelas', $request->kelas);
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

        $siswas = $query->orderBy('nama_lengkap')->get()->map(fn (Siswa $siswa) => $this->formatSiswa($siswa));

        return response()->json(['data' => $siswas]);
    }

    public function stats(): JsonResponse
    {
        $query = Siswa::query();
        


        $siswas = $query->get();
        
        $totalKelas = \App\Models\Kelas::count();

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

    private function formatSiswa(Siswa $siswa): array
    {
        return [
            'id' => $siswa->id,
            'nis' => $siswa->nis,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama_lengkap,
            'kelas' => $siswa->kelas,
            'jk' => $siswa->jenis_kelamin,
            'noHp' => $siswa->no_hp,
            'alamat' => $siswa->alamat,
            'status' => $siswa->is_active ? 'aktif' : 'nonaktif',
            'poin' => (int) ($siswa->total_poin ?? $siswa->pelanggarans()->sum('poin')),
        ];
    }
}
