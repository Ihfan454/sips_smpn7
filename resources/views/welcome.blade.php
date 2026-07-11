<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Informasi Pelanggaran Siswa | SMP Negeri 7 Jember</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom CSS -->
    @vite(['resources/css/welcome.css'])

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <!-- Background Blobs -->
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

<!-- Header -->
<header class="header" id="mainHeader">
    <nav class="nav-container">
        <a href="{{ url('/') }}" class="logo">
            <div class="logo-icon">
                <img src="{{ asset('images/logo-baru.png') }}" alt="Logo SMP Negeri 7 Jember" class="logo-image">
            </div>
            <div class="logo-text">
                <span>SMP Negeri 7 Jember</span>
                <small>Sistem Informasi Pelanggaran Siswa</small>
            </div>
        </a>
        <div class="nav-buttons">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-dashboard">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>

                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
            @endauth
        </div>
    </nav>
</header>

    <!-- Main Content -->
    <main class="main-container">
        <!-- Hero Section -->
        <div class="hero-grid">
            <!-- Left Content -->
            <div class="animate-slide-up">
                <div class="badge"><i class="fas fa-gavel"></i> <span>Tata Tertib & Disiplin Siswa</span></div>
                <h1 class="hero-title">
                    <span class="text-gradient">Sistem Informasi</span><br>Pelanggaran Siswa
                </h1>
                <p class="hero-description">
                    Mewujudkan generasi disiplin, berkarakter, dan berprestasi melalui pemantauan 
                    pelanggaran siswa secara terintegrasi di lingkungan SMP Negeri 7 Jember.
                </p>
                <div class="hero-buttons">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">Dashboard <i class="fas fa-arrow-right"></i></a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Akses Sistem <i class="fas fa-arrow-right"></i></a>
                    @endauth
                    <a href="#statistics" class="btn-secondary"><i class="fas fa-chart-line"></i> Lihat Statistik</a>
                </div>
                <div class="flex-center gap-4">
                    <span class="text-sm"><i class="fas fa-check-circle text-green-500"></i> Terintegrasi dengan BK</span>
                </div>
            </div>

            <!-- Right Side: Animated Slider -->
            <div class="relative animate-scale-in delay-100">
                <div class="slider-container">
                    <div class="slider-decorations">
                        <div class="slider-decoration-1"></div>
                        <div class="slider-decoration-2"></div>
                    </div>
                    
                    <!-- Hero Slider -->
                    <div class="hero-slider">
                        <!-- Slide 1 -->
                        <div class="hero-slide active" data-slide="0">
                            <div class="slide-content">
                                <div class="slide-image-wrapper"><i class="fas fa-chalkboard-user slide-icon"></i></div>
                                <div class="slide-caption"><h4>Kegiatan Belajar Mengajar</h4><p>Pembelajaran interaktif & menyenangkan</p></div>
                            </div>
                        </div>
                        <!-- Slide 2 -->
                        <div class="hero-slide" data-slide="1">
                            <div class="slide-content">
                                <div class="slide-image-wrapper"><i class="fas fa-trophy slide-icon"></i></div>
                                <div class="slide-caption"><h4>Prestasi Gemilang</h4><p>Juara Olimpiade Sains & Olahraga</p></div>
                            </div>
                        </div>
                        <!-- Slide 3 -->
                        <div class="hero-slide" data-slide="2">
                            <div class="slide-content">
                                <div class="slide-image-wrapper"><i class="fas fa-futbol slide-icon"></i></div>
                                <div class="slide-caption"><h4>Ekstrakurikuler Aktif</h4><p>Paskibra, Pramuka, PMR, Olahraga</p></div>
                            </div>
                        </div>
                        <!-- Slide 4 -->
                        <div class="hero-slide" data-slide="3">
                            <div class="slide-content">
                                <div class="slide-image-wrapper"><i class="fas fa-laptop-code slide-icon"></i></div>
                                <div class="slide-caption"><h4>Fasilitas Modern</h4><p>Lab Komputer, Perpustakaan digital</p></div>
                            </div>
                        </div>
                        <!-- Slide 5 -->
                        <div class="hero-slide" data-slide="4">
                            <div class="slide-content">
                                <div class="slide-image-wrapper"><i class="fas fa-hand-peace slide-icon"></i></div>
                                <div class="slide-caption"><h4>Berkarakter & Berakhlak</h4><p>Sopan, disiplin, religius</p></div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="slider-dots">
                            <button class="slider-dot active" data-slide="0"></button>
                            <button class="slider-dot" data-slide="1"></button>
                            <button class="slider-dot" data-slide="2"></button>
                            <button class="slider-dot" data-slide="3"></button>
                            <button class="slider-dot" data-slide="4"></button>
                        </div>
                        <button class="slider-nav prev"><i class="fas fa-chevron-left"></i></button>
                        <button class="slider-nav next"><i class="fas fa-chevron-right"></i></button>
                    </div>

                    <!-- Badge Group -->
                    <div class="badge-group">
                        <div class="badge-item"><i class="fas fa-check-circle text-green-500"></i> Tertib</div>
                        <div class="badge-item"><i class="fas fa-star text-yellow-500"></i> Berprestasi</div>
                        <div class="badge-item"><i class="fas fa-heart text-red-500"></i> Berkarakter</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div id="statistics" class="stats-grid">
            <div class="stat-card" data-target="{{ $totalSiswa }}">
                <i class="fas fa-users"></i>
                <div class="stat-number">0</div>
                <p>Total Siswa</p>
            </div>
            <div class="stat-card" data-target="{{ $totalPelanggaran }}">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="stat-number">0</div>
                <p>Total Pelanggaran</p>
            </div>
            <div class="stat-card" data-target="{{ $trenPelanggaran }}">
                <i class="fas fa-chart-line"></i>
                <div class="stat-number">0</div>
                <p>Tren Pelanggaran ↓</p>
            </div>
            <div class="stat-card" data-target="{{ $kelasTertib }}">
                <i class="fas fa-trophy"></i>
                <div class="stat-number">0</div>
                <p>Kelas Tertib</p>
            </div>
        </div>

        <!-- Klasifikasi Pelanggaran -->
        <div class="violations-section">
            <div class="section-header">
                <h2>Klasifikasi Pelanggaran</h2>
                <p>Sistem mengkategorikan pelanggaran berdasarkan tingkat keparahan untuk penanganan yang tepat</p>
            </div>
            <div class="violations-grid">
                <!-- Ringan -->
                <div class="violation-card ringan">
                    <div class="card-header"><div class="card-icon"><i class="fas fa-leaf"></i></div><h3>Ringan</h3></div>
                    <ul><li><i class="fas fa-clock"></i> Terlambat datang</li><li><i class="fas fa-tshirt"></i> Seragam tidak rapi</li><li><i class="fas fa-cut"></i> Rambut tidak sesuai aturan</li><li><i class="fas fa-trash-alt"></i> Membuang sampah sembarangan</li></ul>
                    <div class="card-footer">Poin: 1-10 | Sanksi: Teguran</div>
                </div>
                <!-- Sedang -->
                <div class="violation-card sedang">
                    <div class="card-header"><div class="card-icon"><i class="fas fa-exclamation"></i></div><h3>Sedang</h3></div>
                    <ul><li><i class="fas fa-comment-slash"></i> Berkata tidak sopan</li><li><i class="fas fa-mobile-alt"></i> Menggunakan HP tanpa izin</li><li><i class="fas fa-door-open"></i> Keluar kelas tanpa izin</li><li><i class="fas fa-ban"></i> Tidak mengerjakan tugas</li></ul>
                    <div class="card-footer">Poin: 11-25 | Sanksi: Pemanggilan orang tua</div>
                </div>
                <!-- Berat -->
                <div class="violation-card berat">
                    <div class="card-header"><div class="card-icon"><i class="fas fa-fire"></i></div><h3>Berat</h3></div>
                    <ul><li><i class="fas fa-fist-raised"></i> Perkelahian antar siswa</li><li><i class="fas fa-skull-crossbones"></i> Bullying/perundungan</li><li><i class="fas fa-wine-bottle"></i> Membawa rokok/minuman keras</li><li><i class="fas fa-gavel"></i> Vandalisme fasilitas sekolah</li></ul>
                    <div class="card-footer">Poin: 26-50 | Sanksi: Skorsing</div>
                </div>
            </div>
        </div>

        <!-- Tata Tertib Section -->
        <div class="rules-section">
            <div class="rules-grid">
                <div>
                    <div class="badge"><i class="fas fa-book-open"></i> <span>Peraturan Sekolah</span></div>
                    <h2>Tata Tertib SMP Negeri 7 Jember</h2>
                    <p>Menciptakan lingkungan belajar yang kondusif, aman, dan nyaman bagi seluruh warga sekolah.</p>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Hadir tepat waktu sebelum bel berbunyi</li>
                        <li><i class="fas fa-check-circle"></i> Mengenakan seragam sesuai ketentuan</li>
                        <li><i class="fas fa-check-circle"></i> Menjaga kebersihan dan keindahan lingkungan sekolah</li>
                        <li><i class="fas fa-check-circle"></i> Menghormati guru dan sesama siswa</li>
                        <li><i class="fas fa-check-circle"></i> Melaksanakan ibadah sesuai agama masing-masing</li>
                    </ul>
                </div>
                <div class="rules-quote">
                    <i class="fas fa-gavel"></i>
                    <blockquote>"Disiplin adalah jembatan antara tujuan dan prestasi"</blockquote>
                    <cite>- BK SMP Negeri 7 Jember</cite>
                </div>
            </div>
        </div>


    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="social-icons">
            <a href="https://www.instagram.com/stuju_bisa?igsh=MTBjaHFocGhvZjI2bQ==" target="_blank" rel="noopener noreferrer" style="text-decoration: none;"><i class="fab fa-instagram"></i></a><a href="https://www.tiktok.com/@stuju.jember?_r=1&_t=ZS-97td3kAMTwY" target="_blank" rel="noopener noreferrer" style="text-decoration: none;"><i class="fab fa-tiktok"></i></a><a href="https://youtube.com/@smpnegeri7jember?si=91QiITvL-Izi5QRo" target="_blank" rel="noopener noreferrer" style="text-decoration: none;"><i class="fab fa-youtube"></i></a>
        </div>
        <p>© {{ date('Y') }} SMP Negeri 7 Jember - Sistem Informasi Pelanggaran Siswa</p>
        <p class="address">Jl. Cendrawasih No.22, Puring, Slawu, Kec. Patrang, Kabupaten Jember, Jawa Timur 68118</p>
    </footer>

    <!-- Custom JavaScript -->
    @vite(['resources/js/welcome.js'])
</body>
</html>