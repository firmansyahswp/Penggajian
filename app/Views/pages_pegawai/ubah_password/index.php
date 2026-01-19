<div class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h5 class="m-0">Form Ubah Password</h5>
            </div>
            <div class="card-body">

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                
                <?php $errors = session()->getFlashdata('errors'); if ($errors): ?>
                    <div class="alert alert-warning">
                        <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('ubah-password/update/' . session()->get('kd_user')) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="password_lama">Password Lama <span class="text-danger">*</span></label>
                        <input type="password" name="password_lama" id="password_lama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password_baru">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_baru" id="password_baru" class="form-control" required minlength="6">
                        <small class="form-text text-muted">Minimal 6 karakter.</small>
                    </div>

                    <div class="form-group">
                        <label for="konfirmasi_password">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> Perbarui Password
                    </button>
                    <a href="<?= base_url('pegawai/dashboard') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>