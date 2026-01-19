<?php 
    // Cek apakah tanggal yang sedang dipilih adalah hari kerja
    $hariDipilih = date('N', strtotime($selectedDate)); 
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('gagal')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('gagal') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Rekap Harian</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Filter Data</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="tanggal_input">Pilih Tanggal Rekap:</label>
                            <input type="date" id="tanggal_input" class="form-control" value="<?= $selectedDate ?>">
                        </div>
                        <div class="col-md-9 text-right">
                            <?php if ($hariDipilih < 7) : ?>
                                <a href="<?= base_url('admin/rekap-presensi/sinkronisasi-alpa/' . $selectedDate) ?>" 
                                   class="btn btn-danger mt-4" onclick="return confirm('Jalankan sinkronisasi alpa?')">
                                    <i class="fas fa-user-times"></i> Sinkronkan Alpa
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('admin/export-harian/' . $selectedDate) ?>" class="btn btn-success mt-4">
                                <i class="fas fa-file-excel"></i> Export Harian
                            </a>
                        </div>
                    </div>

                    <h4 class="mt-4">Rekap Presensi Tanggal: <strong><?= date('d F Y', strtotime($selectedDate)) ?></strong></h4>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Total Jam Kerja</th>
                                    <th>Status</th>
                                    <th>Keterlambatan</th>
                                    <th>Lokasi Presensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (empty($rekapData)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-danger py-4">
                                            ⚠️ Tidak ada data presensi pada tanggal <strong><?= date('d F Y', strtotime($selectedDate)) ?></strong>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rekapData as $data): ?>
                                        <?php 
                                            $isAlpa = (isset($data['tipe_data']) && $data['tipe_data'] === 'ALPA') || ($data['jam_masuk'] === '-');
                                            $status_masuk = strtolower($data['status_masuk'] ?? '');
                                        ?>
                                        <tr <?= $isAlpa ? 'style="background-color: rgba(255,0,0,0.05);"' : '' ?>>
                                            <td><?= $no++ ?></td>
                                            <td><strong><?= $data['nm_karyawan'] ?? '-' ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($data['tanggal_masuk'] ?? $selectedDate)) ?></td>
                                            <td class="text-center">
                                                <span class="badge <?= $isAlpa ? 'bg-danger' : 'bg-success' ?>"><?= $isAlpa ? 'ALPA' : 'HADIR' ?></span>
                                            </td>
                                            <td class="text-center"><?= $isAlpa ? '-' : substr($data['jam_masuk'], 0, 5) ?></td>
                                            <td class="text-center">
                                                <?php if ($isAlpa): ?> - 
                                                <?php elseif (empty($data['jam_keluar']) || $data['jam_keluar'] === '00:00:00'): ?>
                                                    <span class="badge bg-warning">Belum Pulang</span>
                                                <?php else: ?>
                                                    <?= substr($data['jam_keluar'], 0, 5) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= ($isAlpa || $data['total_jam_kerja'] === 'N/A') ? '-' : substr($data['total_jam_kerja'], 0, 5) ?></td>
                                            
                                            <td class="text-center">
                                                <?php 
                                                    $color = 'secondary';
                                                    if ($isAlpa) $color = 'danger';
                                                    elseif ($status_masuk === 'terlambat') $color = 'warning text-dark';
                                                    elseif ($status_masuk === 'on time' || $status_masuk === 'tepat waktu') $color = 'success';
                                                ?>
                                                <span class="badge bg-<?= $color ?>">
                                                    <?= $isAlpa ? 'Alpa' : esc($data['status_masuk'] ?? 'N/A') ?>
                                                </span>
                                            </td>

                                            <td class="text-center"><?= $isAlpa ? '-' : $data['keterlambatan'] ?></td>
                                            <td>
                                                <?php if ($isAlpa): ?>
                                                    <i class="text-danger fas fa-exclamation-triangle mr-1"></i> Tanpa Keterangan
                                                <?php else: ?>
                                                    <?= $data['nama_lokasi'] ?? '-' ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tanggalInput = document.getElementById('tanggal_input');
        if (tanggalInput) {
            tanggalInput.addEventListener('change', function() {
                window.location.href = '<?= base_url('admin/rekap-harian') ?>/' + this.value;
            });
        }
    });
</script>