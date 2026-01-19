<div class="content-header"></div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-<?= ($potongan ? 'warning' : 'success') ?> card-outline">
            <div class="card-header"><h5 class="m-0"><?= esc($title) ?></h5></div>
            <div class="card-body">

                <?php $errors = session()->getFlashdata('errors'); if ($errors): ?>
                    <div class="alert alert-warning">
                        <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>
                
                <?php 
                    // Tentukan aksi dan data default
                    $action = $potongan ? 'admin/potongan/update/' . esc($potongan['id']) : 'admin/potongan/store';
                    $data_karyawan = $potongan ? esc($potongan['kd_karyawan']) : old('kd_karyawan');
                    $data_jenis = $potongan ? esc($potongan['jenis_potongan']) : old('jenis_potongan');
                    $data_jumlah = $potongan ? esc($potongan['besar_potongan']) : old('besar_potongan');
                    $data_periode = $potongan ? esc($potongan['periode_gaji']) : (old('periode_gaji') ?? date('Y-m'));
                    $data_keterangan = $potongan ? esc($potongan['keterangan']) : old('keterangan');
                ?>

                <?= form_open($action) ?>

                    <div class="form-group">
                        <label for="kd_karyawan">Pilih Pegawai <span class="text-danger">*</span></label>
                        <select name="kd_karyawan" id="kd_karyawan" class="form-control" required <?= ($potongan ? 'disabled' : '') ?>>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($list_karyawan as $karyawan): ?>
                                <option value="<?= esc($karyawan['id_karyawan']) ?>" <?= ($data_karyawan == $karyawan['id_karyawan'] ? 'selected' : '') ?>>
                                    <?= esc($karyawan['nm_karyawan']) ?> (NIP: <?= esc($karyawan['nip']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($potongan): ?>
                            <input type="hidden" name="kd_karyawan" value="<?= $data_karyawan ?>">
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="jenis_potongan">Jenis Potongan <span class="text-danger">*</span></label>
                        <select name="jenis_potongan" id="jenis_potongan" class="form-control" required>
                            <option value="IURAN_KOPERASI" <?= ($data_jenis == 'IURAN_KOPERASI' ? 'selected' : '') ?>>Iuran Koperasi</option>
                            <option value="DENDA_ABSENSI" <?= ($data_jenis == 'DENDA_ABSENSI' ? 'selected' : '') ?>>Denda Absensi / Pelanggaran</option>
                            <option value="POTONGAN_LAIN" <?= ($data_jenis == 'POTONGAN_LAIN' ? 'selected' : '') ?>>Potongan Lain-lain</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="besar_potongan">Jumlah Potongan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="besar_potongan" id="besar_potongan" class="form-control" 
                               value="<?= $data_jumlah ?>" placeholder="Masukkan jumlah potongan" required min="1000">
                    </div>

                    <div class="form-group">
                        <label for="periode_gaji">Periode Gaji <span class="text-danger">*</span></label>
                        <input type="month" name="periode_gaji" id="periode_gaji" class="form-control" 
                               value="<?= $data_periode ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan Tambahan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2"><?= $data_keterangan ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-<?= ($potongan ? 'warning' : 'success') ?>">
                        <i class="fas fa-save"></i> <?= ($potongan ? 'Perbarui Potongan' : 'Simpan Potongan') ?>
                    </button>
                    <a href="<?= base_url('admin/potongan') ?>" class="btn btn-secondary">Kembali</a>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>