<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    // Menampilkan daftar pelanggaran ke tabel
    public function index() 
    {
        $query = Pelanggaran::with('siswa');



        $data_pelanggaran = $query->latest()->get();
        return view('pelanggaran', compact('data_pelanggaran'));
    }

    // Menampilkan form create
    public function create() 
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencatat pelanggaran.');
        }

        $data_siswa = Siswa::all();
        return view('pelanggaran-create', compact('data_siswa'));
    }

    // Menyimpan data ke database
    public function store(Request $request) 
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencatat pelanggaran.');
        }

        // Validasi dasar
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pelanggaran' => 'required|string',
            'kategori' => 'required|in:ringan,sedang,berat',
            'poin' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        // Menggabungkan data dari form ke kolom database
        $data = [
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'kategori' => $request->kategori,
            'poin' => $request->poin,
            'tanggal_pelanggaran' => $request->tanggal,
            'deskripsi' => $request->catatan,
            'user_id' => auth()->id() ?? 1, // Menggunakan user yang sedang login, fallback ke 1 jika null
            'status' => 'proses', // lowercase sesuai enum database
        ];

        Pelanggaran::create($data);

        return redirect()->route('pelanggaran')->with('success', 'Data pelanggaran berhasil dicatat!');
    }
    public function edit($id)
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pelanggaran.');
        }

        $pelanggaran = Pelanggaran::findOrFail($id);
        $data_siswa = \App\Models\Siswa::orderBy('nama_lengkap', 'asc')->get();
        return view('pelanggaran-edit', compact('pelanggaran', 'data_siswa'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pelanggaran.');
        }

        $pelanggaran = Pelanggaran::findOrFail($id);

        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pelanggaran' => 'required|string',
            'kategori' => 'required|in:ringan,sedang,berat',
            'poin' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $pelanggaran->update([
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'kategori' => $request->kategori,
            'poin' => $request->poin,
            'tanggal_pelanggaran' => $request->tanggal,
            'deskripsi' => $request->catatan,
        ]);

        return redirect()->route('pelanggaran')->with('success', 'Data pelanggaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdminBK() && !auth()->user()->isGuruBK()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus pelanggaran.');
        }

        $pelanggaran = Pelanggaran::findOrFail($id);
        $pelanggaran->delete();

        return redirect()->route('pelanggaran')->with('success', 'Data pelanggaran berhasil dihapus!');
    }
}