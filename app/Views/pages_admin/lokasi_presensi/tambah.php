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
                            
                            <form method="POST" action="<?= base_url('admin/lokasipresensi/store') ?>">
                                <?= csrf_field() ?>

                                <div class="form-group">
                                    <label for="nama_lokasi">Nama Lokasi Presensi <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_lokasi" id="nama_lokasi" class="form-control" 
                                            value="<?= old('nama_lokasi') ?>" required placeholder="Contoh: Kantor Pusat Jakarta">
                                    <div class="text-small text-danger">
                                        <?= validation_show_error('nama_lokasi') ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="alamat_lokasi">Alamat Lokasi Lengkap <span class="text-danger">*</span></label>
                                    <textarea name="alamat_lokasi" id="alamat_lokasi" class="form-control" rows="3" required placeholder="Jalan Raya No. 123..."><?= old('alamat_lokasi') ?></textarea>
                                    <div class="text-small text-danger">
                                        <?= validation_show_error('alamat_lokasi') ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tipe_lokasi">Tipe Lokasi <span class="text-danger">*</span></label>
                                    <select name="tipe_lokasi" id="tipe_lokasi" class="form-control" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="Kantor Pusat" <?= old('tipe_lokasi') == 'Kantor Pusat' ? 'selected' : '' ?>>Kantor Pusat</option>
                                        <option value="Kantor Cabang" <?= old('tipe_lokasi') == 'Kantor Cabang' ? 'selected' : '' ?>>Kantor Cabang</option>
                                        <option value="Proyek" <?= old('tipe_lokasi') == 'Proyek' ? 'selected' : '' ?>>Proyek</option>
                                    </select>
                                    <div class="text-small text-danger">
                                        <?= validation_show_error('tipe_lokasi') ?>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                        <input type="text" name="latitude" id="latitude" class="form-control"
                                                value="<?= old('latitude') ?>" required placeholder="-6.200000">
                                        <div class="text-small text-danger">
                                            <?= validation_show_error('latitude') ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                        <input type="text" name="longitude" id="longitude" class="form-control"
                                                value="<?= old('longitude') ?>" required placeholder="106.816666">
                                        <div class="text-small text-danger">
                                            <?= validation_show_error('longitude') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="radius">Radius Toleransi (Meter) <span class="text-danger">*</span></label>
                                        <input type="number" name="radius" id="radius" class="form-control"
                                                value="<?= old('radius') ?>" required placeholder="Misal: 50">
                                        <div class="text-small text-danger">
                                            <?= validation_show_error('radius') ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="zona_waktu">Zona Waktu <span class="text-danger">*</span></label>
                                        <input type="text" name="zona_waktu" id="zona_waktu" class="form-control"
                                                value="<?= old('zona_waktu') ?>" required placeholder="WIB / WITA / WIT">
                                        <div class="text-small text-danger">
                                            <?= validation_show_error('zona_waktu') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="jam_masuk">Jam Masuk <span class="text-danger">*</span></label>
                                        <input type="time" name="jam_masuk" id="jam_masuk" class="form-control"
                                                value="<?= old('jam_masuk') ?>" required>
                                        <div class="text-small text-danger">
                                            <?= validation_show_error('jam_masuk') ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="jam_pulang">Jam Pulang <span class="text-danger">*</span></label>
                                        <input type="time" name="jam_pulang" id="jam_pulang" class="form-control"
                                                value="<?= old('jam_pulang') ?>" required>
                                        <div class="text-small text-danger">
                                            <?= validation_show_error('jam_pulang') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                                    <a href="<?= base_url('admin/lokasipresensi')?>" class="btn btn-secondary">Batal</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>