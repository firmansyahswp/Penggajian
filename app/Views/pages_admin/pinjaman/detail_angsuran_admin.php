<div class="content-header"></div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-primary">
                    <div class="card-header"><h3 class="card-title">Informasi Pinjaman</h3></div>
                    <div class="card-body">
                        <table class="table">
                            <tr><th>Nama Karyawan</th><td><?= esc($karyawan['nm_karyawan'] ?? 'N/A') ?></td></tr>
                            <tr><th>ID Permohonan</th><td><?= esc($hutang_awal['id_permohonan']) ?></td></tr>
                            <tr><th>Tanggal Cair</th><td><?= date('d-m-Y H:i', strtotime(esc($hutang_awal['tanggal_bayar']))) ?></td></tr>
                            <tr><th>Total Hutang Awal</th><td>Rp. <?= number_format(esc($total_pinjaman), 0, ',', '.') ?></td></tr>
                            <tr><th>Status Akhir</th><td><span class="badge bg-<?= ($sisa_saldo == 0 ? 'success' : 'warning') ?>"><?= ($sisa_saldo == 0 ? 'LUNAS' : 'AKTIF') ?></span></td></tr>
                            <tr><th>Sisa Saldo</th><td><strong class="text-danger">Rp. <?= number_format($sisa_saldo, 0, ',', '.') ?></strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <?php if ($sisa_saldo > 0): ?>
                <div class="card card-success">
                    <div class="card-header"><h3 class="card-title">Catat Pembayaran Manual</h3></div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('admin/angsuran/manual') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_permohonan" value="<?= esc($hutang_awal['id_permohonan']) ?>">

                            <div class="form-group">
                                <label>Tanggal Pembayaran</label>
                                <input type="date" name="tanggal_bayar" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Bayar (Max: Rp <?= number_format($sisa_saldo) ?>)</label>
                                <input type="number" name="besar_angsuran" class="form-control" placeholder="Masukkan jumlah angsuran" max="<?= (int)$sisa_saldo ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Keterangan (Opsional)</label>
                                <textarea name="keterangan" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Catat Pembayaran</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header"><h3 class="card-title">Riwayat Semua Transaksi</h3></div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Jumlah</th>
                                    <th>Tipe Transaksi</th>
                                    <th>Periode Gaji</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($riwayat_angsuran)): ?>
                                    <tr><td colspan="6" class="text-center">Belum ada riwayat transaksi.</td></tr>
                                <?php else: ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($riwayat_angsuran as $angsuran): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime(esc($angsuran['tanggal_bayar']))) ?></td>
                                        <td>
                                            <?php 
                                                // Tampilkan angka positif, tapi beri warna untuk hutang (DEBT)
                                                $jumlah = abs(esc($angsuran['besar_angsuran']));
                                                $class = (esc($angsuran['tipe_pembayaran']) == 'DEBT') ? 'text-danger' : 'text-success';
                                            ?>
                                            <strong class="<?= $class ?>">Rp. <?= number_format($jumlah, 0, ',', '.') ?></strong>
                                        </td>
                                        <td><span class="badge bg-<?= (esc($angsuran['tipe_pembayaran']) == 'DEBT' ? 'danger' : (esc($angsuran['tipe_pembayaran']) == 'POTONGAN_GAJI' ? 'primary' : 'info')) ?>"><?= esc($angsuran['tipe_pembayaran']) ?></span></td>
                                        <td><?= esc($angsuran['periode_gaji'] ?? '-') ?></td>
                                        <td><?= esc($angsuran['keterangan']) ?></td>
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