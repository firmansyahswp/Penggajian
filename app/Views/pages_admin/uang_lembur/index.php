<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Uang Lembur</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h5 class="m-0 card-title">Data Uang Lembur Manual</h5>
                <div class="card-tools">
                    <a href="<?= base_url('admin/lembur/create') ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Tambah Uang Lembur
                    </a>
                </div>
            </div>
            <div class="card-body">

                <form action="<?= base_url('admin/lembur') ?>" method="get" class="form-inline mb-3">
                    <div class="form-group mr-3 mb-2 mb-md-0">
                        <label for="periode" class="mr-2">Filter Periode Gaji:</label>
                        <select name="periode" id="periode" class="form-control">
                            <?php foreach ($listPeriode as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($value == $selectedPeriode) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 mb-md-0"><i class="fas fa-filter"></i> Filter</button>
                </form>
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fas fa-times-circle"></i> <?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 5%">No.</th>
                                <th style="width: 20%">Nama Karyawan</th>
                                <th style="width: 10%">NIP</th>
                                <th style="width: 15%">Periode Gaji</th>
                                <th style="width: 15%" class="text-right">Jumlah Lembur (Rp)</th>
                                <th>Keterangan</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php if (empty($list_lembur)): ?>
                                <tr><td colspan="7" class="text-center text-muted">⚠️ Tidak ada data uang lembur tercatat untuk periode ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($list_lembur as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($row['nm_karyawan'] ?? 'N/A') ?></td>
                                    <td><?= esc($row['nip'] ?? '') ?></td>
                                    <td><span class="badge bg-info"><?= esc($row['periode_gaji']) ?></span></td>
                                    <td class="text-right"><strong>Rp. <?= number_format(esc($row['besar_lembur']), 0, ',', '.') ?></strong></td>
                                    <td><?= esc($row['keterangan']) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= base_url('admin/lembur/edit/' . esc($row['id'])) ?>" class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('admin/lembur/delete/' . esc($row['id'])) ?>" class="btn btn-danger" 
                                                onclick="return confirm('Yakin ingin menghapus data lembur <?= esc($row['nm_karyawan']) ?>?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                </div>
        </div>
    </div>
</div>