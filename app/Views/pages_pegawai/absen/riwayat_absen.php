<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('pegawai/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Absen</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h5 class="m-0">Riwayat Absensi Anda</h5>
            </div>
            <div class="card-body">

                <form action="<?= base_url('pegawai/data_absen') ?>" method="get" class="form-inline mb-3">
                    <label for="periode" class="mr-2">Filter Bulan:</label>
                    <select name="periode" id="periode" class="form-control mr-3">
                        <?php foreach ($listPeriode as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($value == $selectedPeriode) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                </form>
                
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status Masuk</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if (empty($riwayat_absen)): ?>
                            <tr><td colspan="7" class="text-center">Tidak ada riwayat absen tercatat untuk periode ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($riwayat_absen as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime(esc($row['tanggal_masuk']))) ?></td>
                                <td><?= esc($row['nama_lokasi'] ?? 'Tidak Tercatat') ?></td>
                                <td><?= esc($row['jam_masuk'] ?? '-') ?></td>
                                <td><?= esc($row['jam_keluar'] ?? '-') ?></td>
                                <td><span class="badge bg-<?= ($row['status_masuk'] == 'Tepat Waktu' ? 'success' : 'warning') ?>"><?= esc($row['status_masuk'] ?? 'N/A') ?></span></td>
                                <td><?= esc($row['keterangan'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>