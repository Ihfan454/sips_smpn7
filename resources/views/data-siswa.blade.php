<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Siswa | Sistem Informasi Pelanggaran Siswa - SMP Negeri 7 Jember</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Vite Assets -->
    @vite(['resources/css/data-siswa.css', 'resources/js/data-siswa.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.userCanCRUD = {{ (auth()->user()->isAdminBK() || auth()->user()->isGuruBK()) ? 'true' : 'false' }};
    </script>
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
                    <h2>Data Siswa</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Cari NIS, NISN, atau nama..." id="searchSiswa">
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
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
                        <div class="stat-icon green">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Siswa Aktif</h3>
                            <p class="stat-number" id="siswaAktif">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Siswa Nonaktif</h3>
                            <p class="stat-number" id="siswaNonaktif">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-school"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Kelas</h3>
                            <p class="stat-number" id="totalKelas">0</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Filter & Actions -->
            <section class="filter-section">
                <div class="filter-left">
                    <div class="filter-group">
                        <label>Filter Kelas:</label>
                        <select id="filterKelas" class="filter-select">
                            <option value="all">Semua Kelas</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status:</label>
                        <select id="filterStatus" class="filter-select">
                            <option value="all">Semua</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="filter-right">
                    @if(auth()->user()->isAdminBK() || auth()->user()->isGuruBK())
                    <button class="btn-add" id="btnTambahSiswa" style="margin-right: 0.5rem;">
                        <i class="fas fa-user-plus"></i> Tambah Siswa
                    </button>
                    <button class="btn-export" id="btnNaikKelas" style="background-color: #f59e0b; color: white; border: none; margin-right: 0.5rem;">
                        <i class="fas fa-arrows-up-down"></i> Naik/Pindah Kelas
                    </button>
                    <button class="btn-export" id="btnHapusLulus" style="display: none; background-color: #ef4444; color: white; border: none; margin-right: 0.5rem;">
                        <i class="fas fa-user-minus"></i> Hapus Lulus
                    </button>
                    <button class="btn-export" id="btnImport" style="background-color: #10b981; color: white; border: none; margin-right: 0.5rem;">
                        <i class="fas fa-file-excel"></i> Import Excel
                    </button>
                    @endif
                    <button class="btn-export" id="btnExport">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                </div>
            </section>

            <!-- Table -->
            <section class="table-section">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jenis Kelamin</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th>Total Poin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info">
                        <span id="paginationInfo">Menampilkan 1-10 dari 0 data</span>
                    </div>
                    <div class="pagination-buttons">
                        <button class="page-btn" id="prevPage" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="pageNumbers"></span>
                        <button class="page-btn" id="nextPage">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Tambah/Edit Siswa -->
    <div class="modal" id="modalSiswa">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Siswa</h3>
                <button class="modal-close" id="modalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="formSiswa">
                    <input type="hidden" id="editId" value="">
                    <div class="form-row">
                        <div class="form-group">
                            <label>NIS</label>
                            <input type="text" id="nis" class="form-input" placeholder="Nomor Induk Sekolah" required>
                        </div>
                        <div class="form-group">
                            <label>NISN</label>
                            <input type="text" id="nisn" class="form-input" placeholder="Nomor Induk Siswa Nasional" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="nama" class="form-input" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select id="kelas" class="form-input" required>
                                <option value="">Pilih Kelas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select id="jenisKelamin" class="form-input" required>
                                <option value="">Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" id="noHp" class="form-input" placeholder="Masukkan nomor HP">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea id="alamat" class="form-input" rows="2" placeholder="Masukkan alamat"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="status" class="form-input">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Tidak Aktif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" id="modalCancel">Batal</button>
                <button class="btn-save" id="modalSave">Simpan</button>
            </div>
        </div>
    </div>
    <!-- Modal Naik/Pindah Kelas -->
    <div class="modal" id="modalNaikKelas">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Pindah Kelas Massal</h3>
                <button class="modal-close" id="modalNaikKelasClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Pindahkan seluruh siswa dari satu kelas ke kelas lain sekaligus.</p>
                <form id="formNaikKelas">
                    <div class="form-group">
                        <label>Kelas Asal</label>
                        <select id="kelasAsal" class="form-input" required>
                            <option value="">Pilih Kelas Asal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kelas Tujuan</label>
                        <select id="kelasTujuan" class="form-input" required>
                            <option value="">Pilih Kelas Tujuan</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" id="modalNaikKelasCancel">Batal</button>
                <button class="btn-save" id="modalNaikKelasSave" style="background-color: #f59e0b;">Proses Pindah</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Import Excel -->
    <div class="modal" id="modalImport">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Import Data Siswa</h3>
                <button class="modal-close" id="modalImportClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Unggah file Excel (.xlsx) untuk memasukkan data siswa secara massal.</p>
                <div style="margin-bottom: 15px;">
                    <a href="/api/siswa/template" class="btn-add" style="display: block; text-align: center; background-color: #3b82f6; text-decoration: none;">
                        <i class="fas fa-download"></i> Download Template Excel
                    </a>
                </div>
                <form id="formImport">
                    <div class="form-group">
                        <label>File Excel (.xlsx)</label>
                        <input type="file" id="fileExcel" class="form-input" accept=".xlsx, .xls" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" id="modalImportCancel">Batal</button>
                <button class="btn-save" id="modalImportSave" style="background-color: #10b981;">Import Data</button>
            </div>
        </div>
    </div>
</body>
</html>