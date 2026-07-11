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
                    <h2>Edit Guru BK</h2>
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
                    <h3 class="page-heading"><i class="fas fa-user-edit"></i> Formulir Edit Akun Guru BK</h3>
                    <p class="page-subtitle" style="margin-bottom: 25px;">Silakan perbarui data akun Guru BK atau administrator di bawah ini.</p>
                </div>

                <form action="{{ route('guru-bk.update', $guru->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama Lengkap <span class="text-danger" style="display:inline;">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $guru->name) }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Alamat Email <span class="text-danger" style="display:inline;">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $guru->email) }}" required>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip">NIP <small class="text-muted">(PNS - Opsional)</small></label>
                                <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip', $guru->nip) }}" placeholder="Masukkan 18 digit NIP" maxlength="20">
                                @error('nip') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ni_pppk">NI PPPK <small class="text-muted">(Opsional)</small></label>
                                <input type="text" class="form-control" id="ni_pppk" name="ni_pppk" value="{{ old('ni_pppk', $guru->ni_pppk) }}" placeholder="Masukkan 18 digit NI PPPK" maxlength="20">
                                @error('ni_pppk') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Peran / Hak Akses <span class="text-danger" style="display:inline;">*</span></label>
                                <select class="form-control" id="role" name="role" required onchange="toggleKelasField()">
                                    <option value="admin_bk" {{ old('role', $guru->role) == 'admin_bk' ? 'selected' : '' }}>Admin BK (Administrator)</option>
                                    <option value="guru_bk" {{ old('role', $guru->role) == 'guru_bk' ? 'selected' : '' }}>Guru BK</option>

                                </select>
                                @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" id="kelasField" style="display: none;">
                            <div class="form-group">
                                <label for="class_id">Kelas Diampu <span class="text-danger" style="display:inline;">*</span></label>
                                <select class="form-control" id="class_id" name="class_id">
                                    <option value="">Pilih Kelas</option>
                                    @foreach($list_kelas as $kelas)
                                        <option value="{{ $kelas->id }}" {{ old('class_id', $guru->class_id) == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                                    @endforeach
                                </select>
                                @error('class_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="password-box">
                        <h4><i class="fas fa-key"></i> Ubah Password <small class="text-muted" style="font-weight: normal;">(Kosongkan jika tidak ingin diubah)</small></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="password">Password Baru</label>
                                    <div style="position: relative;">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter">
                                        <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                                    <div style="position: relative;">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                                        <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_hp">No. Handphone</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}">
                                @error('no_hp') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="is_active">Status Akun <span class="text-danger" style="display:inline;">*</span></label>
                                <select class="form-control" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', $guru->is_active) == '1' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                                    <option value="0" {{ old('is_active', $guru->is_active) == '0' ? 'selected' : '' }}>Nonaktif (Ditangguhkan)</option>
                                </select>
                                @error('is_active') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $guru->alamat) }}</textarea>
                        @error('alamat') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('guru-bk.index') }}" class="btn btn-secondary">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelasField = document.getElementById('kelasField');
            const roleSelect = document.getElementById('role');

            function toggleKelasField() {
                if (roleSelect.value === 'guru_bk') {
                    kelasField.style.display = 'block';
                    document.getElementById('class_id').required = true;
                } else {
                    kelasField.style.display = 'none';
                    document.getElementById('class_id').required = false;
                    document.getElementById('class_id').value = '';
                }
            }

            roleSelect.addEventListener('change', toggleKelasField);
            // Jalankan saat load untuk mengecek nilai lama
            toggleKelasField();

            // Toggle Password Visibility
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
        });
    </script>
</body>
</html>
