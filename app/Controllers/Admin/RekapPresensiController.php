<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PresensiModel;
use CodeIgniter\I18n\Time; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 

class RekapPresensiController extends BaseController
{
    // Pastikan Timezone diatur untuk akurasi tanggal/waktu server
    public function __construct()
    {
        // Ganti 'Asia/Jakarta' sesuai dengan zona waktu Anda (WIB/WITA/WIT)
        date_default_timezone_set('Asia/Jakarta'); 
    }

    /**
     * Menampilkan Rekap Presensi Harian
     * @param string|null $tanggal Tanggal rekap dalam format Y-m-d
     */
    public function index($tanggal = null)
    {
        $presensiModel = new PresensiModel();
        
        // Inisialisasi tanggal rekap. Jika $tanggal null, gunakan tanggal hari ini
        $tanggalRekap = $tanggal ?? date('Y-m-d'); 

        $dataPresensi = $presensiModel->getRekapHarian($tanggalRekap);

        $rekapData = $this->processPresensiData($dataPresensi);
        
        $data = [
            'title'          => 'Rekap Presensi Harian',
            'rekapData'      => $rekapData,
            'selectedDate'   => $tanggalRekap,
            'jamMasukKantor' => '08:00:00' 
        ];

        return view('layout_admin/head', $data)
            . view('layout_admin/sidebar')
            . view('pages_admin/rekap_absensi/harian', $data) 
            . view('layout_admin/footer');
    }

    /**
     * Menampilkan Rekap Presensi Mingguan (Fitur Baru)
     */
    public function mingguan()
    {
        $presensiModel = new PresensiModel();
        helper('cookie'); // Memastikan helper cookie aktif

        // 1. Ambil input dari pencarian (GET)
        $filterMinggu = $this->request->getVar('minggu');

        if ($filterMinggu) {
            // Jika user memilih tanggal, simpan ke Cookie selama 30 hari
            set_cookie('last_filter_minggu', $filterMinggu, 2592000);
        } else {
            // Jika tidak ada input, coba ambil dari Cookie
            $filterMinggu = get_cookie('last_filter_minggu');
            
            // Jika Cookie juga kosong (baru pertama kali buka), gunakan minggu ini
            if (!$filterMinggu) {
                $filterMinggu = date('Y-\WW'); 
            }
        }

        $dto = new \DateTime();
        // Pastikan variabel mengandung karakter '-W' agar bisa diproses
        if (strpos($filterMinggu, '-W') !== false) {
            $parts = explode('-W', $filterMinggu);
            $tahun = (int)$parts[0];
            $mingguKe = (int)$parts[1];
            $dto->setISODate($tahun, $mingguKe, 1);
        } else {
            // Fallback jika format salah
            $dto->setISODate((int)date('Y'), (int)date('W'), 1);
            $filterMinggu = date('Y-\WW');
        }

        $tglAwal = $dto->format('Y-m-d');    
        $dto->modify('+6 days');
        $tglAkhir = $dto->format('Y-m-d');

        $dataRaw = $presensiModel->getRekapMingguan($tglAwal, $tglAkhir);
        $processedData = $this->processPresensiData($dataRaw);
        $rekapMingguan = $this->aggregateMingguan($processedData, $tglAwal, $tglAkhir);
        
        $data = [
            'title'         => 'Rekap Presensi Mingguan',
            'rekapMingguan' => $rekapMingguan,
            'tglAwal'       => $tglAwal,
            'tglAkhir'      => $tglAkhir,
            'filterMinggu'  => $filterMinggu // Nilai ini akan dikirim ke View
        ];

        return view('layout_admin/head', $data)
            . view('layout_admin/sidebar')
            . view('pages_admin/rekap_absensi/mingguan', $data) 
            . view('layout_admin/footer');
    }

    /**
     * Menampilkan Rekap Presensi Bulanan
     * @param string|null $bulanTahun Bulan dan tahun rekap dalam format YYYY-MM
     */
    public function bulanan($bulanTahun = null)
    {
        $presensiModel = new PresensiModel();
        
        $bulanTahunRekap = $bulanTahun ?? date('Y-m'); 

        $dataPresensiRaw = $presensiModel->getRekapBulanan($bulanTahunRekap);

        $processedDataHarian = $this->processPresensiData($dataPresensiRaw);

        $rekapBulanan = $this->aggregateBulanan($processedDataHarian);
        
        $data = [
            'title'             => 'Rekap Presensi Bulanan',
            'rekapBulanan'      => $rekapBulanan,
            'selectedBulanTahun'=> $bulanTahunRekap,
            'listBulan'         => $this->getListBulan(), 
            'listTahun'         => $this->getListTahun(), 
            'jamMasukKantor'    => '08:00:00'
        ];

        return view('layout_admin/head', $data)
            . view('layout_admin/sidebar')
            . view('pages_admin/rekap_absensi/bulanan', $data) 
            . view('layout_admin/footer');
    }

