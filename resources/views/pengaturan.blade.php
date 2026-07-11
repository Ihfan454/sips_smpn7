<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Sistem | Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Cropper.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    
    @vite(['resources/css/dashboard.css', 'resources/js/pengaturan.js'])
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
                    <h2>Pengaturan Sistem</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                </div>
            </header>

            <div class="settings-container">
                <!-- Navigation Tabs (Left Sidebar) -->
                <div class="settings-sidebar">
                    <button class="tab-btn active" data-tab="profile">
                        <i class="fas fa-id-badge"></i>
                        <span>Profil Saya</span>
                    </button>
                    <button class="tab-btn" data-tab="password">
                        <i class="fas fa-key"></i>
                        <span>Keamanan / Sandi</span>
                    </button>
                    @if(auth()->user()->isAdminBK())
                    <button class="tab-btn" data-tab="aplikasi">
                        <i class="fas fa-school"></i>
                        <span>Instansi Sekolah</span>
                    </button>
                    @endif
                </div>

                <!-- Settings Formss (Right Content) -->
                <div class="settings-content">
                    @if(session('success'))
                        <div class="alert alert-success" id="alertSuccess" style="transition: opacity 0.5s ease;">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                        <script>
                            setTimeout(() => {
                                const alert = document.getElementById('alertSuccess');
                                if (alert) {
                                    alert.style.opacity = '0';
                                    setTimeout(() => alert.remove(), 500);
                                }
                            }, 3000);
                        </script>
                    @endif

                    <!-- Tab 1: Profil Saya -->
                    <div class="tab-panel active" id="tab-profile">
                        <h3 class="page-heading"><i class="fas fa-user-pen"></i> Edit Profil Saya</h3>
                        <p class="page-subtitle">Ubah data informasi pribadi dan foto profil Anda di sini.</p>

                        <form action="{{ route('pengaturan.profile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="profile-upload-area">
                                <div class="profile-preview" id="profilePreview">
                                    @if($user->foto && file_exists(public_path($user->foto)))
                                        <img src="{{ asset($user->foto) }}" alt="Foto Profil">
                                    @else
                                        {{ substr($user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div style="display:flex; flex-direction:column; gap: 8px;">
                                    <div style="display:flex; gap:10px; align-items:center;">
                                        <label for="foto" class="upload-label">Unggah Foto Baru</label>
                                        @if($user->foto && file_exists(public_path($user->foto)))
                                            <button type="button" class="btn btn-danger btn-sm" id="btnHapusFoto" style="padding: 6px 12px; font-size: 13px; margin:0;">Hapus Foto</button>
                                        @endif
                                    </div>
                                    <input type="file" id="foto" name="foto_upload" accept="image/*" style="font-size: 13px;">
                                    <input type="hidden" name="foto_base64" id="foto_base64">
                                    <input type="hidden" name="hapus_foto" id="hapus_foto" value="0">
                                    <small class="text-muted">Rekomendasi ukuran square/kotak, Maksimal 2MB (JPG, PNG, SVG)</small>
                                    @error('foto') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Alamat Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nip">NIP <small class="text-muted">(PNS - Opsional)</small></label>
                                        <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip', $user->nip) }}" placeholder="18 digit NIP" maxlength="20">
                                        @error('nip') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="ni_pppk">NI PPPK <small class="text-muted">(Opsional)</small></label>
                                        <input type="text" class="form-control" id="ni_pppk" name="ni_pppk" value="{{ old('ni_pppk', $user->ni_pppk) }}" placeholder="18 digit NI PPPK" maxlength="20">
                                        @error('ni_pppk') <span class="text-danger">{{ $user->ni_pppk }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_hp">No. Handphone</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Masukkan nomor HP">
                                        @error('no_hp') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap rumah">{{ old('alamat', $user->alamat) }}</textarea>
                                @error('alamat') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn-save">
                                <i class="fas fa-floppy-disk"></i> Simpan Profil
                            </button>
                        </form>
                    </div>

                    <!-- Tab 2: Sandi / Keamanan -->
                    <div class="tab-panel" id="tab-password">
                        <h3 class="page-heading"><i class="fas fa-key"></i> Ganti Password</h3>
                        <p class="page-subtitle">Demi keamanan akun, silakan ganti password Anda secara berkala.</p>

                        <form action="{{ route('pengaturan.password') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="current_password">Password Sekarang</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Masukkan sandi saat ini" required>
                                @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password Baru</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required>
                                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi sandi baru" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-save">
                                <i class="fas fa-key"></i> Perbarui Sandi
                            </button>
                        </form>
                    </div>

                    @if(auth()->user()->isAdminBK())
                    <!-- Tab 3: Pengaturan Instansi Sekolah -->
                    <div class="tab-panel" id="tab-aplikasi">
                        <h3 class="page-heading"><i class="fas fa-school"></i> Identitas Sekolah / Instansi</h3>
                        <p class="page-subtitle">Sesuaikan identitas sekolah yang akan tampil di kop surat cetak laporan pelanggaran.</p>

                        <form action="{{ route('pengaturan.aplikasi') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="nama_sekolah">Nama Sekolah</label>
                                <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah', $sekolah['nama_sekolah'] ?? '') }}" required>
                                @error('nama_sekolah') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="alamat_sekolah">Alamat Sekolah</label>
                                <textarea class="form-control" id="alamat_sekolah" name="alamat_sekolah" rows="2" required>{{ old('alamat_sekolah', $sekolah['alamat_sekolah'] ?? '') }}</textarea>
                                @error('alamat_sekolah') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email_sekolah">Alamat Email Sekolah</label>
                                        <input type="email" class="form-control" id="email_sekolah" name="email_sekolah" value="{{ old('email_sekolah', $sekolah['email_sekolah'] ?? '') }}" required>
                                        @error('email_sekolah') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telepon_sekolah">No. Telepon Sekolah</label>
                                        <input type="text" class="form-control" id="telepon_sekolah" name="telepon_sekolah" value="{{ old('telepon_sekolah', $sekolah['telepon_sekolah'] ?? '') }}" required>
                                        @error('telepon_sekolah') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="akreditasi">Akreditasi</label>
                                        <input type="text" class="form-control" id="akreditasi" name="akreditasi" value="{{ old('akreditasi', $sekolah['akreditasi'] ?? '') }}" placeholder="Contoh: A" required>
                                        @error('akreditasi') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun_ajaran">Tahun Ajaran Aktif</label>
                                        <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran', $sekolah['tahun_ajaran'] ?? '') }}" placeholder="Contoh: 2025/2026" required>
                                        @error('tahun_ajaran') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-save">
                                <i class="fas fa-floppy-disk"></i> Simpan Instansi
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Cropper Modal -->
    <div id="cropModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.8); align-items:center; justify-content:center;">
        <div style="background:white; padding:20px; border-radius:12px; width:90%; max-width:600px;">
            <h3 style="margin-top:0;">Sesuaikan Foto Profil</h3>
            <div style="width:100%; max-height:400px; margin-bottom:20px;">
                <img id="cropImage" src="" style="max-width:100%;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary" id="btnCancelCrop">Batal</button>
                <button type="button" class="btn btn-primary" id="btnApplyCrop">Potong & Terapkan</button>
            </div>
        </div>
    </div>

    <!-- Cropper.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tab-btn');
            const panels = document.querySelectorAll('.tab-panel');

            // Handle tab switching
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetTab = tab.getAttribute('data-tab');

                    // Set active button
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    // Set active panel
                    panels.forEach(p => {
                        p.classList.remove('active');
                        if (p.id === `tab-${targetTab}`) {
                            p.classList.add('active');
                        }
                    });

                    // Add query parameter to URL for persistence on redirect
                    const url = new URL(window.location);
                    url.searchParams.set('tab', targetTab);
                    window.history.pushState({}, '', url);
                });
            });

            // Set initial active tab based on query param
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = urlParams.get('tab') || '{{ $active_tab }}';
            const targetBtn = document.querySelector(`.tab-btn[data-tab="${initialTab}"]`);
            if (targetBtn) {
                targetBtn.click();
            }

            // Cropper handler
            const fotoInput = document.getElementById('foto');
            const previewDiv = document.getElementById('profilePreview');
            const fotoBase64Input = document.getElementById('foto_base64');
            
            // Cropper variables
            let cropper = null;
            const cropModal = document.getElementById('cropModal');
            const cropImage = document.getElementById('cropImage');
            const btnCancelCrop = document.getElementById('btnCancelCrop');
            const btnApplyCrop = document.getElementById('btnApplyCrop');

            fotoInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    // Jika ada gambar yang dipilih, batalkan hapus foto
                    document.getElementById('hapus_foto').value = '0';
                    
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        // Set image src to cropper
                        cropImage.src = event.target.result;
                        // Show modal
                        cropModal.style.display = 'flex';
                        
                        // Initialize Cropper
                        if (cropper) {
                            cropper.destroy();
                        }
                        cropper = new Cropper(cropImage, {
                            aspectRatio: 1, // Square crop
                            viewMode: 1,
                            minCropBoxWidth: 100,
                            minCropBoxHeight: 100,
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Cancel Crop
            btnCancelCrop.addEventListener('click', () => {
                cropModal.style.display = 'none';
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                fotoInput.value = ''; // Reset input file
            });

            // Apply Crop
            btnApplyCrop.addEventListener('click', () => {
                if (cropper) {
                    // Get cropped canvas
                    const canvas = cropper.getCroppedCanvas({
                        width: 400,
                        height: 400,
                    });
                    
                    // Convert to base64
                    const base64Data = canvas.toDataURL('image/jpeg', 0.9);
                    
                    // Set to hidden input
                    fotoBase64Input.value = base64Data;
                    
                    // Update preview
                    previewDiv.innerHTML = `<img src="${base64Data}" alt="Preview Foto">`;
                    
                    // Close modal
                    cropModal.style.display = 'none';
                    cropper.destroy();
                    cropper = null;
                }
            });

            // Delete Photo
            const btnHapusFoto = document.getElementById('btnHapusFoto');
            const hapusFotoInput = document.getElementById('hapus_foto');
            if(btnHapusFoto) {
                btnHapusFoto.addEventListener('click', () => {
                    hapusFotoInput.value = '1';
                    // Reset inputs
                    fotoInput.value = '';
                    fotoBase64Input.value = '';
                    
                    // Reset preview to initials
                    const name = document.getElementById('name').value;
                    const initial = name ? name.substring(0, 1).toUpperCase() : 'U';
                    previewDiv.innerHTML = initial;
                    
                    // Hide the button since it's removed
                    btnHapusFoto.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
