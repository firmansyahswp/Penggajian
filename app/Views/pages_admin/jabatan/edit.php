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
                            
                            <form action="<?= base_url('admin/jabatan/update/' . $jabatan['kd_jabatan']) ?>" method="post">                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label for="nm_jabatan">Nama Jabatan</label>
                                    
                                    <input type="hidden" name="kd_jabatan" value="<?= $jabatan['kd_jabatan'] ?>">
                                    
                                    <input type="text" name="nm_jabatan" id="nm_jabatan" class="form-control" 
                                           value="<?= old('nm_jabatan', $jabatan['nm_jabatan']) ?>">
                                    
                                    <div class="text-small text-danger">
                                        <?= service('validation')->showError('nm_jabatan') ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="gaji_pokok">Gaji Pokok</label>
                                    <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control" 
                                           value="<?= old('gaji_pokok', $jabatan['gaji_pokok']) ?>">
                                    <div class="text-small text-danger">
                                        <?= service('validation')->showError('gaji_pokok') ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="uang_transport">Tunjangan Transport</label>
                                    <input type="number" name="uang_transport" id="uang_transport" class="form-control" 
                                           value="<?= old('uang_transport', $jabatan['uang_transport']) ?>">
                                    <div class="text-small text-danger">
                                        <?= service('validation')->showError('uang_transport') ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="uang_makan">Uang Makan</label>
                                    <input type="number" name="uang_makan" id="uang_makan" class="form-control" 
                                           value="<?= old('uang_makan', $jabatan['uang_makan']) ?>">
                                    <div class="text-small text-danger">
                                        <?= service('validation')->showError('uang_makan') ?>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">Update</button>
                                <a href="<?= base_url('admin/jabatan')?>" class="btn btn-warning">Kembali</a>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>