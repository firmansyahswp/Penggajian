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
            
            <div class="row">
                <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">
                    
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            
                            <!-- Mengarah ke Controller LokasiPresensi metode update() -->
                           <form method="POST" action="<?= base_url('admin/lokasipresensi/update/' . $lokasi['id']) ?>">
                                <?= csrf_field() ?>
                                <!-- Hidden ID (Primary Key) -->
                                <input type="hidden" name="id" value="<?= $lokasi['id'] ?>">

                                <!-- Nama Lokasi -->
                                <div class="form-group">
                                    <label for="nama_lokasi">Nama Lokasi Presensi</label>
                                    <input type="text" name="nama_lokasi" id="nama_lokasi" class="form-control" 
                                            value="<?= old('nama_lokasi', $lokasi['nama_lokasi']) ?>">
                                    
                                    <div class="text-small text-danger">
                                        <?= service('validation')->showError('nama_lokasi') ?>
                                    </div>
                                </div>

                                <!-- Alamat Lokasi -->
                                <div class="form-group">
                                    <label for="alamat_lokasi">Alamat Lokasi Lengkap</label>
                                    <textarea name="alamat_lokasi" id="alamat_lokasi" class="form-control" rows="3"><?= old('alamat_lokasi', $lokasi['alamat_lokasi']) ?></textarea>
                                    <div class="text-small text-danger">
                                        <?= service('validation')->showError('alamat_lokasi') ?>
                                    </div>
                                </div>

                                <!-- Tipe Lokasi -->
                                <div class="form-group">
                                    <label for="tipe_lokasi">Tipe Lokasi</label>
                                    <select name="tipe_lokasi" id="tipe_lokasi" class="form-control">
                                        <?php $selected_tipe = old('tipe_lokasi', $lokasi['tipe_lokasi']); ?>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="Kantor Pusat" <?= $selected_tipe == 'Kantor Pusat' ? 'selected' : '' ?>>Kantor Pusat</option>
                                        <option value="Kantor Cabang" <?= $selected_tipe == 'Kantor Cabang' ? 'selected' : '' ?>>Kantor Cabang</option>
                                        <option value="Proyek" <?= $selected_tipe == 'Proyek' ? 'selected' : '' ?>>Proyek</option>
                                    </select>
                                    <div class="text-small text-danger">
                                        <?= service('validation')->showError('tipe_lokasi') ?>
                                    </div>
                                </div>

                                <!-- Latitude dan Longitude -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="latitude">Latitude</label>
                                        <input type="text" name="latitude" id="latitude" class="form-control"
                                                value="<?= old('latitude', $lokasi['latitude']) ?>" placeholder="-6.200000">
                                        <div class="text-small text-danger">
                                            <?= service('validation')->showError('latitude') ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="longitude">Longitude</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control"
                                                value="<?= old('longitude', $lokasi['longitude']) ?>" placeholder="106.816666">
                                        <div class="text-small text-danger">
                                            <?= service('validation')->showError('longitude') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Radius dan Zona Waktu -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="radius">Radius Toleransi (Meter)</label>
                                        <input type="number" name="radius" id="radius" class="form-control"
                                                value="<?= old('radius', $lokasi['radius']) ?>" placeholder="Misal: 50">
                                        <div class="text-small text-danger">
                                            <?= service('validation')->showError('radius') ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="zona_waktu">Zona Waktu (Contoh: WIB, WITA, WIT)</label>
                                        <input type="text" name="zona_waktu" id="zona_waktu" class="form-control"
                                                value="<?= old('zona_waktu', $lokasi['zona_waktu']) ?>" placeholder="WIB">
                                        <div class="text-small text-danger">
                                            <?= service('validation')->showError('zona_waktu') ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Jam Masuk dan Jam Pulang -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="jam_masuk">Jam Masuk</label>
                                        <input type="time" name="jam_masuk" id="jam_masuk" class="form-control"
                                                value="<?= old('jam_masuk', $lokasi['jam_masuk']) ?>">
                                        <div class="text-small text-danger">
                                            <?= service('validation')->showError('jam_masuk') ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="jam_pulang">Jam Pulang</label>
                                        <input type="time" name="jam_pulang" id="jam_pulang" class="form-control"
                                                value="<?= old('jam_pulang', $lokasi['jam_pulang']) ?>">
                                        <div class="text-small text-danger">
                                            <?= service('validation')->showError('jam_pulang') ?>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">Update</button>
                                <!-- Link kembali ke Lokasi Presensi -->
                                <a href="<?= base_url('admin/lokasipresensi')?>" class="btn btn-warning">Kembali</a>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>