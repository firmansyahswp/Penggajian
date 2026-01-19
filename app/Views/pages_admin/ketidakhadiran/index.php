<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title ?></h1>
                </div>
            </div>
        </div>
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
                    <h3 class="card-title">Daftar Pengajuan Ketidakhadiran</h3>
                </div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <form action="<?= base_url('admin/ketidakhadiran') ?>" method="get">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" 
                                    placeholder="Cari Nama/NIP/Keterangan..." 
                                    value="<?= esc($keyword ?? '') ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                    <?php if ($keyword): ?>
                                    <a href="<?= base_url('admin/ketidakhadiran') ?>" class="btn btn-outline-warning">
                                        Reset
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>NIP</th>
                                    <th>Tanggal Izin</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>File Bukti</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php if (empty($list_izin)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($list_izin as $izin): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($izin['nm_karyawan']) ?></td>
                                    <td><?= esc($izin['nip']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($izin['tanggal'])) ?></td>
                                    <td><?= esc($izin['keterangan']) ?></td>
                                    
                                    <td>
                                        <?php 
                                            $status = $izin['status_pengajuan'] ?? 'N/A';
                                            
                                            if ($status === 'Approved') {
                                                $bg_class = 'bg-success'; 
                                            } elseif ($status === 'Rejected') {
                                                $bg_class = 'bg-danger'; 
                                            } else {
                                                $bg_class = 'bg-warning'; 
                                            }
                                        ?>
                                        <span 
                                            class="<?= $bg_class ?> text-white px-2 py-1 rounded" 
                                            style="font-size: 0.8em; white-space: nowrap;">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <?php if (!empty($izin['file'])): ?>
                                            <a href="<?= base_url('files/izin/' . $izin['file']) ?>" 
                                                class="btn btn-sm btn-info" 
                                                target="_blank"
                                                download>
                                                Download
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($izin['status_pengajuan'] === 'Pending'): ?>
                                            <a href="<?= base_url('admin/ketidakhadiran/updatestatus/' . $izin['id'] . '/approved') ?>" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pengajuan ini?')">Approved</a>
                                            <a href="<?= base_url('admin/ketidakhadiran/updatestatus/' . $izin['id'] . '/rejected') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pengajuan ini?')">Rejected</a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Review Selesai</button>
                                        <?php endif; ?>
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
    </section>
</div>