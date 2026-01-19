<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rekap Gaji</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h5 class="m-0">Rekap Gaji Periode: <span class="badge badge-primary"><?= date('F Y', strtotime($selectedPeriode . '-01')) ?></span></h5>
            </div>
            <div class="card-body">

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?= form_open(base_url('admin/gaji/rekap'), ['method' => 'get', 'class' => 'form-inline mb-3']) ?>
                    <label for="periode" class="mr-2">Periode:</label>
                    <select name="periode" id="periode" class="form-control mr-3">
                        <?php foreach ($listPeriode as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($value == $selectedPeriode) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label for="q" class="mr-2">Cari Nama/NIP:</label>
                    <input type="text" name="q" id="q" class="form-control mr-3" placeholder="Nama atau NIP" value="<?= esc($keyword ?? '') ?>">
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                    <a href="<?= base_url('admin/gaji/rekap') ?>" class="btn btn-secondary ml-2">Reset</a>
                <?= form_close() ?>
                
                <hr>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NIP</th>
                            <th>Nama Karyawan</th>
                            <th>Gaji Bruto (Rp)</th>
                            <th>Potongan Wajib (Rp)</th>
                            <th>Gaji Bersih (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if (empty($list_rekap)): ?>
                            <tr><td colspan="7" class="text-center">Tidak ada data gaji tercatat untuk periode ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($list_rekap as $row): 
                                $potonganWajib = $row['pph21_sebulan'] + $row['potongan_bpjs_tk'] + $row['potongan_bpjs_kes'];
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($row['nip'] ?? '') ?></td>
                                <td><?= esc($row['nm_karyawan'] ?? 'N/A') ?></td>
                                <td><?= number_format(esc($row['bruto']), 0, ',', '.') ?></td>
                                <td><?= number_format($potonganWajib, 0, ',', '.') ?></td>
                                <td><?= number_format(esc($row['gaji_bersih']), 0, ',', '.') ?></td>
                                <td>
                                    <a href="<?= base_url('admin/gaji/export/' . esc($row['kd_karyawan']) . '/' . esc($row['periode_pph21'])) ?>" class="btn btn-sm btn-info" title="Export Slip PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="<?= base_url('admin/gaji/delete/' . esc($row['no_penggajian'])) ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('ANDA AKAN MENGHAPUS DATA GAJI INI DAN RECORD ANGSURAN PINJAMAN OTOMATIS. Yakin ingin menghapus?')" title="Hapus (Re-run)">
                                        <i class="fas fa-trash"></i>
                                    </a>
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