<div class="content-wrapper">
    <section class="content-header">
        <h1><?= $title ?></h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php $errors = session()->getFlashdata('errors'); if ($errors): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Form Pengajuan Baru</h3>
                </div>
                
                <form action="<?= base_url('pegawai/ketidakhadiran/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label for="tanggal">Tanggal Izin:</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" 
                                value="<?= old('tanggal') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="keterangan">Keterangan (Izin/Sakit):</label>
                            <select name="keterangan" id="keterangan" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="Izin" <?= old('keterangan') === 'Izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="Sakit" <?= old('keterangan') === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi:</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required><?= old('deskripsi') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="file_bukti">File Bukti (Foto/PDF):</label>
                            <input type="file" name="file_bukti" id="file_bukti" class="form-control-file" required>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengajuan</button>
                        <a href="<?= base_url('pegawai/ketidakhadiran') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>