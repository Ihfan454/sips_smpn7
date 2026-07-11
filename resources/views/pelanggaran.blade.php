<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Pelanggaran | Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    @vite(['resources/css/dashboard.css', 'resources/js/pelanggaran.js'])
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
                    <h2>Data Pelanggaran</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari...">
                    </div>
                </div>
            </header>

            <section class="recent-section page-section-margin">
                <div class="report-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Daftar Pelanggaran Siswa</h3>
                    @if(auth()->user()->isAdminBK() || auth()->user()->isGuruBK())
                    <a href="{{ route('pelanggaran.create') }}" class="btn-tambah">
                        <i class="fas fa-plus"></i> Catat Pelanggaran
                    </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="recent-table-wrapper">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Bentuk Pelanggaran</th>
                                <th>Kategori</th>
                                <th>Poin</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data_pelanggaran as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->tanggal_pelanggaran ? $item->tanggal_pelanggaran->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->siswa->nama_lengkap ?? 'Data Tidak Ditemukan' }}</td>
                                <td>{{ $item->siswa->kelas ?? '-' }}</td>
                                <td>{{ $item->jenis_pelanggaran ?? '-' }}</td>
                                <td>
                                    <span class="badge-kategori badge-kategori-{{ $item->kategori ?? 'ringan' }}">
                                        {{ $item->kategori ?? '-' }}
                                    </span>
                                </td>
                                <td style="color: #ef4444; font-weight: 700;">+{{ $item->poin ?? 0 }}</td>
                                @if(auth()->user()->isAdminBK() || auth()->user()->isGuruBK())
                                <td class="text-center">
                                    <a href="{{ route('pelanggaran.edit', $item->id ?? 1) }}" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pelanggaran.destroy', $item->id ?? 1) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus catatan pelanggaran ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                @else
                                <td class="text-center">
                                    <span style="color:#94a3b8;font-size:0.75rem;">Read Only</span>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 20px;">Belum ada data pelanggaran yang tercatat.</td>
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