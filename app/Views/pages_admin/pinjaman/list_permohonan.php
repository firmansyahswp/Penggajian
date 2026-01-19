<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Persetujuan Pinjaman</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">Permohonan Pinjaman Menunggu Persetujuan</h5>
                    </div>
                    <div class="card-body">
                        
                        <?php // Menampilkan Pesan Sukses/Error ?>
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <?php if (empty($list_permohonan)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle"></i> Tidak ada permohonan pinjaman yang menunggu persetujuan saat ini.
                            </div>
                        <?php else: ?>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal Ajuan</th>
                                        <th>Nama Karyawan</th>
                                        <th>NIP</th>
                                        <th>Besar Pinjaman</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($list_permohonan as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime(esc($row['timedate']))) ?></td>
                                        <td><?= esc($row['nm_karyawan'] ?? 'N/A') ?></td> 
                                        <td><?= esc($row['nip'] ?? 'N/A') ?></td>
                                        <td>Rp. <?= number_format(esc($row['besar_pinjaman']), 0, ',', '.') ?></td>
                                        <td><span class="badge bg-warning"><?= esc($row['status_pengajuan']) ?></span></td>
                                        <td>
                                            <form action="<?= base_url('admin/pinjaman/approve/' . esc($row['id'])) ?>" method="post" style="display:inline;" onsubmit="return confirm('Anda yakin SETUJUI pinjaman ini? Hutang akan dicatat di ledger.')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                            </form>

                                            <form action="<?= base_url('admin/pinjaman/reject/' . esc($row['id'])) ?>" method="post" style="display:inline;" onsubmit="return confirm('Anda yakin menolak pinjaman ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </form>
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
    </div>
</div>