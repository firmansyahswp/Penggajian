<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Angsuran Pinjaman</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header"><h5 class="m-0">Manajemen Pinjaman yang Sudah Dicairkan</h5></div>
            <div class="card-body">

                <form action="<?= base_url('admin/angsuran') ?>" method="get" class="form-inline mb-3">
                    <label for="periode" class="mr-2">Periode Cair:</label>
                    <select name="periode" id="periode" class="form-control mr-3">
                        <?php 
                        $selectedYear = substr($selectedBulanTahun, 0, 4);
                        $selectedMonth = substr($selectedBulanTahun, 5, 2);
                        foreach ($listTahun as $y):
                            foreach ($listBulan as $m => $namaBulan):
                                $value = $y . '-' . $m;
                                $selected = ($value == $selectedBulanTahun) ? 'selected' : '';
                        ?>
                                <option value="<?= $value ?>" <?= $selected ?>><?= $namaBulan ?> <?= $y ?></option>
                        <?php endforeach; endforeach; ?>
                    </select>
                    
                    <label for="q" class="mr-2">Cari Karyawan:</label>
                    <input type="text" name="q" id="q" class="form-control mr-3" placeholder="Nama/NIP" value="<?= esc($keyword) ?>">
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                </form>

                <hr>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <?php if (empty($list_pinjaman)): ?>
                    <div class="alert alert-info"><i class="fas fa-info-circle"></i> Tidak ada pinjaman yang dicairkan pada periode ini.</div>
                <?php else: ?>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tgl. Pencairan</th>
                                <th>Nama Karyawan</th>
                                <th>Total Hutang</th>
                                <th>Sisa Saldo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($list_pinjaman as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime(esc($row['tanggal_bayar']))) ?></td>
                                <td><?= esc($row['nm_karyawan'] ?? 'N/A') ?> (NIP: <?= esc($row['nip']) ?>)</td>
                                <td>Rp. <?= number_format(esc($row['total_hutang']), 0, ',', '.') ?></td>
                                <td>
                                    <?php 
                                        $sisa = $row['sisa_saldo'];
                                        $color = ($sisa > 0) ? 'text-danger' : 'text-success';
                                    ?>
                                    <strong class="<?= $color ?>">
                                        Rp. <?= number_format($sisa, 0, ',', '.') ?>
                                    </strong>
                                </td>
                                <td><span class="badge bg-<?= ($row['status_lunas'] == 'LUNAS' ? 'success' : 'warning') ?>"><?= esc($row['status_lunas']) ?></span></td>
                                <td>
                                    <a href="<?= base_url('admin/angsuran/detail/' . $row['id_permohonan']) ?>" class="btn btn-sm btn-info" title="Lihat Riwayat & Bayar Manual">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>