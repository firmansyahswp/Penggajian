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
                    <form class="needs-validation" method="POST" action="<?= base_url('admin/karyawan/update') ?>" enctype="multipart/form-data" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_karyawan" value="<?= $karyawan['id_karyawan'] ?>">
                        <?php if ($user): ?>
                            <input type="hidden" name="kd_user" value="<?= $user['kd_user'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $user['foto'] ?>">
                        <?php else: ?>
                            <input type="hidden" name="foto_lama" value="">
                        <?php endif; ?>

                        <div class="row">
                            
                            <div class="col-md-9 border-right pr-4">
                                
                                <h5 class="text-primary mb-3 pb-2 border-bottom"><i class="fas fa-user-tie"></i> Data Pribadi Karyawan</h5>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="nip">NIP (Nomor Induk Pegawai) <span class="text-danger">*</span></label>
                                        <input type="text" name="nip" id="nip" class="form-control <?= service('validation')->hasError('nip') ? 'is-invalid' : '' ?>" value="<?= old('nip', $karyawan['nip']) ?>" required>
                                        <div class="invalid-feedback"><?= service('validation')->showError('nip') ?: 'NIP wajib diisi.' ?></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nm_karyawan">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nm_karyawan" id="nm_karyawan" class="form-control <?= service('validation')->hasError('nm_karyawan') ? 'is-invalid' : '' ?>" value="<?= old('nm_karyawan', $karyawan['nm_karyawan']) ?>" required>
                                        <div class="invalid-feedback"><?= service('validation')->showError('nm_karyawan') ?: 'Nama Lengkap wajib diisi.' ?></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" id="alamat" class="form-control <?= service('validation')->hasError('alamat') ? 'is-invalid' : '' ?>" rows="2"><?= old('alamat', $karyawan['alamat']) ?></textarea>
                                    <div class="invalid-feedback"><?= service('validation')->showError('alamat') ?></div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <?php $selected_kelamin = old('kelamin', $karyawan['kelamin']); ?>
                                        <select name="kelamin" id="kelamin" class="form-control <?= service('validation')->hasError('kelamin') ? 'is-invalid' : '' ?>" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki" <?= $selected_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="Perempuan" <?= $selected_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                        <div class="invalid-feedback"><?= service('validation')->showError('kelamin') ?: 'Jenis Kelamin wajib dipilih.' ?></div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="agama">Agama</label>
                                        <input type="text" name="agama" id="agama" class="form-control <?= service('validation')->hasError('agama') ? 'is-invalid' : '' ?>" value="<?= old('agama', $karyawan['agama']) ?>">
                                        <div class="invalid-feedback"><?= service('validation')->showError('agama') ?></div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="no_telp">Nomor Telepon</label>
                                        <input type="text" name="no_telp" id="no_telp" class="form-control <?= service('validation')->hasError('no_telp') ? 'is-invalid' : '' ?>" value="<?= old('no_telp', $karyawan['no_telp']) ?>">
                                        <div class="invalid-feedback"><?= service('validation')->showError('no_telp') ?></div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="tempat_lahir">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control <?= service('validation')->hasError('tempat_lahir') ? 'is-invalid' : '' ?>" value="<?= old('tempat_lahir', $karyawan['tempat_lahir']) ?>">
                                        <div class="invalid-feedback"><?= service('validation')->showError('tempat_lahir') ?></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tgl_lahir">Tanggal Lahir</label>
                                        <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control <?= service('validation')->hasError('tgl_lahir') ? 'is-invalid' : '' ?>" value="<?= old('tgl_lahir', $karyawan['tgl_lahir']) ?>">
                                        <div class="invalid-feedback"><?= service('validation')->showError('tgl_lahir') ?></div>
                                    </div>
                                </div>

                                <h5 class="text-primary mb-3 mt-4 pb-2 border-bottom"><i class="fas fa-briefcase"></i> Data Pekerjaan & Presensi</h5>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="kd_jabatan">Jabatan <span class="text-danger">*</span></label>
                                        <?php $selected_jabatan = old('kd_jabatan', $karyawan['kd_jabatan']); ?>
                                        <select name="kd_jabatan" id="kd_jabatan" class="form-control <?= service('validation')->hasError('kd_jabatan') ? 'is-invalid' : '' ?>" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                            <?php foreach ($jabatan_list as $j): ?>
                                                <option value="<?= $j['kd_jabatan'] ?>" <?= $selected_jabatan == $j['kd_jabatan'] ? 'selected' : '' ?>>
                                                    <?= esc($j['nm_jabatan']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"><?= service('validation')->showError('kd_jabatan') ?: 'Jabatan wajib dipilih.' ?></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control <?= service('validation')->hasError('tanggal_masuk') ? 'is-invalid' : '' ?>" value="<?= old('tanggal_masuk', $karyawan['tanggal_masuk']) ?>" required>
                                        <div class="invalid-feedback"><?= service('validation')->showError('tanggal_masuk') ?: 'Tanggal Masuk wajib diisi.' ?></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="id_lokasi_default"><i class="fas fa-map-marker-alt"></i> Lokasi Presensi Default</label>
                                    <?php $selected_lokasi = old('id_lokasi_default', $karyawan['id_lokasi_default']); ?>
                                    <select name="id_lokasi_default" id="id_lokasi_default" class="form-control <?= service('validation')->hasError('id_lokasi_default') ? 'is-invalid' : '' ?>">
                                        <option value="">-- Tidak Ada Lokasi Default --</option>
                                        <?php foreach ($lokasi_list as $l): ?>
                                            <option value="<?= $l['id'] ?>" <?= $selected_lokasi == $l['id'] ? 'selected' : '' ?>>
                                                <?= esc($l['nama_lokasi']) ?> (<?= esc($l['tipe_lokasi']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback"><?= service('validation')->showError('id_lokasi_default') ?></div>
                                </div>
                                
                            </div> <div class="col-md-3 pl-4">

                                <h5 class="text-primary mb-3 pb-2 border-bottom"><i class="fas fa-dollar-sign"></i> Penggajian & Perpajakan</h5>
                                
                                <div class="form-group">
                                    <label for="status_kawin">Status Kawin</label>
                                    <?php $selected_kawin = old('status_kawin', $karyawan['status_kawin']); ?>
                                    <select name="status_kawin" id="status_kawin" class="form-control <?= service('validation')->hasError('status_kawin') ? 'is-invalid' : '' ?>">
                                        <option value="">-- Pilih --</option>
                                        <option value="Belum Kawin" <?= $selected_kawin == 'Belum Kawin' ? 'selected' : '' ?>>Belum Kawin</option>
                                        <option value="Kawin" <?= $selected_kawin == 'Kawin' ? 'selected' : '' ?>>Kawin</option>
                                        <option value="Cerai" <?= $selected_kawin == 'Cerai' ? 'selected' : '' ?>>Cerai</option>
                                    </select>
                                    <div class="invalid-feedback"><?= service('validation')->showError('status_kawin') ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="jumlah_anak">Jumlah Anak</label>
                                    <input type="number" name="jumlah_anak" id="jumlah_anak" class="form-control <?= service('validation')->hasError('jumlah_anak') ? 'is-invalid' : '' ?>" value="<?= old('jumlah_anak', $karyawan['jumlah_anak']) ?>">
                                    <div class="invalid-feedback"><?= service('validation')->showError('jumlah_anak') ?></div>
                                </div>

                                <div class="form-group">
                                    <label for="id_ptkp">Status PTKP</label>
                                    <?php $selected_ptkp = old('id_ptkp', $karyawan['id_ptkp']); ?>
                                    <select name="id_ptkp" id="id_ptkp" class="form-control <?= service('validation')->hasError('id_ptkp') ? 'is-invalid' : '' ?>">
                                        <option value="">-- Pilih PTKP --</option>
                                        <?php foreach ($ptkp_list as $p): ?>
                                            <option value="<?= $p['id_ptkp'] ?>" <?= $selected_ptkp == $p['id_ptkp'] ? 'selected' : '' ?>>
                                                <?= esc($p['nama_ptkp']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback"><?= service('validation')->showError('id_ptkp') ?></div>
                                </div>

                                <h5 class="text-primary mb-3 mt-4 pb-2 border-bottom"><i class="fas fa-lock"></i> Akun Pengguna</h5>

                                <?php if (!$user): ?>
                                    <div class="alert alert-warning">
                                        Karyawan ini belum memiliki akun. Silakan buat akun di bawah.
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" name="username" id="username" class="form-control <?= service('validation')->hasError('username') ? 'is-invalid' : '' ?>" 
                                            value="<?= old('username', $user['username'] ?? $karyawan['nip']) ?>"
                                            <?= !$user ? 'required' : '' ?>>
                                    <div class="invalid-feedback"><?= service('validation')->showError('username') ?: (!$user ? 'Username wajib diisi.' : '') ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="level">Level Akses</label>
                                    <?php $selected_level = old('level', $user['level'] ?? 'Pegawai'); ?>
                                    <select name="level" id="level" class="form-control <?= service('validation')->hasError('level') ? 'is-invalid' : '' ?>" required>
                                        <option value="Pegawai" <?= $selected_level == 'Pegawai' ? 'selected' : '' ?>>Pegawai</option>
                                        <option value="Admin" <?= $selected_level == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <div class="invalid-feedback"><?= service('validation')->showError('level') ?: 'Level Akses wajib dipilih.' ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Password <?= $user ? 'Baru' : ' (Wajib) ' ?><?= !$user ? '<span class="text-danger">*</span>' : '' ?></label>
                                    <input type="password" name="password" id="password" class="form-control <?= service('validation')->hasError('password') ? 'is-invalid' : '' ?>" 
                                            placeholder="<?= $user ? 'Kosongkan jika tidak diubah' : 'Isi password untuk akun baru' ?>"
                                            <?= !$user ? 'required' : '' ?>>
                                    <div class="text-small text-info">
                                        <?= $user ? 'Password hanya diubah jika field ini diisi.' : 'Password wajib diisi untuk membuat akun.' ?>
                                    </div>
                                    <div class="invalid-feedback"><?= service('validation')->showError('password') ?: (!$user ? 'Password wajib diisi.' : '') ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="foto">Ubah Foto Profil</label>
                                    <input type="file" name="foto" id="foto" class="form-control <?= service('validation')->hasError('foto') ? 'is-invalid' : '' ?>">
                                    <div class="invalid-feedback"><?= service('validation')->showError('foto') ?></div>
                                    <?php if ($user && $user['foto']): ?>
                                        <div class="mt-2 text-center">
                                            <img src="<?= base_url('foto/' . $user['foto']) ?>" alt="Foto Profil Saat Ini" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div> </div> <hr class="mt-4">

                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Data</button>
                            <a href="<?= base_url('admin/karyawan')?>" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        'use strict';

        // Ambil form dengan class needs-validation (hanya 1 form di halaman ini)
        const form = document.querySelector('.needs-validation'); 
        
        if (form) {
            // 1. Terapkan Validasi Sisi Klien saat submit
            form.addEventListener('submit', function(event) {
                // Gunakan checkValidity() bawaan browser untuk memeriksa input required dll.
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                // Menambahkan kelas was-validated saat submit 
                // Ini akan mengaktifkan display invalid-feedback dan styling is-invalid browser
                form.classList.add('was-validated'); 
            }, false);
            
            // 2. Jika ada error dari server (setelah page reload), pastikan kelas pemicu validation terpasang
            if (form.querySelector('.is-invalid')) {
                // Tambahkan kelas was-validated jika ada error CI4 (is-invalid) ditemukan.
                // Ini memastikan styling merah error server-side langsung muncul saat reload.
                form.classList.add('was-validated');
            }
        }
    });
</script>