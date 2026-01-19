<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= $title ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Periode Mingguan</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('admin/rekap-mingguan') ?>" method="get">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Pilih Minggu</label>
                                    <input type="week" class="form-control" name="minggu" value="<?= $filterMinggu ?>" required>                                    <small class="text-muted">Periode terpilih: <strong><?= date('d M Y', strtotime($tglAwal)) ?></strong> s/d <strong><?= date('d M Y', strtotime($tglAkhir)) ?></strong></small>
                                </div>
                                <div class="col-md-4 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Tampilkan
                                    </button>
                                    <a href="<?= base_url('admin/rekap-mingguan/export?tgl_awal='.$tglAwal.'&tgl_akhir='.$tglAkhir) ?>" class="btn btn-success w-100">
                                        Export Excel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Total Hadir</th>
                                    <th>Total Alpa</th>
                                    <th>Total Jam Kerja</th>
                                    <th>Total Terlambat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rekapMingguan)) : ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Data tidak ditemukan untuk periode <?= date('d/m/Y', strtotime($tglAwal)) ?> - <?= date('d/m/Y', strtotime($tglAkhir)) ?></td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no = 1; foreach ($rekapMingguan as $rekap) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= $rekap['nm_karyawan'] ?></td>
                                            <td class="text-center"><?= $rekap['total_kehadiran'] ?> Hari</td>
                                            <td class="text-center text-danger"><?= $rekap['total_alpa'] ?> Hari</td>
                                            <td class="text-center"><?= $rekap['total_jam_kerja_format'] ?></td>
                                            <td class="text-center">
                                                <?php if($rekap['total_terlambat_menit'] > 0): ?>
                                                    <span class="badge bg-danger"><?= $rekap['total_terlambat_menit'] ?> Menit</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">0 Menit</span>
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
    </div>
</div>