<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register | Sistem Informasi Pelanggaran Siswa - SMP Negeri 7 Jember</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom CSS -->
    @vite(['resources/css/register.css'])
</head>
<body>
    <!-- Splash Screen / Opening Animation Container -->
    <div class="splash-screen" id="splashScreen">
        <div class="splash-content">
            <div class="splash-logo-wrapper">
                <img src="{{ asset('images/logo-baru.png') }}" alt="Logo SMP Negeri 7 Jember" class="splash-logo" id="splashLogo">
            </div>
            <h2 class="splash-title">SMP Negeri 7 Jember</h2>
            <p class="splash-subtitle">Sistem Informasi Pelanggaran Siswa</p>
            <div class="splash-loader">
                <div class="loader-bar"></div>
            </div>
        </div>
    </div>

    <!-- Main Register Container (Awalnya tersembunyi) -->
    <div class="register-container" id="registerContainer">
        <!-- Left Side - School Image & Info (Akan bergerak dari tengah ke kiri) -->
        <div class="register-left" id="registerLeft">
            <div class="school-overlay"></div>
            <div class="school-content">
                <div class="school-icon">
                    <img src="{{ asset('images/logo-baru.png') }}" alt="Logo SMPN 7 Jember" class="school-logo-img">
                </div>
                <h2>SMP Negeri 7 Jember</h2>
                <p>Sistem Informasi Pelanggaran Siswa</p>
                <div class="school-motto">
                    <i class="fas fa-quote-left"></i>
                    <span>Disiplin adalah jembatan antara tujuan dan prestasi</span>
                </div>
                <div class="school-stats">
                    <div class="stat">
                        <span class="stat-number">892</span>
                        <span class="stat-label">Siswa</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">{{ $waliKelasCount ?? 0 }}</span>
                        <span class="stat-label">Wali Kelas</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">15</span>
                        <span class="stat-label">Kelas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form (Akan fade in) -->
        <div class="register-right" id="registerRight">
            <div class="register-card">
                <div class="register-header">
                    <h1>Buat Akun Baru</h1>
                    <p>Daftar untuk mengakses sistem</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="session-status">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-status">
                        <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan, periksa kembali data Anda
                    </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="input-group">
                        <div class="input-field-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="name" name="name" class="modern-input" 
                                   placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus>
                        </div>
                        @error('name')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="input-group">
                        <div class="input-field-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="modern-input" 
                                   placeholder="Alamat Email" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Kepegawaian -->
                    <div class="input-group">
                        <div class="input-field-wrapper">
                            <i class="fas fa-briefcase input-icon"></i>
                            <select id="status_pegawai" name="status_pegawai" class="modern-input" style="padding-left: 45px; width: 100%; border: none; outline: none; background: transparent; font-family: inherit; font-size: 1rem; color: #333;" onchange="togglePegawaiInput()">
                                <option value="">Pilih Status Kepegawaian (Opsional)</option>
                                <option value="pns">PNS (Pegawai Negeri Sipil)</option>
                                <option value="pppk">PPPK</option>
                            </select>
                        </div>
                    </div>

                    <!-- NIP -->
                    <div class="input-group" id="nip_group" style="display: none;">
                        <div class="input-field-wrapper">
                            <i class="fas fa-id-badge input-icon"></i>
                            <input type="text" id="nip" name="nip" class="modern-input" 
                                   placeholder="Nomor Induk Pegawai (18 Digit)" value="{{ old('nip') }}" maxlength="20">
                        </div>
                        @error('nip')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- NI PPPK -->
                    <div class="input-group" id="ni_pppk_group" style="display: none;">
                        <div class="input-field-wrapper">
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" id="ni_pppk" name="ni_pppk" class="modern-input" 
                                   placeholder="Nomor Induk PPPK (18 Digit)" value="{{ old('ni_pppk') }}" maxlength="20">
                        </div>
                        @error('ni_pppk')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <div class="input-field-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="modern-input" 
                                   placeholder="Password" required>
                            <button type="button" class="toggle-password" data-target="password">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="input-group">
                        <div class="input-field-wrapper">
                            <i class="fas fa-check-circle input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="modern-input" 
                                   placeholder="Konfirmasi Password" required>
                            <button type="button" class="toggle-password" data-target="password_confirmation">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" class="password-match"></div>
                        @error('password_confirmation')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Register Button -->
                    <button type="submit" class="register-btn" id="registerBtn">
                        <span>Daftar</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <!-- Login Link -->
                    <div class="login-prompt">
                        <p>Sudah punya akun? 
                            <a href="{{ route('login') }}" class="login-link">Login disini</a>
                        </p>
                    </div>

                    <div class="secure-note">
                        <i class="fas fa-shield-alt"></i> Sistem aman & terintegrasi dengan BK
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom JavaScript -->
    @vite(['resources/js/register.js'])
    <script>
        function togglePegawaiInput() {
            const status = document.getElementById('status_pegawai').value;
            const nipGroup = document.getElementById('nip_group');
            const pppkGroup = document.getElementById('ni_pppk_group');
            const nipInput = document.getElementById('nip');
            const pppkInput = document.getElementById('ni_pppk');
            
            if (status === 'pns') {
                nipGroup.style.display = 'block';
                pppkGroup.style.display = 'none';
                pppkInput.value = '';
            } else if (status === 'pppk') {
                nipGroup.style.display = 'none';
                pppkGroup.style.display = 'block';
                nipInput.value = '';
            } else {
                nipGroup.style.display = 'none';
                pppkGroup.style.display = 'none';
                nipInput.value = '';
                pppkInput.value = '';
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            // Restore state if there are validation errors
            if (document.getElementById('nip').value) {
                document.getElementById('status_pegawai').value = 'pns';
            } else if (document.getElementById('ni_pppk').value) {
                document.getElementById('status_pegawai').value = 'pppk';
            }
            togglePegawaiInput();
        });
    </script>
</body>
</html>