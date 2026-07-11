<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class GuruBKController extends Controller
{
    public function index()
    {
        $guru_bk = User::with('kelas')->latest()->get();
        return view('guru-bk.index', compact('guru_bk'));
    }

    public function create()
    {
        $list_kelas = \App\Models\Kelas::all();
        return view('guru-bk.create', compact('list_kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'nip' => 'nullable|string|max:20|unique:users',
            'ni_pppk' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin_bk,guru_bk',
            'class_id' => 'nullable|exists:kelas,id',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'ni_pppk' => $request->ni_pppk,
            'password' => $request->password,
            'role' => $request->role,
            'class_id' => $request->role === 'guru_bk' ? $request->class_id : null,
            'jabatan' => $request->role === 'admin_bk' ? 'Admin BK' : 'Guru BK',
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('guru-bk.index')->with('success', 'Akun pengguna berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $guru = User::findOrFail($id);
        $list_kelas = \App\Models\Kelas::all();
        return view('guru-bk.edit', compact('guru', 'list_kelas'));
    }

    public function update(Request $request, $id)
    {
        $guru = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($guru->id)],
            'nip' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($guru->id)],
            'ni_pppk' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($guru->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:admin_bk,guru_bk',
            'class_id' => 'nullable|exists:kelas,id',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'ni_pppk' => $request->ni_pppk,
            'role' => $request->role,
            'class_id' => $request->role === 'guru_bk' ? $request->class_id : null,
            'jabatan' => $request->role === 'admin_bk' ? 'Admin BK' : 'Guru BK',
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'is_active' => $request->is_active,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $guru->update($data);

        return redirect()->route('guru-bk.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $guru = User::findOrFail($id);

        // Jangan biarkan user menghapus dirinya sendiri
        if (auth()->id() === $guru->id) {
            return redirect()->route('guru-bk.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan!');
        }

        $guru->delete();

        return redirect()->route('guru-bk.index')->with('success', 'Akun pengguna berhasil dihapus!');
    }
}
