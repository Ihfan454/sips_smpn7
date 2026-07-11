<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password | Sistem Informasi Pelanggaran Siswa - SMP Negeri 7 Jember</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom CSS (Reusing Login CSS) -->
    @vite(['resources/css/login.css'])

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
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

    <!-- Main Container -->
    <div class="login-container" id="loginContainer">
        <!-- Left Side - School Image & Info -->
        <div class="login-left" id="loginLeft">
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
            </div>
        </div>

        <!-- Right Side - Forgot Password Form -->
        <div class="login-right" id="loginRight">
            <div class="login-card">
                <div class="login-header">
                    <h1>Lupa Password?</h1>
                    <p style="font-size: 0.9rem; line-height: 1.5;">Tidak masalah. Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password Anda.</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="session-status">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                <!-- Forgot Password Form -->
                <form method="POST" action="{{ route('password.email') }}" id="loginForm">
                    @csrf

                    <!-- Email Field -->
                    <div class="input-group">
                        <div class="input-field-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="modern-input" 
                                   placeholder="Email Anda" value="{{ old('email') }}" required autofocus>
                        </div>
                        @error('email')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="options-row" style="margin-top: -10px;">
                        <a href="{{ route('login') }}" class="forgot-link">
                            <i class="fas fa-arrow-left"></i> Kembali ke Login
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="login-btn">
                        <span>Kirim Tautan Reset</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>

                    <div class="secure-note">
                        <i class="fas fa-shield-alt"></i> Sistem aman & terintegrasi dengan BK
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom JavaScript -->
    @vite(['resources/js/login.js'])
</body>
</html>
