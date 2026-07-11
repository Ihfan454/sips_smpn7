<?php

namespace App\Http\Controllers;

use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WaliKelasController extends Controller
{
    public function index()
    {
        $wali_kelas = WaliKelas::latest()->get();
        return view('wali-kelas.index', compact('wali_kelas'));
    }

    public function create()
    {
        $list_kelas = \App\Models\Kelas::all();
        return view('wali-kelas.create', compact('list_kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20|unique:wali_kelas,nip',
            'ni_pppk' => 'nullable|string|max:20|unique:wali_kelas,ni_pppk',
            'kelas_wali' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        WaliKelas::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nip' => $request->nip,
            'ni_pppk' => $request->ni_pppk,
            'kelas_wali' => $request->kelas_wali,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('wali-kelas.index')->with('success', 'Data Wali Kelas berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $wali = WaliKelas::findOrFail($id);
        $list_kelas = \App\Models\Kelas::all();
        return view('wali-kelas.edit', compact('wali', 'list_kelas'));
    }

    public function update(Request $request, $id)
    {
        $wali = WaliKelas::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => ['nullable', 'string', 'max:20', Rule::unique('wali_kelas')->ignore($wali->id)],
            'ni_pppk' => ['nullable', 'string', 'max:20', Rule::unique('wali_kelas')->ignore($wali->id)],
            'kelas_wali' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $wali->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nip' => $request->nip,
            'ni_pppk' => $request->ni_pppk,
            'kelas_wali' => $request->kelas_wali,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('wali-kelas.index')->with('success', 'Data Wali Kelas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $wali = WaliKelas::findOrFail($id);
        
        // Memeriksa jika wali kelas ini masih terhubung dengan data siswa
        if ($wali->siswas()->count() > 0) {
            return redirect()->route('wali-kelas.index')->with('error', 'Tidak dapat menghapus Wali Kelas karena masih terhubung dengan data Siswa.');
        }

        $wali->delete();

        return redirect()->route('wali-kelas.index')->with('success', 'Data Wali Kelas berhasil dihapus!');
    }
}