    public function sinkronisasiAlpa($tanggal = null)
    {
        $tanggal = $tanggal ?? date('Y-m-d');
        $nomorHari = date('N', strtotime($tanggal)); 
        if ($nomorHari == 7) { 
            return redirect()->back()->with('gagal', "Sinkronisasi dibatalkan. Tanggal $tanggal adalah hari libur (akhir pekan).");
        }

        $karyawanModel = new \App\Models\KaryawanModel();
        $presensiModel = new \App\Models\PresensiModel();
        $ketidakhadiranModel = new \App\Models\KetidakhadiranModel();
        $listKaryawan = $karyawanModel->findAll();
        $count = 0;

        foreach ($listKaryawan as $k) {
            $idKaryawan = $k['id_karyawan'];

            // Cek apakah sudah ada data di tabel presensi atau ketidakhadiran
            $sudahAbsen = $presensiModel->where(['kd_karyawan' => $idKaryawan, 'tanggal_masuk' => $tanggal])->first();
            $adaKeterangan = $ketidakhadiranModel->where(['kd_karyawan' => $idKaryawan, 'tanggal' => $tanggal])->first();

            // Jika data kosong, masukkan sebagai ALPA
            if (!$sudahAbsen && !$adaKeterangan) {
                $ketidakhadiranModel->insert([
                    'kd_karyawan'      => $idKaryawan,
                    'tanggal'          => $tanggal,
                    'keterangan'       => 'ALPA', // Penting untuk filter Gaji
                    'deskripsi'        => 'Tidak hadir tanpa keterangan (Otomatis Local)',
                    'status_pengajuan' => 'DISETUJUI'
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', "Berhasil mensinkronisasi $count data Alpa.");
    }

    /**
     * Fungsi utama untuk memproses data harian (Digunakan oleh Harian, Mingguan & Bulanan)
     */
    private function processPresensiData($dataPresensi)
    {
        $results = [];

        // Set Timezone sebelum perhitungan waktu
        date_default_timezone_set('Asia/Jakarta'); 

        foreach ($dataPresensi as $data) {
            
            // Tentukan tanggal basis (menggunakan tanggal_masuk yang didapat dari query Model)
            if (!isset($data['tanggal_masuk']) || empty($data['tanggal_masuk'])) {
                $baseDate = date('Y-m-d'); 
            } else {
                $baseDate = $data['tanggal_masuk'];
            }
            
            $jamMasukAturanTime = null;
            $totalJamKerja = 'N/A';
            $statusMasuk   = 'N/A';
            $keterlambatan = '-';
            $jamMasukDefault = '08:00:00'; // Default jika jam_masuk_lokasi kosong

            // 1. Hitung Keterlambatan
            if (!empty($data['jam_masuk']) && $data['jam_masuk'] != '-') {
                $jamMasukPegawaiTime = strtotime($baseDate . ' ' . $data['jam_masuk']);

                // Gunakan jam_masuk_lokasi jika ada, jika tidak, gunakan default jam kantor
                $aturanJam = $data['jam_masuk_lokasi'] ?? $jamMasukDefault;
                
                if ($aturanJam !== null) {
                    $jamMasukAturanTime = strtotime($baseDate . ' ' . $aturanJam);

                    if ($jamMasukPegawaiTime > $jamMasukAturanTime) {
                        $selisihDetik = $jamMasukPegawaiTime - $jamMasukAturanTime;
                        $menitTerlambat = ceil($selisihDetik / 60); 
                        $statusMasuk = 'Terlambat';
                        $keterlambatan = $menitTerlambat . " menit";
                    } else {
                        $statusMasuk = 'On Time';
                    }
                } else {
                    $statusMasuk = 'Aturan Jam Tidak Ada';
                }
            } else {
                $statusMasuk = $data['tipe_data'] ?? 'Belum Absen';
            }

            // 2. Hitung Total Jam Kerja
            if (!empty($data['jam_masuk']) && !empty($data['jam_keluar']) && $data['jam_keluar'] != '00:00:00' && $data['jam_keluar'] != '-') {
                $masuk = strtotime($baseDate . ' ' . $data['jam_masuk']);
                $keluar = strtotime($baseDate . ' ' . $data['jam_keluar']);
                if ($keluar < $masuk) {
                    $keluar = strtotime('+1 day', $keluar);
                }
                
                $durasi = $keluar - $masuk;
                $totalJamKerja = gmdate('H:i:s', $durasi);
            }

            $data['total_jam_kerja'] = $totalJamKerja;
            $data['status_masuk']    = $statusMasuk;
            $data['keterlambatan']   = $keterlambatan;

            $results[] = $data;
        }
        return $results;
    }

    /**
     * Agregasi data untuk Rekap Mingguan
     */
    private function aggregateMingguan($dataHarian, $tglAwal, $tglAkhir)
    {
        $results = [];
        $ketidakhadiranModel = new \App\Models\KetidakhadiranModel();

        foreach ($dataHarian as $data) {
            $id = $data['id_karyawan'];
            
            if (!isset($results[$id])) {
                $jumlahAlpa = $ketidakhadiranModel
                    ->where('kd_karyawan', $id)
                    ->where('keterangan', 'ALPA')
                    ->where('tanggal >=', $tglAwal)
                    ->where('tanggal <=', $tglAkhir)
                    ->countAllResults();

                $results[$id] = [
                    'id_karyawan'           => $id,
                    'nm_karyawan'           => $data['nm_karyawan'],
                    'total_kehadiran'       => 0,
                    'total_alpa'            => $jumlahAlpa,
                    'total_terlambat_menit' => 0,
                    'total_jam_kerja_detik' => 0,
                    'total_jam_kerja_format'=> '00:00:00',
                ];
            }

            if (($data['tipe_data'] ?? '') === 'HADIR' && !empty($data['jam_masuk']) && $data['jam_masuk'] != '-') {
                $results[$id]['total_kehadiran']++;
            }
            
            if ($data['status_masuk'] == 'Terlambat') {
                $menitTerlambat = (int) filter_var($data['keterlambatan'], FILTER_SANITIZE_NUMBER_INT);
                $results[$id]['total_terlambat_menit'] += $menitTerlambat;
            }
            
            if ($data['total_jam_kerja'] !== 'N/A' && $data['total_jam_kerja'] !== '-') {
                list($h, $m, $s) = explode(':', $data['total_jam_kerja']);
                $results[$id]['total_jam_kerja_detik'] += ($h * 3600) + ($m * 60) + $s;
            }
        }
        
        foreach ($results as &$res) {
            $totalDetik = $res['total_jam_kerja_detik'];
            $hours = floor($totalDetik / 3600);
            $minutes = floor(($totalDetik % 3600) / 60);
            $seconds = $totalDetik % 60;
            $res['total_jam_kerja_format'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return array_values($results);
    }

    /**
     * Fungsi untuk mengagregasi data harian menjadi rekap bulanan
     */
    private function aggregateBulanan($dataHarian)
    {
        $results = [];
        $ketidakhadiranModel = new \App\Models\KetidakhadiranModel();
        foreach ($dataHarian as $data) {
            $id = $data['id_karyawan'];
            
            if (!isset($results[$id])) {
                $periodeBulan = date('Y-m', strtotime($data['tanggal_masuk']));

                $jumlahAlpa = $ketidakhadiranModel
                    ->where('kd_karyawan', $id)
                    ->where('keterangan', 'ALPA')
                    ->where("DATE_FORMAT(tanggal, '%Y-%m')", $periodeBulan)
                    ->countAllResults();

                $results[$id] = [
                    'id_karyawan'           => $id,
                    'nm_karyawan'           => $data['nm_karyawan'],
                    'total_kehadiran'       => 0,
                    'total_alpa'            => $jumlahAlpa,
                    'total_terlambat_menit' => 0,
                    'total_jam_kerja_detik' => 0,
                    'total_jam_kerja_format'=> '00:00:00',
                ];
            }
            if ($data['tipe_data'] === 'ALPA') {
                $results[$id]['total_alpa']++;
            }
            elseif (!empty($data['jam_masuk']) && !empty($data['jam_keluar']) && $data['jam_keluar'] != '00:00:00') {
                $results[$id]['total_kehadiran']++;

                if ($data['status_masuk'] == 'Terlambat') {
                    $menitTerlambat = (int) filter_var($data['keterlambatan'], FILTER_SANITIZE_NUMBER_INT);
                    $results[$id]['total_terlambat_menit'] += $menitTerlambat;
                }
                
                if ($data['total_jam_kerja'] !== 'N/A') {
                    list($h, $m, $s) = explode(':', $data['total_jam_kerja']);
                    $detik = ($h * 3600) + ($m * 60) + $s;
                    $results[$id]['total_jam_kerja_detik'] += $detik;
                }
            }
        }
        
        foreach ($results as &$res) {
            $totalDetik = $res['total_jam_kerja_detik'];
            $hours = floor($totalDetik / 3600);
            $minutes = floor(($totalDetik % 3600) / 60);
            $seconds = $totalDetik % 60;
            $res['total_jam_kerja_format'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return array_values($results);
    }
    
    /**
     * Export Rekap Mingguan ke XLSX (Excel)
     */
    public function exportMingguan()
    {
        $presensiModel = new PresensiModel();
        
        $tglAwal  = $this->request->getVar('tgl_awal') ?? date('Y-m-d', strtotime('monday this week'));
        $tglAkhir = $this->request->getVar('tgl_akhir') ?? date('Y-m-d', strtotime('sunday this week'));

        $dataRaw = $presensiModel->getRekapMingguan($tglAwal, $tglAkhir);
        $processedData = $this->processPresensiData($dataRaw);
        $rekapMingguan = $this->aggregateMingguan($processedData, $tglAwal, $tglAkhir);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul & Periode
        $sheet->setCellValue('A1', 'REKAP PRESENSI MINGGUAN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'PERIODE: ' . date('d/m/Y', strtotime($tglAwal)) . ' s/d ' . date('d/m/Y', strtotime($tglAkhir)));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header Tabel
        $headerKolom = ['No', 'Nama Pegawai', 'Total Hadir (Hari)', 'Total Alpa (Hari)', 'Total Jam Kerja', 'Total Keterlambatan (Menit)'];
        $kolomStart = 'A';
        $barisHeader = 4;
        
        foreach ($headerKolom as $judul) {
            $kolom = $kolomStart++;
            $sheet->setCellValue($kolom . $barisHeader, $judul);
            $sheet->getColumnDimension($kolom)->setAutoSize(true);
            $style = $sheet->getStyle($kolom . $barisHeader);
            $style->getFont()->setBold(true);
            $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFA0A0A0'); 
            $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Isi Data
        $barisData = 5;
        foreach ($rekapMingguan as $no => $data) {
            $sheet->setCellValue('A' . $barisData, $no + 1);
            $sheet->setCellValue('B' . $barisData, $data['nm_karyawan']);
            $sheet->setCellValue('C' . $barisData, $data['total_kehadiran']);
            $sheet->setCellValue('D' . $barisData, $data['total_alpa']);
            $sheet->setCellValue('E' . $barisData, $data['total_jam_kerja_format']);
            $sheet->setCellValue('F' . $barisData, $data['total_terlambat_menit']);
            $sheet->getStyle('A' . $barisData . ':F' . $barisData)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $barisData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap_Mingguan_' . $tglAwal . '_to_' . $tglAkhir . '.xlsx';

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Rekap Presensi Harian ke XLSX (Excel)
     */
    public function exportHarian($tanggal = null)
    {
        date_default_timezone_set('Asia/Jakarta'); 
        $presensiModel = new PresensiModel();
        $tanggalRekap = $tanggal ?? date('Y-m-d'); 

        $dataPresensiRaw = $presensiModel->getRekapHarian($tanggalRekap);
        $rekapData = $this->processPresensiData($dataPresensiRaw);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $tanggalNama = date('d F Y', strtotime($tanggalRekap));

        $sheet->setCellValue('A1', 'REKAP PRESENSI HARIAN');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'TANGGAL: ' . $tanggalNama);
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $headerKolom = ['No', 'Nama Pegawai', 'Tanggal', 'Tipe','Jam Masuk', 'Jam Keluar', 'Total Jam Kerja', 'Status', 'Keterlambatan', 'Lokasi Presensi'];
        $kolomStart = 'A';
        $barisHeader = 4;
        
        foreach ($headerKolom as $judul) {
            $kolom = $kolomStart++;
            $sheet->setCellValue($kolom . $barisHeader, $judul);
            $sheet->getColumnDimension($kolom)->setAutoSize(true);
            $style = $sheet->getStyle($kolom . $barisHeader);
            $style->getFont()->setBold(true);
            $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFA0A0A0'); 
            $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $barisData = $barisHeader + 1;
        $no = 1;
        foreach ($rekapData as $data) {
            $jamKeluarDisplay = $data['jam_keluar'];
            if ($data['tipe_data'] === 'ALPA') {
                $jamKeluarDisplay = '-';
            } elseif ($jamKeluarDisplay == '00:00:00' || empty($jamKeluarDisplay)) {
                $jamKeluarDisplay = 'Belum Pulang';
            } else {
                $jamKeluarDisplay = substr($jamKeluarDisplay, 0, 5);
            }

            $row = [
                $no++,
                $data['nm_karyawan'] ?? '-',
                empty($data['tanggal_masuk']) ? '-' : date('d/m/Y', strtotime($data['tanggal_masuk'])),
                $data['tipe_data'] ?? 'HADIR',
                $data['jam_masuk'] ?? '-',
                $jamKeluarDisplay,
                $data['total_jam_kerja'] ?? '-',
                $data['status_masuk'] ?? '-',
                $data['keterlambatan'] ?? '-',
                $data['nama_lokasi'] ?? '-'
            ];
            
            $kolomStart = 'A';
            foreach ($row as $value) {
                $sheet->setCellValue($kolomStart++ . $barisData, $value);
            }
            $sheet->getStyle('A' . $barisData . ':I' . $barisData)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $barisData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap_Harian_' . $tanggalRekap . '.xlsx';
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0'); 
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Rekap Presensi Bulanan ke XLSX (Excel)
     */
    public function exportBulanan($bulanTahun = null)
    {
        date_default_timezone_set('Asia/Jakarta'); 
        $presensiModel = new PresensiModel();
        $bulanTahunRekap = $bulanTahun ?? date('Y-m'); 

        $dataPresensiRaw = $presensiModel->getRekapBulanan($bulanTahunRekap);
        $processedDataHarian = $this->processPresensiData($dataPresensiRaw);
        $rekapBulanan = $this->aggregateBulanan($processedDataHarian);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $bulanNama = date('F Y', strtotime($bulanTahunRekap . '-01'));

        $sheet->setCellValue('A1', 'REKAP PRESENSI BULANAN');
        $sheet->mergeCells('A1:F1'); 
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'PERIODE: ' . $bulanNama);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $headerKolom = ['No', 'Nama Pegawai', 'Total Hadir (Hari)', 'Total Alpa (Hari)', 'Total Jam Kerja', 'Total Keterlambatan'];        $kolomStart = 'A';
        $barisHeader = 4;
        
        foreach ($headerKolom as $judul) {
            $kolom = $kolomStart++;
            $sheet->setCellValue($kolom . $barisHeader, $judul);
            $sheet->getColumnDimension($kolom)->setAutoSize(true);
            $style = $sheet->getStyle($kolom . $barisHeader);
            $style->getFont()->setBold(true);
            $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFA0A0A0'); 
            $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $barisData = $barisHeader + 1; 
        foreach ($rekapBulanan as $no => $data) {
            $row = [
                $no + 1,
                $data['nm_karyawan'] ?? '-',
                $data['total_kehadiran'],
                $data['total_alpa'] ?? 0,
                $data['total_jam_kerja_format'],
                $data['total_terlambat_menit']
            ];
            
            $kolomStart = 'A';
            foreach ($row as $value) {
                $sheet->setCellValue($kolomStart++ . $barisData, $value);
            }
            $sheet->getStyle('A' . $barisData . ':F' . $barisData)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $barisData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap_Bulanan_' . str_replace('-', '_', $bulanTahunRekap) . '.xlsx';
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0'); 
        $writer->save('php://output');
        exit;
    }

    private function getListBulan()
    {
        return ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                 '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
    }

    private function getListTahun()
    {
        $currentYear = (int) date('Y');
        $years = [];
        for ($i = 0; $i < 5; $i++) {
            $years[] = (string) ($currentYear - $i);
        }
        return $years;
    }
}