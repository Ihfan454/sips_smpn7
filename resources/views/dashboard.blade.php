<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | Sistem Informasi Pelanggaran Siswa - SMP Negeri 7 Jember</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
</head>
<body>
    <!-- Overlay untuk mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="dashboard-wrapper">
        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>Dashboard</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                </div>
            </header>

            <!-- Stats Cardss -->
            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Siswa</h3>
                            <p class="stat-number" id="totalSiswa">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Pelanggaran</h3>
                            <p class="stat-number" id="totalPelanggaran">0</p>
                        </div>
                    </div>
                    <!-- Status cards removed -->
                </div>
            </section>
 
            <!-- Charts Section -->
            <section class="charts-section">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-column"></i> Statistik Pelanggaran per Kategori</h3>
                        <div class="chart-actions">
                            <button class="chart-btn active" data-period="week">Minggu</button>
                            <button class="chart-btn" data-period="month">Bulan</button>
                            <button class="chart-btn" data-period="year">Tahun</button>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Distribusi Pelanggaran</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </section>
 
            <!-- Recent Violations -->
            <section class="recent-section">
                <div class="recent-header">
                    <h3><i class="fas fa-clock-rotate-left"></i> Pelanggaran Terbaru</h3>
                    <a href="#" class="view-all">Lihat Semua →</a>
                </div>
                <div class="recent-table-wrapper">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Kategori</th>
                                <th>Poin</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="recentTableBody">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>