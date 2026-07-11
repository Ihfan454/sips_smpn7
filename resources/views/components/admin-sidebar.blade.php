<script>
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    })();
</script>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="{{ asset('images/logo-baru.png') }}" alt="Logo SMP Negeri 7 Jember" class="brand-logo">
        </div>
        <div class="brand-text">
            <span>SMPN 7 Jember</span>
            <small>Sistem Pelanggaran</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('data-siswa') }}" class="nav-item {{ request()->routeIs('data-siswa') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i>
            <span>Data Siswa</span>
        </a>
        <a href="{{ route('pelanggaran') }}" class="nav-item {{ request()->routeIs('pelanggaran') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Pelanggaran</span>
        </a>
        <a href="{{ route('laporan') }}" class="nav-item {{ request()->routeIs('laporan') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Laporan</span>
        </a>
        @if(auth()->user()->isAdminBK())
        <a href="{{ route('guru-bk.index') }}" class="nav-item {{ request()->routeIs('guru-bk.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Manajemen User</span>
        </a>
        @endif
        @if(auth()->user()->isAdminBK() || auth()->user()->isGuruBK())
        <a href="{{ route('wali-kelas.index') }}" class="nav-item {{ request()->routeIs('wali-kelas.*') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Data Wali Kelas</span>
        </a>
        @endif
        <a href="{{ route('pengaturan') }}" class="nav-item {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Pengaturan</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="theme-toggle-container" style="margin-bottom: 0.75rem;">
            <button id="themeToggleBtn" class="logout-btn" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                <i class="fas fa-sun" id="themeSunIcon" style="display: none;"></i>
                <i class="fas fa-moon" id="themeMoonIcon"></i>
                <span id="themeToggleText">Tema Gelap</span>
            </button>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
        <div class="user-info">
            <div class="user-avatar">
                @if(auth()->user()->foto && file_exists(public_path(auth()->user()->foto)))
                    <img src="{{ asset(auth()->user()->foto) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    {{ substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div class="user-name">{{ auth()->user()->name }}</div>
            <small>
                @if(auth()->user()->isAdminBK())
                    Admin BK
                @elseif(auth()->user()->isGuruBK())
                    Guru BK

                @else
                    {{ auth()->user()->jabatan ?? 'Pengguna' }}
                @endif
            </small>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('themeToggleBtn');
        const sunIcon = document.getElementById('themeSunIcon');
        const moonIcon = document.getElementById('themeMoonIcon');
        const toggleText = document.getElementById('themeToggleText');

        function updateToggleUI(theme) {
            if (theme === 'dark') {
                sunIcon.style.display = 'inline-block';
                moonIcon.style.display = 'none';
                toggleText.textContent = 'Tema Terang';
            } else {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'inline-block';
                toggleText.textContent = 'Tema Gelap';
            }
        }

        const currentTheme = localStorage.getItem('theme') || 'dark';
        updateToggleUI(currentTheme);

        toggleBtn.addEventListener('click', () => {
            const nowTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', nowTheme);
            localStorage.setItem('theme', nowTheme);
            updateToggleUI(nowTheme);
        });
    });
</script>
