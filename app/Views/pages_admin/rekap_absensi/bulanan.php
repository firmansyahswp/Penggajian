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
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Filter Rekap Bulanan</h3>
                </div>
                <div class="card-body">
                    <?php 
                        $selectedYear = date('Y', strtotime($selectedBulanTahun . '-01'));
                        $selectedMonth = date('m', strtotime($selectedBulanTahun . '-01'));
                    ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Pilih Bulan</label>
                            <select id="bulan_input" class="form-control">
                                <?php foreach ($listBulan as $monthValue => $monthName): ?>
                                    <option value="<?= $monthValue ?>" <?= $monthValue == $selectedMonth ? 'selected' : '' ?>>
                                        <?= $monthName ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pilih Tahun</label>
                            <select id="tahun_input" class="form-control">
                                <?php foreach ($listTahun as $tahun): ?>
                                    <option value="<?= $tahun ?>" <?= $tahun == $selectedYear ? 'selected' : '' ?>>
                                        <?= $tahun ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="button" id="btn_tampilkan" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            <a href="<?= base_url('admin/export-bulanan/' . $selectedBulanTahun) ?>" class="btn btn-success w-100">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h3 class="card-title">Data Kehadiran Periode: <strong><?= date('F Y', strtotime($selectedBulanTahun . '-01')) ?></strong></h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover text-nowrap">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 5%">No</th>
                                <th>Nama Pegawai</th>
                                <th>Total Hadir</th>
                                <th>Total Alpa</th>
                                <th>Total Jam Kerja</th>
                                <th>Total Terlambat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekapBulanan)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-danger">
                                        ⚠️ Tidak ada data presensi pada bulan **<?= date('F Y', strtotime($selectedBulanTahun . '-01')) ?>**
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($rekapBulanan as $data): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= $data['nm_karyawan'] ?? '-' ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?= $data['total_kehadiran'] ?> Hari</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger"><?= $data['total_alpa'] ?? 0 ?> Hari</span>
                                        </td>
                                        <td class="text-center"><?= $data['total_jam_kerja_format'] ?></td>
                                        <td class="text-center">
                                            <?php if ($data['total_terlambat_menit'] > 0): ?>
                                                <span class="badge bg-warning text-dark"><?= $data['total_terlambat_menit'] ?> Menit</span>
                                            <?php else: ?>
                                                <span class="badge bg-light">0 Menit</span>
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
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectBulan = document.getElementById('bulan_input');
        const selectTahun = document.getElementById('tahun_input');
        const btnTampilkan = document.getElementById('btn_tampilkan');
        
        function redirectToNewDate() {
            const bulan = selectBulan.value;
            const tahun = selectTahun.value;
            const newBulanTahun = tahun + '-' + bulan;
            window.location.href = '<?= base_url('admin/rekap-bulanan') ?>/' + newBulanTahun;
        }

        // Tampilkan data saat tombol diklik atau select diganti
        btnTampilkan.addEventListener('click', redirectToNewDate);
        selectBulan.addEventListener('change', redirectToNewDate);
        selectTahun.addEventListener('change', redirectToNewDate);
    });
</script>