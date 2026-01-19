<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji <?= esc($gaji['kd_karyawan']) ?> - <?= esc($periode_gaji) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        .container { width: 90%; margin: 20px auto; border: 1px solid #ccc; padding: 15px; }
        h4 { text-align: center; margin-bottom: 5px; }
        .header-info, .section-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .header-info td { padding: 4px 0; }
        .table-potongan th, .table-potongan td { border: 1px solid #000; padding: 6px; text-align: left; }
        .table-potongan th { background-color: #f2f2f2; }
        .summary-box { width: 40%; float: right; border: 1px solid #000; padding: 10px; margin-top: 10px; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

<div class="container">
    <h4>SLIP GAJI KARYAWAN</h4>
    <p style="text-align: center; margin-top: 0;">
    Periode: 
    <p style="text-align: center; margin-top: 0;">
    Periode: 
    <?php
            $bulanIndo = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            
            $time = strtotime($gaji['periode_pph21']);
            $bln = date('m', $time);
            $thn = date('Y', $time);
            
            echo (isset($bulanIndo[$bln]) ? $bulanIndo[$bln] : $bln) . ' ' . $thn;
        ?>
    </p>
    
    <table class="header-info">
        <tr>
            <td style="width: 50%;">
                <small>NIP: <?= esc($karyawan['nip'] ?? '-') ?></small><br>
                <small>Nama: <?= esc($karyawan['nm_karyawan'] ?? 'N/A') ?></small><br>
                <small>Jabatan: <?= esc($karyawan['nm_jabatan'] ?? '-') ?></small>
            </td>
            <td style="width: 50%; text-align: right;">
                <small>Tgl. Rekap: <?= date('d-m-Y') ?></small>
            </td>
        </tr>
    </table>
    
    <div style="border-top: 2px solid #333; margin-bottom: 15px;"></div>

    <table class="table-potongan">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center;">A. PENDAPATAN / GAJI KOTOR (BRUTO)</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Gaji Pokok</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['gaji_pokok']), 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Transport</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['tunj_transport']), 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Makan</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['tunj_makan']), 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Lembur/Bonus</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['total_lembur'] + $gaji['total_bonus']), 0, ',', '.') ?></td></tr>
            <tr style="font-weight: bold; background-color: #e0e0e0;">
                <td>TOTAL GAJI BRUTO</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['bruto']), 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <table class="table-potongan">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center;">B. POTONGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="2" style="background-color: #f9f9f9; font-weight: bold;">Potongan Wajib (Pajak & BPJS):</td></tr>
            <tr><td>PPh Pasal 21</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['pph21_sebulan']), 0, ',', '.') ?></td></tr>
            <tr><td>BPJS Ketenagakerjaan (JHT/JP)</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['potongan_bpjs_tk']), 0, ',', '.') ?></td></tr>
            <tr><td>BPJS Kesehatan</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['potongan_bpjs_kes']), 0, ',', '.') ?></td></tr>
            
            <tr><td colspan="2" style="background-color: #f9f9f9; font-weight: bold;">Potongan Lain-lain:</td></tr>
            <tr><td>Angsuran Pinjaman</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['total_pinjaman']), 0, ',', '.') ?></td></tr>
            <tr><td>Potongan Lain (Denda/Koperasi)</td><td style="text-align: right;">Rp. <?= number_format(esc($gaji['total_potongan_lain']), 0, ',', '.') ?></td></tr>
            <tr>
                <td>Potongan Alpa (<?= $gaji['jumlah_alpa'] ?? 0 ?> Hari)</td>
                <td>: Rp <?= number_format($gaji['potongan_alpa'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Denda Terlambat (<?= esc($gaji['jumlah_terlambat'] ?? 0) ?>x)</td>
                <td style="text-align: right;">
                    Rp. <?= number_format(esc($gaji['potongan_terlambat'] ?? 0), 0, ',', '.') ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="clearfix">
        <div class="summary-box">
            <h5 style="margin-top: 0;">TOTAL GAJI DITERIMA</h5>
            <div style="font-size: 14pt; font-weight: bold; color: green; text-align: right;">
                Rp. <?= number_format(esc($gaji['gaji_bersih']), 0, ',', '.') ?>
            </div>
        </div>
    </div>
    
</div>

</body>
</html>