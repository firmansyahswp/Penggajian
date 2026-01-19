<div class="content-header"></div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header"><h5 class="m-0">Formulir Pengajuan Pinjaman Baru</h5></div>
            <div class="card-body">

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?= form_open('pegawai/pinjaman/saveAjuan') ?>

                    <div class="form-group">
                        <label for="besar_pinjaman">Besar Pinjaman (Rp)</label>
                        <input type="number" name="besar_pinjaman" id="besar_pinjaman" class="form-control <?= (session('errors.besar_pinjaman') ? 'is-invalid' : '') ?>" 
                               value="<?= old('besar_pinjaman') ?>" placeholder="Masukkan jumlah pinjaman yang diajukan" required>
                        <?php if (session('errors.besar_pinjaman')): ?>
                            <div class="invalid-feedback"><?= session('errors.besar_pinjaman') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan / Tujuan Pinjaman</label>
                        <textarea name="keterangan" id="keterangan" class="form-control <?= (session('errors.keterangan') ? 'is-invalid' : '') ?>" rows="3" 
                                  placeholder="Jelaskan secara singkat tujuan pinjaman"><?= old('keterangan') ?></textarea>
                        <?php if (session('errors.keterangan')): ?>
                            <div class="invalid-feedback"><?= session('errors.keterangan') ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Ajukan Pinjaman</button>
                    <a href="<?= base_url('pegawai/pinjaman/status') ?>" class="btn btn-secondary">Batal</a>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>