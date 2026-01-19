<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Hitung Gaji</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0">Pilih Periode Perhitungan Gaji</h5>
            </div>
            <div class="card-body">
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <?= form_open(base_url('admin/gaji/proses')) ?>

                    <div class="form-group">
                        <label for="periode_gaji">Periode Gaji Bulanan <span class="text-danger">*</span></label>
                        <select name="periode_gaji" id="periode_gaji" class="form-control" required>
                            <option value="">-- Pilih Bulan dan Tahun --</option>
                            <?php foreach ($listPeriode as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($value == date('Y-m')) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Pastikan data **Uang Lembur** dan **Potongan Lain** untuk periode ini sudah diinput.</small>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-calculator"></i> Mulai Proses Hitung Gaji
                    </button>
                <?= form_close() ?>

            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            **PENTING!** Proses perhitungan ini akan melewati (skip) karyawan yang sudah memiliki data gaji di periode yang sama.
            Jika ingin menghitung ulang (Re-run), hapus data gaji lama melalui menu **Rekap Gaji** terlebih dahulu.
        </div>
    </div>
</div>