<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('pegawai/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Riwayat Gaji</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h5 class="m-0">Daftar Slip Gaji yang Sudah Diproses</h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (empty($riwayat_gaji)): ?>
                    <div class="alert alert-warning">Belum ada slip gaji yang diproses untuk Anda.</div>
                <?php else: ?>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Periode Gaji</th>
                                <th>Gaji Kotor (Bruto)</th>
                                <th>Total Potongan</th>
                                <th>Gaji Bersih</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($riwayat_gaji as $gaji): ?>
                                <?php 
                                    $totalPotongan = $gaji['total_pinjaman'] + $gaji['total_potongan_lain'] + $gaji['pph21_sebulan'] + $gaji['potongan_bpjs_tk'] + $gaji['potongan_bpjs_kes'];
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('F Y', strtotime($gaji['periode_pph21'])) ?></td>
                                    <td>Rp. <?= number_format($gaji['bruto'], 0, ',', '.') ?></td>
                                    <td>Rp. <?= number_format($totalPotongan, 0, ',', '.') ?></td>
                                    <td>Rp. <?= number_format($gaji['gaji_bersih'], 0, ',', '.') ?></td>
                                    <td>
                                        <a href="<?= base_url('pegawai/gaji/detail/' . esc($gaji['no_penggajian'])) ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="<?= base_url('pegawai/gaji/download/' . esc($gaji['no_penggajian'])) ?>" class="btn btn-sm btn-success" title="Download PDF">
                                            <i class="fas fa-download"></i> PDF
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