<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $siswaCount = Siswa::count();
        $waliKelasCount = \App\Models\WaliKelas::count();
        $kelasCount = Kelas::count();

        return view('auth.register', compact('siswaCount', 'waliKelasCount', 'kelasCount'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nip' => ['nullable', 'string', 'max:20', 'unique:'.User::class], 
            'ni_pppk' => ['nullable', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'ni_pppk' => $request->ni_pppk,
            'password' => $request->password,
        ]);

        event(new Registered($user));

        // ==========================================
        // HAPUS / COMMENT baris Auth::login($user) ini
        // ==========================================
        // Auth::login($user);

        // ==========================================
        // REDIRECT KE HALAMAN LOGIN dengan pesan sukses
        // ==========================================
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }
}