<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\GuruBKController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\PengaturanController;
use Illuminate\Support\Facades\Route;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Kelas;

Route::get('/', function () {
    $totalSiswa = Siswa::count();
    $totalPelanggaran = Pelanggaran::count();
    
    // Tren Pelanggaran: jumlah pelanggaran bulan ini
    $trenPelanggaran = Pelanggaran::whereMonth('tanggal_pelanggaran', now()->month)
        ->whereYear('tanggal_pelanggaran', now()->year)
        ->count();

    // Kelas Tertib: jumlah kelas yang tidak memiliki pelanggaran di bulan ini
    $kelasTertib = Kelas::whereDoesntHave('siswas.pelanggarans', function ($q) {
        $q->whereMonth('tanggal_pelanggaran', now()->month)
          ->whereYear('tanggal_pelanggaran', now()->year);
    })->count();

    return view('welcome', compact('totalSiswa', 'totalPelanggaran', 'trenPelanggaran', 'kelasTertib'));
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Semua rute di dalam grup ini WAJIB LOGIN (Aman dari akses luar)
Route::middleware('auth')->group(function () {

    // ------------------------------------------
    // RUTE DATA SISWA (Halaman utama open untuk semua, data dibatasi via API)
    // ------------------------------------------
    Route::get('/data-siswa', function () {
        return view('data-siswa');
    })->name('data-siswa');

    // ------------------------------------------
    // RUTE PELANGGARAN
    // ------------------------------------------
    
    // 1. Menampilkan daftar pelanggaran (Open untuk semua)
    Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran');

    // CRUD Pelanggaran hanya untuk Admin BK dan Guru BK
    Route::middleware('role:admin_bk,guru_bk')->group(function () {
        // 2. Menampilkan form tambah pelanggaran baru
        Route::get('/pelanggaran/create', [PelanggaranController::class, 'create'])->name('pelanggaran.create');

        // 3. Menampilkan form edit pelanggaran
        Route::get('/pelanggaran/{id}/edit', [PelanggaranController::class, 'edit'])->name('pelanggaran.edit');

        // 3.5. Memproses update data pelanggaran
        Route::put('/pelanggaran/{id}', [PelanggaranController::class, 'update'])->name('pelanggaran.update');

        // 4. Proses hapus pelanggaran
        Route::delete('/pelanggaran/{id}', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');

        // 5. Proses menyimpan data pelanggaran baru (Pintu Penerima POST)
        Route::post('/pelanggaran', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
    });

    // 6. Halaman Laporan Pelanggaran (Semua role bisa lihat, Wali Kelas ter-scope)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

    Route::get('/test-hash', function() {
        $u = new \App\Models\User();
        $rawHash = \Illuminate\Support\Facades\Hash::make('password123');
        $u->password = $rawHash; // Assign it to trigger the cast
        return response()->json([
            'raw_hash' => $rawHash,
            'model_hash' => $u->password,
            'is_double_hashed' => $rawHash !== $u->password
        ]);
    });

    // 7. Manajemen Guru / Pengguna (Hanya Admin BK)
    Route::middleware('role:admin_bk')->group(function () {
        Route::resource('guru-bk', GuruBKController::class);
    });

    // 7.5 Manajemen Wali Kelas (Admin BK & Guru BK)
    Route::middleware('role:admin_bk,guru_bk')->group(function () {
        Route::resource('wali-kelas', WaliKelasController::class);
    });

    // 8. Pengaturan Lengkap
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan/profile', [PengaturanController::class, 'updateProfile'])->name('pengaturan.profile');
    Route::post('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.password');
    
    // Hanya Admin yang bisa mengubah pengaturan aplikasi / instansi
    Route::post('/pengaturan/aplikasi', [PengaturanController::class, 'updateAplikasi'])
        ->middleware('role:admin_bk')
        ->name('pengaturan.aplikasi');

    // ------------------------------------------
    // RUTE PROFILE (Bawaan Laravel Breeze)
    // ------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ------------------------------------------
    // RUTE API (Scoping internal)
    // ------------------------------------------
    Route::prefix('api')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/recent', [DashboardController::class, 'recent']);
        Route::get('/dashboard/charts', [DashboardController::class, 'charts']);
        Route::get('/notifications/today', [DashboardController::class, 'notificationsToday']);
        
        Route::get('/siswa/stats', [SiswaController::class, 'stats']);
        Route::get('/siswa/kelas', [SiswaController::class, 'kelas']);
        Route::get('/siswa', [SiswaController::class, 'index']);

        // Write API only for Admin BK and Guru BK
        Route::middleware('role:admin_bk,guru_bk')->group(function () {
            Route::post('/siswa', [SiswaController::class, 'store']);
            Route::put('/siswa/{siswa}', [SiswaController::class, 'update']);
            Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy']);
        });
    });
});

require __DIR__.'/auth.php';