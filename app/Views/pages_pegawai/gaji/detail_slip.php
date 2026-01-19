<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Rincian Slip Gaji</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-calendar-alt mr-1"></i> 
                            Periode: <?= date('F Y', strtotime($gaji['periode_pph21'])) ?>
                        </h3>
                        <div class="card-tools">
                            <a href="<?= base_url('pegawai/gaji/download/' . esc($gaji['no_penggajian'])) ?>" class="btn btn-sm btn-success shadow-sm" title="Download PDF">
                                <i class="fas fa-file-pdf"></i> Download Slip
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-7">
                                <h5 class="text-primary border-bottom pb-2"><i class="fas fa-user-circle mr-2"></i>Informasi Pegawai</h5>
                                <div class="row mt-3">
                                    <div class="col-sm-4 text-muted">Nama Karyawan</div>
                                    <div class="col-sm-8 font-weight-bold">: <?= esc($karyawan['nm_karyawan'] ?? '-') ?></div>
                                    
                                    <div class="col-sm-4 text-muted">NIP</div>
                                    <div class="col-sm-8">: <?= esc($karyawan['nip'] ?? '-') ?></div>
                                    
                                    <div class="col-sm-4 text-muted">Jabatan</div>
                                    <div class="col-sm-8">: <?= esc($karyawan['nm_jabatan'] ?? '-') ?></div>
                                    
                                    <div class="col-sm-4 text-muted">Status PTKP</div>
                                    <div class="col-sm-8">: <span class="badge bg-info"><?= esc($karyawan['nama_ptkp'] ?? '-') ?></span></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="bg-light p-3 border rounded text-right">
                                    <span class="text-muted d-block text-uppercase small font-weight-bold">Total Gaji Diterima (Netto)</span>
                                    <h2 class="text-success font-weight-bold mb-0">
                                        Rp. <?= number_format(esc($gaji['gaji_bersih']), 0, ',', '.') ?>
                                    </h2>
                                    <small class="text-muted">No. Penggajian: <?= esc($gaji['no_penggajian']) ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm border">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th colspan="2" class="py-2"><i class="fas fa-plus-circle mr-2"></i>A. PENDAPATAN (BRUTO)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>Gaji Pokok</td><td class="text-right font-weight-bold">Rp. <?= number_format(esc($gaji['gaji_pokok']), 0, ',', '.') ?></td></tr>
                                            <tr><td>Tunjangan Transport</td><td class="text-right">Rp. <?= number_format(esc($gaji['tunj_transport']), 0, ',', '.') ?></td></tr>
                                            <tr><td>Tunjangan Makan</td><td class="text-right">Rp. <?= number_format(esc($gaji['tunj_makan']), 0, ',', '.') ?></td></tr>
                                            <tr><td>Uang Lembur</td><td class="text-right">Rp. <?= number_format(esc($gaji['total_lembur']), 0, ',', '.') ?></td></tr>
                                        </tbody>
                                        <tfoot class="bg-light font-weight-bold">
                                            <tr>
                                                <td class="text-primary text-uppercase">Total Bruto</td>
                                                <td class="text-right text-primary">Rp. <?= number_format(esc($gaji['bruto']), 0, ',', '.') ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm border">
                                        <thead class="bg-danger text-white">
                                            <tr>
                                                <th colspan="2" class="py-2"><i class="fas fa-minus-circle mr-2"></i>B. POTONGAN (DEDUCTIONS)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td class="text-muted italic" colspan="2">Potongan Kehadiran:</td></tr>
                                            <tr><td class="pl-4">Potongan Terlambat</td><td class="text-right text-danger">Rp. <?= number_format(esc($gaji['potongan_terlambat'] ?? 0), 0, ',', '.') ?></td></tr>
                                            <tr><td class="pl-4">Potongan Alpa/Tanpa Keterangan</td><td class="text-right text-danger">Rp. <?= number_format(esc($gaji['potongan_alpa'] ?? 0), 0, ',', '.') ?></td></tr>
                                            
                                            <tr><td class="text-muted italic" colspan="2">Potongan Wajib:</td></tr>
                                            <tr><td class="pl-4">PPh Pasal 21</td><td class="text-right text-danger">Rp. <?= number_format(esc($gaji['pph21_sebulan']), 0, ',', '.') ?></td></tr>
                                            <tr><td class="pl-4">BPJS Ketenagakerjaan</td><td class="text-right text-danger">Rp. <?= number_format(esc($gaji['potongan_bpjs_tk']), 0, ',', '.') ?></td></tr>
                                            <tr><td class="pl-4">BPJS Kesehatan</td><td class="text-right text-danger">Rp. <?= number_format(esc($gaji['potongan_bpjs_kes']), 0, ',', '.') ?></td></tr>
                                            
                                            <tr><td class="text-muted italic" colspan="2">Potongan Lainnya:</td></tr>
                                            <tr><td class="pl-4">Angsuran Pinjaman</td><td class="text-right text-danger">Rp. <?= number_format(esc($gaji['total_pinjaman']), 0, ',', '.') ?></td></tr>
                                            <tr><td class="pl-4">Denda / Lain-lain</td><td class="text-right text-danger">Rp. <?= number_format(esc($gaji['total_potongan_lain']), 0, ',', '.') ?></td></tr>
                                        </tbody>
                                        <?php 
                                            // Perhitungan total potongan diperbarui dengan alpa dan terlambat
                                            $totalPotongan = 
                                                ($gaji['pph21_sebulan'] ?? 0) + 
                                                ($gaji['potongan_bpjs_tk'] ?? 0) + 
                                                ($gaji['potongan_bpjs_kes'] ?? 0) + 
                                                ($gaji['total_pinjaman'] ?? 0) + 
                                                ($gaji['total_potongan_lain'] ?? 0) + 
                                                ($gaji['potongan_terlambat'] ?? 0) + 
                                                ($gaji['potongan_alpa'] ?? 0);
                                        ?>
                                        <tfoot class="bg-light font-weight-bold">
                                            <tr>
                                                <td class="text-danger text-uppercase">Total Potongan</td>
                                                <td class="text-right text-danger">Rp. <?= number_format($totalPotongan, 0, ',', '.') ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="text-muted small mb-0">*</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>