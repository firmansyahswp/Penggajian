<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Potongan Gaji</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h5 class="m-0">Data Potongan Gaji</h5>
                <div class="card-tools">
                    <a href="<?= base_url('admin/potongan/create') ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Tambah Potongan
                    </a>
                </div>
            </div>
            <div class="card-body">

                <form action="<?= base_url('admin/potongan') ?>" method="get" class="form-inline mb-3">
                    <label for="periode" class="mr-2">Filter Periode Potongan:</label>
                    <select name="periode" id="periode" class="form-control mr-3">
                        <?php foreach ($listPeriode as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($value == $selectedPeriode) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                </form>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Karyawan</th>
                            <th>Periode</th>
                            <th>Jenis Potongan</th>
                            <th>Jumlah (Rp)</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if (empty($list_potongan)): ?>
                            <tr><td colspan="7" class="text-center">Tidak ada potongan tercatat untuk periode ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($list_potongan as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($row['nm_karyawan'] ?? 'N/A') ?> (<?= esc($row['nip'] ?? '') ?>)</td>
                                <td><?= esc($row['periode_gaji']) ?></td>
                                <td><span class="badge bg-danger"><?= esc($row['jenis_potongan']) ?></span></td>
                                <td>Rp. <?= number_format(esc($row['besar_potongan']), 0, ',', '.') ?></td>
                                <td><?= esc($row['keterangan']) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/potongan/edit/' . esc($row['id'])) ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/potongan/delete/' . esc($row['id'])) ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus potongan ini?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
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