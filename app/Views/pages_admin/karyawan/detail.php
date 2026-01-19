<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card shadow mb-4">
                <div class="card-body">
                    
                    <div class="row">
                        
                        <!-- Kolom Kiri: Foto dan Aksi -->
                        <div class="col-md-3 text-center border-right">
                            <!-- Foto Profil -->
                            <?php 
                                // Tentukan path foto. Jika foto tidak ada, gunakan default.
                                $foto_file = $karyawan['foto'] ?? 'default.png';
                                $foto_url = base_url('foto/' . $foto_file); 
                            ?>
                            <img src="<?= $foto_url ?>" alt="Foto Profil" class="img-fluid rounded-circle mb-3" 
                                style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #007bff;"
                                onerror="this.onerror=null;this.src='<?= base_url('foto/default.png')?>';">
                                
                            <h4 class="mb-0"><?= esc($karyawan['nm_karyawan']) ?></h4>
                            <p class="text-muted"><?= esc($karyawan['nip']) ?></p>
                            
                            <a href="<?= base_url('admin/karyawan/edit/'.$karyawan['id_karyawan'])?>" class="btn btn-info btn-sm mt-2"><i class="fas fa-edit"></i> Edit Profil</a>
                        </div>

                        <!-- Kolom Kanan: Detail Informasi -->
                        <div class="col-md-9">
                            
                            <!-- Bagian 1: Informasi Pribadi -->
                            <h5 class="text-primary mb-3"><i class="fas fa-user-circle"></i> Informasi Pribadi</h5>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><th style="width: 30%;">NIP</th><td><?= esc($karyawan['nip']) ?></td></tr>
                                    <tr><th>Jenis Kelamin</th><td><?= esc($karyawan['kelamin']) ?></td></tr>
                                    <tr><th>Agama</th><td><?= esc($karyawan['agama']) ?></td></tr>
                                    <tr><th>No. Telepon</th><td><?= esc($karyawan['no_telp']) ?></td></tr>
                                    <tr><th>Tempat, Tgl Lahir</th><td><?= esc($karyawan['tempat_lahir']) ?>, <?= esc(date('d F Y', strtotime($karyawan['tgl_lahir']))) ?></td></tr>
                                    <tr><th>Alamat</th><td><?= esc($karyawan['alamat']) ?></td></tr>
                                </tbody>
                            </table>
                            
                            <!-- Bagian 2: Informasi Pekerjaan -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase"></i> Data Pekerjaan</h5>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><th>Jabatan</th><td><?= esc($karyawan['nm_jabatan'] ?: 'N/A') ?></td></tr>
                                    <tr><th>Tanggal Masuk</th><td><?= esc(date('d F Y', strtotime($karyawan['tanggal_masuk']))) ?></td></tr>
                                </tbody>
                            </table>
                            
                            <!-- Bagian BARU: Lokasi Presensi Default -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-map-marker-alt"></i> Lokasi Presensi Default</h5>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><th style="width: 30%;">Lokasi Default</th>
                                        <td>
                                            <?php if ($karyawan['nama_lokasi_default']): ?>
                                                <?= esc($karyawan['nama_lokasi_default']) ?> (<?= esc($karyawan['tipe_lokasi_default']) ?>)
                                                
                                                <?php if ($karyawan['id_lokasi_default']): ?>
                                                    (<a href="<?= base_url('admin/lokasipresensi/detail/' . $karyawan['id_lokasi_default']) ?>">Lihat Detail</a>)
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-danger">Belum Ditentukan</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <!-- Jika Anda mengupdate Model untuk membawa jam dan zona waktu lokasi default, baris berikut bisa diaktifkan -->
                                    <?php if (isset($karyawan['lokasi_zona_waktu']) && $karyawan['lokasi_zona_waktu']): ?>
                                    <tr><th>Zona Waktu</th><td><?= esc($karyawan['lokasi_zona_waktu']) ?></td></tr>
                                    <tr><th>Jam Kerja</th><td><?= esc($karyawan['lokasi_jam_masuk']) ?> - <?= esc($karyawan['lokasi_jam_pulang']) ?></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <!-- Akhir Bagian BARU -->

                            <!-- Bagian 3: Detail Penggajian -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-dollar-sign"></i> Detail Penggajian</h5>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><th style="width: 30%;">Status Kawin</th><td><?= esc($karyawan['status_kawin']) ?></td></tr>
                                    <tr><th>Jumlah Anak</th><td><?= esc($karyawan['jumlah_anak']) ?></td></tr>
                                    <tr><th>Status PTKP</th>
                                        <td>
                                            <?php if ($ptkp): ?>
                                                <!-- Menggunakan nama_ptkp yang baru dan total_ptkp -->
                                                <?= esc($ptkp['nama_ptkp'] ?: 'N/A') ?> (Rp. <?= number_format($ptkp['total_ptkp'], 0, ',', '.') ?>)
                                                <small class="text-muted">(<?= esc($ptkp['keterangan']) ?>)</small>
                                            <?php else: ?>
                                                <span class="text-danger">Data PTKP tidak ditemukan.</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Bagian 4: Detail Akun User -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-lock"></i> Detail Akun User</h5>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <?php if ($karyawan['kd_user']): ?>
                                        <tr><th>Username</th><td><?= esc($karyawan['username']) ?></td></tr>
                                        <tr><th>Level Akses</th><td><span class="badge bg-primary text-white"><?= esc($karyawan['level']) ?></span></td></tr>
                                    <?php else: ?>
                                        <tr><td colspan="2"><span class="text-danger">Karyawan ini belum memiliki akun user.</span></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <a href="<?= base_url('admin/karyawan')?>" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>