<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Guru BK | Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    @vite(['resources/css/dashboard.css', 'resources/js/guru-bk.js'])
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="dashboard-wrapper">
        <x-admin-sidebar />

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>Edit Data Wali Kelas</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                </div>
            </header>

            <div class="form-section">
                <div style="margin-bottom: 25px;">
                    <h3 class="page-heading"><i class="fas fa-user-edit"></i> Formulir Pembaruan Data</h3>
                    <p class="page-subtitle" style="margin-bottom: 25px;">Silakan perbarui data wali kelas di bawah ini.</p>
                </div>

                <form action="{{ route('wali-kelas.update', $wali->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap <span class="text-danger" style="display:inline;">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $wali->nama_lengkap) }}" placeholder="Contoh: Budi Santoso, S.Pd." required>
                                @error('nama_lengkap') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kelas_wali">Kelas Diampu <span class="text-danger" style="display:inline;">*</span></label>
                                <select class="form-control" id="kelas_wali" name="kelas_wali" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($list_kelas as $kelas)
                                        <option value="{{ $kelas->nama }}" {{ old('kelas_wali', $wali->kelas_wali) == $kelas->nama ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                                    @endforeach
                                </select>
                                @error('kelas_wali') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip">NIP <small class="text-muted">(PNS - Opsional)</small></label>
                                <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip', $wali->nip) }}" placeholder="Masukkan 18 digit NIP" maxlength="20">
                                @error('nip') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ni_pppk">NI PPPK <small class="text-muted">(Opsional)</small></label>
                                <input type="text" class="form-control" id="ni_pppk" name="ni_pppk" value="{{ old('ni_pppk', $wali->ni_pppk) }}" placeholder="Masukkan 18 digit NI PPPK" maxlength="20">
                                @error('ni_pppk') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_hp">No. Handphone</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $wali->no_hp) }}" placeholder="Contoh: 081234567890">
                                @error('no_hp') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="is_active">Status Aktif <span class="text-danger" style="display:inline;">*</span></label>
                                <select class="form-control" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', $wali->is_active) == '1' ? 'selected' : '' }}>Aktif (Tersedia untuk Siswa)</option>
                                    <option value="0" {{ old('is_active', $wali->is_active) == '0' ? 'selected' : '' }}>Nonaktif / Cuti</option>
                                </select>
                                @error('is_active') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="alamat">Alamat Tinggal</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap wali kelas">{{ old('alamat', $wali->alamat) }}</textarea>
                                @error('alamat') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px;">
                        <a href="{{ route('wali-kelas.index') }}" class="btn btn-secondary" style="text-decoration: none;">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
