<div class="content-header"></div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">Riwayat Pengajuan Anda</h5>
                        <div class="card-tools">
                            <a href="<?= base_url('pegawai/pinjaman/ajukan') ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Ajukan Baru
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>

                        <?php if (empty($permohonan)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Anda belum pernah mengajukan pinjaman.
                            </div>
                        <?php else: ?>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal Ajuan</th>
                                        <th>Besar Pinjaman</th>
                                        <th>Keterangan</th>
                                        <th>Status Pinjaman Akhir</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($permohonan as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime(esc($row['timedate']))) ?></td>
                                        <td>Rp. <?= number_format(esc($row['besar_pinjaman']), 0, ',', '.') ?></td>
                                        <td><?= esc($row['keterangan']) ?></td>
                                        <td>
                                            <?php 
                                                // Key ini dijamin ada karena sudah diperbaiki di Controller
                                                $status = esc($row['status_pinjaman_akhir']); 
                                                $badge_class = 'badge-secondary';
                                                
                                                if ($status == 'PENDING') $badge_class = 'bg-warning';
                                                if ($status == 'AKTIF') $badge_class = 'bg-danger'; 
                                                if ($status == 'LUNAS') $badge_class = 'bg-success'; 
                                                if ($status == 'DISETUJUI') $badge_class = 'bg-info';
                                                if ($status == 'DITOLAK') $badge_class = 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $badge_class ?>"><?= $status ?></span>
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