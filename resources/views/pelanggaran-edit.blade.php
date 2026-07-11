<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Pelanggaran | Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
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
                    <h2>Edit Data Pelanggaran</h2>
                </div>
                <div class="topbar-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">0</span>
                    </div>
                </div>
            </header>

            <div class="form-section">
                <div style="margin-bottom: 25px;">
                    <h3 class="page-heading"><i class="fas fa-clipboard-list"></i> Formulir Input Data</h3>
                    <p class="page-subtitle" style="margin-bottom: 25px;">Silakan lengkapi data pelanggaran siswa di bawah ini.</p>
                </div>

                <form action="{{ route('pelanggaran.update', $pelanggaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal">Tanggal Pelanggaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal', $pelanggaran->tanggal_pelanggaran ? date('Y-m-d', strtotime($pelanggaran->tanggal_pelanggaran)) : '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="siswa_id">Nama Siswa <span class="text-danger">*</span></label>
                                <select class="form-control" id="siswa_id" name="siswa_id" required>
                                    <option value=""></option>
                                    @if(isset($data_siswa) && count($data_siswa) > 0)
                                        @foreach ($data_siswa as $siswa)
                                            <option value="{{ $siswa->id }}" {{ $siswa->id == old('siswa_id', $pelanggaran->siswa_id) ? 'selected' : '' }}>
                                                {{ $siswa->nisn ?? $siswa->nis ?? '-' }} - {{ $siswa->nama_lengkap ?? $siswa->nama_siswa ?? 'Nama Tidak Diketahui' }} ({{ $siswa->kelas ?? '-' }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Data siswa kosong di database</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="jenis_pelanggaran">Bentuk Pelanggaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="jenis_pelanggaran" name="jenis_pelanggaran" placeholder="Contoh: Terlambat masuk sekolah, atribut tidak lengkap..." value="{{ old('jenis_pelanggaran', $pelanggaran->jenis_pelanggaran) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori Pelanggaran <span class="text-danger">*</span></label>
                        <select class="form-control" id="kategori" name="kategori" required>
                            <option value="ringan" {{ old('kategori', $pelanggaran->kategori) == 'ringan' ? 'selected' : '' }}>Ringan</option>
                            <option value="sedang" {{ old('kategori', $pelanggaran->kategori) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="berat" {{ old('kategori', $pelanggaran->kategori) == 'berat' ? 'selected' : '' }}>Berat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="poin">Poin Pelanggaran <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="poin" name="poin" placeholder="Contoh: 10" min="1" value="{{ old('poin', $pelanggaran->poin) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Tambahan / Keterangan (Opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="4" placeholder="Tambahkan kronologi atau keterangan spesifik jika ada...">{{ old('catatan', $pelanggaran->deskripsi) }}</textarea>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('pelanggaran') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Pelanggaran
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#siswa_id').select2({
                placeholder: "-- Ketik NISN atau Nama Siswa --",
                allowClear: true,
                width: '100%' 
            });
        });
    </script>
</body>
</html>