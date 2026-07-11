<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PengaturanController extends Controller
{
    private function getSettingsPath()
    {
        // Pastikan folder storage/app ada
        $dir = storage_path('app');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return storage_path('app/settings.json');
    }

    private function getSchoolSettings()
    {
        $path = $this->getSettingsPath();
        
        if (!File::exists($path)) {
            $default = [
                'nama_sekolah' => 'SMP Negeri 7 Jember',
                'alamat_sekolah' => 'Jl. PB Sudirman No. 24, Patrang, Jember, Jawa Timur',
                'email_sekolah' => 'smpn7jember@sch.id',
                'telepon_sekolah' => '(0331) 487007',
                'akreditasi' => 'A',
                'tahun_ajaran' => '2025/2026'
            ];
            File::put($path, json_encode($default, JSON_PRETTY_PRINT));
            return $default;
        }

        return json_decode(File::get($path), true);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $sekolah = $this->getSchoolSettings();
        $active_tab = $request->query('tab', 'profile');

        return view('pengaturan', compact('user', 'sekolah', 'active_tab'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nip' => 'nullable|string|max:20|unique:users,nip,' . $user->id,
            'ni_pppk' => 'nullable|string|max:20|unique:users,ni_pppk,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'nip', 'ni_pppk', 'no_hp', 'alamat']);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke public/uploads/profile
            $destinationPath = public_path('uploads/profile');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            // Hapus foto lama jika ada
            if ($user->foto && File::exists(public_path($user->foto))) {
                File::delete(public_path($user->foto));
            }

            $file->move($destinationPath, $filename);
            $data['foto'] = 'uploads/profile/' . $filename;
        }

        $user->update($data);

        return redirect()->route('pengaturan', ['tab' => 'profile'])->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pengaturan', ['tab' => 'password'])->with('success', 'Password Anda berhasil diubah!');
    }

    public function updateAplikasi(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat_sekolah' => 'required|string',
            'email_sekolah' => 'required|email',
            'telepon_sekolah' => 'required|string',
            'akreditasi' => 'required|string|max:5',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        $settings = $request->only([
            'nama_sekolah',
            'alamat_sekolah',
            'email_sekolah',
            'telepon_sekolah',
            'akreditasi',
            'tahun_ajaran'
        ]);

        File::put($this->getSettingsPath(), json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('pengaturan', ['tab' => 'aplikasi'])->with('success', 'Pengaturan instansi sekolah berhasil diperbarui!');
    }
}
