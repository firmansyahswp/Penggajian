<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/lembur') ?>">Uang Lembur</a></li>
                    <li class="breadcrumb-item active"><?= esc($title) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-<?= ($lembur ? 'warning' : 'success') ?> card-outline">
            <div class="card-header"><h5 class="m-0">Form Input Uang Lembur</h5></div>
            <div class="card-body">

                <?php $errors = session()->getFlashdata('errors'); if ($errors): ?>
                    <div class="alert alert-warning">
                        <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>
                
                <?php 
                    // Tentukan aksi dan data default
                    $action = base_url('admin/lembur/store');
                    $isUpdate = $lembur ? true : false;
                    
                    $data_karyawan = $lembur ? esc($lembur['kd_karyawan']) : old('kd_karyawan');
                    $data_jumlah = $lembur ? esc($lembur['besar_lembur']) : old('besar_lembur');
                    $data_periode = $lembur ? esc($lembur['periode_gaji']) : (old('periode_gaji') ?? date('Y-m'));
                    $data_keterangan = $lembur ? esc($lembur['keterangan']) : old('keterangan');
                ?>

                <?= form_open($action) ?>
                    <?php if ($isUpdate): ?>
                        <input type="hidden" name="id" value="<?= esc($lembur['id']) ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="kd_karyawan">Pilih Pegawai <span class="text-danger">*</span></label>
                        <select name="kd_karyawan" id="kd_karyawan" class="form-control" required <?= ($isUpdate ? 'disabled' : '') ?>>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($list_karyawan as $karyawan): ?>
                                <option value="<?= esc($karyawan['id_karyawan']) ?>" <?= ($data_karyawan == $karyawan['id_karyawan'] ? 'selected' : '') ?>>
                                    <?= esc($karyawan['nm_karyawan']) ?> (NIP: <?= esc($karyawan['nip']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($isUpdate): ?>
                            <input type="hidden" name="kd_karyawan" value="<?= $data_karyawan ?>">
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="periode_gaji">Periode Gaji <span class="text-danger">*</span></label>
                        <input type="month" name="periode_gaji" id="periode_gaji" class="form-control" 
                               value="<?= $data_periode ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="besar_lembur">Jumlah Uang Lembur (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="besar_lembur" id="besar_lembur" class="form-control" 
                               value="<?= $data_jumlah ?>" placeholder="Masukkan total uang lembur" required min="0">
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan Tambahan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2"><?= $data_keterangan ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-<?= ($isUpdate ? 'warning' : 'success') ?>">
                        <i class="fas fa-save"></i> <?= ($isUpdate ? 'Perbarui Lembur' : 'Simpan Lembur') ?>
                    </button>
                    <a href="<?= base_url('admin/lembur') ?>" class="btn btn-secondary">Kembali</a>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>