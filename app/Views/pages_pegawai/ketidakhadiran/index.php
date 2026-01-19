<div class="content-wrapper">
    <section class="content-header">
        <h1><?= $title ?></h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header">
                    <a href="<?= base_url('pegawai/ketidakhadiran/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajukan
                    </a>
                    <a href="<?= base_url('pegawai/ketidakhadiran/export') ?>" class="btn btn-success float-right">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Izin</th>
                                    <th>Keterangan</th>
                                    <th>Deskripsi</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $no = 1; 
                            if (!empty($list_izin)): 
                                foreach ($list_izin as $izin): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('Y-m-d', strtotime($izin['tanggal'])) ?></td>
                                    <td><?= $izin['keterangan'] ?? '-' ?></td>
                                    <td><?= $izin['deskripsi'] ?? '-' ?></td>
                                    <td>
                                        <?php if (!empty($izin['file'])): ?>
                                            <a href="<?= base_url('files/izin/' . $izin['file']) ?>" 
                                                class="btn btn-sm btn-info" 
                                                target="_blank"
                                                download> Download
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $izin['status_pengajuan'] ?? 'N/A';
                                            $badge = $status === 'Approved' ? 'success' : ($status === 'Rejected' ? 'danger' : 'warning');
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
                                    </td>
                                    <td>
                                        <?php if (($izin['status_pengajuan'] ?? '') === 'Pending'): ?>
                                            <a href="<?= base_url('pegawai/ketidakhadiran/edit/' . $izin['id']) ?>" 
                                                class="btn btn-sm btn-warning">
                                                Edit
                                            </a>
                                            <a href="<?= base_url('pegawai/ketidakhadiran/delete/' . $izin['id']) ?>" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                                Delete
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-primary" disabled>Disetujui</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                endforeach; 
                            else: 
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada pengajuan ketidakhadiran.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>