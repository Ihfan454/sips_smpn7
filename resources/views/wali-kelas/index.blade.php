<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Guru BK | Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    @vite(['resources/css/dashboard.css', 'resources/js/guru-bk.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h2>Manajemen Guru BK</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                </div>
            </header>

            <section class="section-card">
                <div class="report-header">
                    <h3 class="page-heading"><i class="fas fa-chalkboard-user"></i> Daftar Wali Kelas</h3>
                    <a href="{{ route('wali-kelas.create') }}" class="btn-tambah">
                        <i class="fas fa-user-plus"></i> Tambah Wali Kelas
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="recent-table-wrapper">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>NIP / PPPK</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas Diampu</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th style="text-align: center; width: 15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($wali_kelas as $index => $wali)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($wali->nip)
                                        <span title="NIP">{{ $wali->nip }}</span> <br><small class="text-muted">(PNS)</small>
                                    @elseif($wali->ni_pppk)
                                        <span title="NI PPPK">{{ $wali->ni_pppk }}</span> <br><small class="text-muted">(PPPK)</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-accent" style="font-weight: 600;">{{ $wali->nama_lengkap }}</td>
                                <td>{{ $wali->kelas_wali }}</td>
                                <td>{{ $wali->no_hp ?? '-' }}</td>
                                <td>
                                    <span class="badge-status {{ $wali->is_active ? 'badge-status-aktif' : 'badge-status-nonaktif' }}">
                                        {{ $wali->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('wali-kelas.edit', $wali->id) }}" class="btn-action btn-edit" title="Edit Wali Kelas">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('wali-kelas.destroy', $wali->id) }}" method="POST" style="display: inline;" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action btn-delete btn-delete-wali" data-name="{{ $wali->nama_lengkap }}" title="Hapus Wali Kelas">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">Belum ada data Wali Kelas yang ditambahkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toast / Session alerts
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const bgColor = isDark ? '#1e293b' : '#ffffff';
            const textColor = isDark ? '#f8fafc' : '#1e293b';

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: bgColor,
                    color: textColor
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    showConfirmButton: true,
                    confirmButtonColor: '#ef4444',
                    background: bgColor,
                    color: textColor
                });
            @endif

            // Delete Confirmations
            const deleteButtons = document.querySelectorAll('.btn-delete-wali');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const waliName = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Hapus Wali Kelas?',
                        html: `Apakah Anda yakin ingin menghapus data wali kelas <strong>${waliName}</strong>?<br><span style="color: #ef4444; font-size: 0.875rem;">Tindakan ini tidak dapat dibatalkan!</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        background: bgColor,
                        color: textColor,
                        iconColor: '#ef4444'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
