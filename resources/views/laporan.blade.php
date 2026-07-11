<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Pelanggaran | Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    @vite(['resources/css/dashboard.css', 'resources/js/laporan.js'])
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
                    <h2>Laporan Pelanggaran</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                </div>
            </header>

            <!-- Kop Surat (Hanya Tampil saat Cetak) -->
            <style>
                .print-header, .print-title { display: none; }
                @media print {
                    @page {
                        margin: 0;
                    }
                    body {
                        padding: 1cm 1.5cm; /* Memberikan ruang di sekitar kertas */
                    }
                    .print-header {
                        display: flex !important;
                        align-items: center;
                        justify-content: space-between;
                        border-bottom: 5px solid black;
                        padding-bottom: 10px;
                        margin-bottom: 20px;
                    }
                    .print-header h1, .print-header h2, .print-header p {
                        color: black !important;
                    }
                    .print-title {
                        display: block !important;
                        text-align: center;
                        margin-bottom: 20px;
                    }
                }
            </style>
            <div class="print-header">
                <img src="{{ asset('images/logo-baru.png') }}" style="width: 100px; height: auto;" alt="Logo SMP 7">
                <div style="flex: 1; text-align: center; padding: 0 15px;">
                    <h1 style="margin: 0; font-size: 26px; font-weight: bold; text-transform: uppercase; font-family: 'Times New Roman', serif;">SMP NEGERI 7 JEMBER</h1>
                    <h2 style="margin: 0; font-size: 14px; font-weight: bold; text-transform: uppercase; font-family: 'Times New Roman', serif;">KECAMATAN PATRANG</h2>
                    <p style="margin: 5px 0 0 0; font-size: 14px; font-weight: bold; font-family: 'Times New Roman', serif;">Alamat : Jalan Cendrawasih No. 22 Telp. 486475 Jember</p>
                </div>
                <!-- Spacer for right side to keep text centered -->
                <div style="width: 100px;"></div>
            </div>

            <div class="print-title">
                <h3 style="margin-top: 0; font-size: 16px; font-weight: bold; text-decoration: underline; text-transform: uppercase; color: black;">Laporan Rekapitulasi Pelanggaran Siswa</h3>
                <p style="font-size: 12px; margin-top: 5px; color: black;">
                    @if($kelas_selected) Kelas: {{ $kelas_selected }} @endif
                    @if($kelas_selected && $kategori_selected) | @endif
                    @if($kategori_selected) Kategori: {{ ucfirst($kategori_selected) }} @endif
                    @if(($kelas_selected || $kategori_selected) && $search) | @endif
                    @if($search) Pencarian: "{{ $search }}" @endif
                </p>
            </div>

            <!-- Filter Panel -->
            <section class="filter-section">
                <form method="GET" action="{{ route('laporan') }}">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label for="search">Cari Nama / NIS</label>
                            <input type="text" class="filter-control" id="search" name="search" value="{{ $search }}" placeholder="Nama / NIS siswa...">
                        </div>

                        <div class="filter-group">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" class="filter-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggal_mulai }}">
                        </div>
                        
                        <div class="filter-group">
                            <label for="tanggal_selesai">Tanggal Selesai</label>
                            <input type="date" class="filter-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggal_selesai }}">
                        </div>

                        <div class="filter-group">
                            <label for="kelas">Kelas</label>
                            <select class="filter-control" id="kelas" name="kelas">
                                <option value="">Semua Kelas</option>
                                @foreach($list_kelas as $kelas)
                                    <option value="{{ $kelas }}" {{ $kelas_selected == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="kategori">Kategori</label>
                            <select class="filter-control" id="kategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                <option value="ringan" {{ $kategori_selected == 'ringan' ? 'selected' : '' }}>Ringan</option>
                                <option value="sedang" {{ $kategori_selected == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="berat" {{ $kategori_selected == 'berat' ? 'selected' : '' }}>Berat</option>
                            </select>
                        </div>

                        <div class="filter-group filter-actions">
                            <button type="submit" class="btn btn-filter">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('laporan') }}" class="btn-reset">
                                <i class="fas fa-arrow-rotate-left"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Statistik Panel -->
            <section class="page-stats-grid">
                <div class="page-stat-card total">
                    <div class="stat-info">
                        <h4>Total Kasus</h4>
                        <div class="value">{{ $stats['total'] }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                </div>

                <div class="page-stat-card ringan">
                    <div class="stat-info">
                        <h4>Ringan</h4>
                        <div class="value value-green">{{ $stats['ringan'] }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-circle-check icon-green"></i></div>
                </div>

                <div class="page-stat-card sedang">
                    <div class="stat-info">
                        <h4>Sedang</h4>
                        <div class="value value-yellow">{{ $stats['sedang'] }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-circle-exclamation icon-yellow"></i></div>
                </div>

                <div class="page-stat-card berat">
                    <div class="stat-info">
                        <h4>Berat</h4>
                        <div class="value value-red">{{ $stats['berat'] }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-circle-xmark icon-red"></i></div>
                </div>

                <div class="page-stat-card poin">
                    <div class="stat-info">
                        <h4>Total Poin</h4>
                        <div class="value value-cyan">{{ $stats['total_poin'] }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-coins icon-cyan"></i></div>
                </div>
            </section>

            <!-- Report Table Section -->
            <section class="report-section">
                <div class="report-header">
                    <h3 class="page-heading"><i class="fas fa-list-check"></i> Hasil Pencarian Laporan</h3>
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </div>

                <div class="recent-table-wrapper">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Wali Kelas</th>
                                <th>Bentuk Pelanggaran</th>
                                <th>Kategori</th>
                                <th>Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data_pelanggaran as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->tanggal_pelanggaran ? $item->tanggal_pelanggaran->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->siswa->nama_lengkap ?? 'Data Tidak Ditemukan' }}</td>
                                <td>{{ $item->siswa->kelas ?? '-' }}</td>
                                <td>{{ $item->siswa->waliKelas->nama_lengkap ?? '-' }}</td>
                                <td>{{ $item->jenis_pelanggaran ?? '-' }}</td>
                                <td>
                                    <span class="badge-kategori badge-kategori-{{ $item->kategori ?? 'ringan' }}">
                                        {{ $item->kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-poin">+{{ $item->poin ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data pelanggaran yang sesuai dengan filter pencarian.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
