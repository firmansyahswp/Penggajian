<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use App\Models\PenggajianModel;
use App\Models\KaryawanModel; // Untuk detail info karyawan saat download
use CodeIgniter\I18n\Time;

class Gaji extends BaseController
{
    protected $penggajianModel;
    protected $karyawanModel;
    protected $session;
    protected $timezone = 'Asia/Jakarta';

    public function __construct()
    {
        \date_default_timezone_set($this->timezone);
        $this->penggajianModel = new PenggajianModel();
        $this->karyawanModel = new KaryawanModel();
        $this->session = \Config\Services::session();
    }
    
    /**
     * Mendapatkan ID Karyawan dari sesi.
     */
    private function getKaryawanId()
    {
        return $this->session->get('user_id'); 
    }

    // =======================================================
    // 1. DAFTAR RIWAYAT GAJI (Read)
    // =======================================================
    public function index()
    {
        $kd_karyawan = $this->getKaryawanId();
        if (!$kd_karyawan) {
            return redirect()->to(base_url('logout'));
        }

        $data = [
            'title' => 'Riwayat Slip Gaji',
            // Ambil semua riwayat gaji untuk karyawan ini
            'riwayat_gaji' => $this->penggajianModel->getGajiByKaryawanId($kd_karyawan)
        ];
        
        return view('pegawai/head', $data)
             . view('pegawai/sidebar')
             . view('pages_pegawai/gaji/riwayat_gaji', $data) 
             . view('pegawai/footer');
    }
    
    // =======================================================
    // 2. TAMPILKAN DETAIL SLIP GAJI (Detail)
    // =======================================================
    public function detail($no_penggajian)
    {
        $kd_karyawan = $this->getKaryawanId();
        if (!$kd_karyawan) {
            return redirect()->to(base_url('logout'));
        }

        $gaji = $this->penggajianModel->find($no_penggajian);
        
        // Pastikan record gaji ada dan milik karyawan ini
        if (!$gaji || $gaji['kd_karyawan'] != $kd_karyawan) {
            return redirect()->to(base_url('pegawai/gaji'))->with('error', 'Slip gaji tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $data = [
            'title' => 'Detail Slip Gaji Periode ' . date('M Y', strtotime($gaji['periode_pph21'])),
            'gaji' => $gaji,
            'karyawan' => $this->karyawanModel->getKaryawanWithDetails($kd_karyawan)
        ];
        
        return view('pegawai/head', $data)
             . view('pegawai/sidebar')
             . view('pages_pegawai/gaji/detail_slip', $data) // View untuk detail
             . view('pegawai/footer');
    }

    // =======================================================
    // 3. DOWNLOAD SLIP GAJI PDF (Download)
    // =======================================================
    public function downloadPdf($no_penggajian)
    {
        $kd_karyawan = $this->getKaryawanId();
        $gaji = $this->penggajianModel->find($no_penggajian);

        if (!$gaji || $gaji['kd_karyawan'] != $kd_karyawan) {
             return redirect()->to(base_url('pegawai/gaji'))->with('error', 'Akses ditolak.');
        }
        
        $data = [
            'gaji' => $gaji,
            'karyawan' => $this->karyawanModel->getKaryawanWithDetails($kd_karyawan),
            'periode_gaji' => $gaji['periode_pph21']
        ];

        // 1. Render View (menggunakan view slip_pdf Admin yang sudah ada)
        $html = view('pages_admin/penggajian/slip_pdf', $data);

        // 2. Inisialisasi Dompdf (Asumsi sudah diinstal via composer)
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Slip_Gaji_' . $data['karyawan']['nip'] . '_' . $data['periode_gaji'] . '.pdf';
        
        $dompdf->stream($filename, ['Attachment' => 1]); 
        exit();
    }
}