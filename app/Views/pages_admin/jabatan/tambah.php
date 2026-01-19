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
                            <?php if (session()->getFlashdata('gagal')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('gagal') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="<?= base_url('admin/jabatan/store') ?>">
                                <?= csrf_field() ?>
                                
                                <div class="form-group mb-3">
                                    <label for="nm_jabatan">Nama Jabatan</label>
                                    <input type="text" name="nm_jabatan" id="nm_jabatan" class="form-control" 
                                           value="<?= old('nm_jabatan') ?>" required> <div class="text-small text-danger">
                                        <?= service('validation')->showError('nm_jabatan') ?>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="gaji_pokok">Gaji Pokok</label>
                                    <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control"
                                           value="<?= old('gaji_pokok') ?>" required> <div class="text-small text-danger">
                                        <?= service('validation')->showError('gaji_pokok') ?>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="uang_transport">Tunjangan Transport</label>
                                    <input type="number" name="uang_transport" id="uang_transport" class="form-control"
                                           value="<?= old('uang_transport') ?>" required> <div class="text-small text-danger">
                                        <?= service('validation')->showError('uang_transport') ?>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="uang_makan">Uang Makan</label>
                                    <input type="number" name="uang_makan" id="uang_makan" class="form-control"
                                           value="<?= old('uang_makan') ?>" required> <div class="text-small text-danger">
                                        <?= service('validation')->showError('uang_makan') ?>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success">Simpan</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                    <a href="<?= base_url('admin/jabatan')?>" class="btn btn-warning">Kembali</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>