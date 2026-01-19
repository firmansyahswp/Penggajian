<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AngsuranPinjamanModel;
use App\Models\PermohonanPinjamanModel;
use App\Models\KaryawanModel;
use CodeIgniter\I18n\Time; 

class Angsuran extends BaseController
{
    protected $angsuranModel;
    protected $permohonanModel;
    protected $karyawanModel;
    protected $timezone = 'Asia/Jakarta';
    
    protected $helpers = ['form', 'number'];

    public function __construct()
    {
        \date_default_timezone_set($this->timezone);
        $this->angsuranModel = new AngsuranPinjamanModel();
        $this->permohonanModel = new PermohonanPinjamanModel(); //
        $this->karyawanModel = new KaryawanModel(); //
    }

    // =======================================================
    // 1. List Pinjaman Aktif (GET /admin/angsuran)
    //    Filter Per Bulan dan Search Keyword
    // =======================================================
    public function index()
    {
        $bulanTahun = $this->request->getGet('periode') ?? date('Y-m'); 
        $keyword = $this->request->getGet('q'); 

        // Mengambil semua record DEBT (Hutang Awal)
        $list_hutang = $this->angsuranModel->getDaftarPinjamanAktif($bulanTahun, $keyword);
        
        $data_pinjaman = [];
        foreach ($list_hutang as $hutang) {
            $id_permohonan = $hutang['id_permohonan'];
            $saldo = $this->angsuranModel->getSaldoPinjaman($id_permohonan);
            
            $hutang['total_hutang'] = abs($hutang['besar_angsuran']); 
            $hutang['sisa_saldo'] = abs(min(0, $saldo)); 
            $hutang['status_lunas'] = ($saldo >= 0) ? 'LUNAS' : 'AKTIF';
            
            $data_pinjaman[] = $hutang;
        }

        $data = [
            'title' => 'Manajemen Angsuran Pinjaman',
            'list_pinjaman' => $data_pinjaman,
            'selectedBulanTahun' => $bulanTahun,
            'keyword' => $keyword,
            'listBulan' => $this->getListBulan(),
            'listTahun' => $this->getListTahun()
        ];

        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/pinjaman/angsuran_bulanan_admin', $data) 
             . view('layout_admin/footer');
    }

    // =======================================================
    // 2. Detail Riwayat Angsuran (GET /admin/angsuran/detail/{id_permohonan})
    // =======================================================
    public function detail($id_permohonan)
    {
        $id_permohonan = (int)$id_permohonan;
        
        // Ambil record DEBT pertama untuk mengetahui hutang awal
        $hutang_awal = $this->angsuranModel
                            ->where('id_permohonan', $id_permohonan)
                            ->where('tipe_pembayaran', 'DEBT')
                            ->first();
                            
        if (empty($hutang_awal)) {
            return redirect()->back()->with('error', 'Data pinjaman tidak ditemukan atau belum dicairkan.');
        }

        $karyawan = $this->karyawanModel->find($hutang_awal['kd_karyawan']);

        $data = [
            'title' => 'Riwayat Angsuran Permohonan ID: ' . $id_permohonan,
            'hutang_awal' => $hutang_awal,
            'karyawan' => $karyawan,
            'total_pinjaman' => abs($hutang_awal['besar_angsuran']),
            'sisa_saldo' => abs(min(0, $this->angsuranModel->getSaldoPinjaman($id_permohonan))),
            'riwayat_angsuran' => $this->angsuranModel->getAngsuranByPermohonanId($id_permohonan),
            'validation' => \Config\Services::validation()
        ];

        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/pinjaman/detail_angsuran_admin', $data) 
             . view('layout_admin/footer');
    }

    // =======================================================
    // 3. Simpan Pembayaran Manual (POST /admin/angsuran/manual)
    // =======================================================
    public function storeManualPayment()
    {
        // ... (Logic storeManualPayment sama seperti di jawaban sebelumnya)

        $id_permohonan = $this->request->getPost('id_permohonan');
        $besar_angsuran = $this->request->getPost('besar_angsuran');
        
        // ... (Validasi dan cek sisa saldo)
        
        $hutang_awal = $this->angsuranModel->where('id_permohonan', $id_permohonan)
                                          ->where('tipe_pembayaran', 'DEBT')
                                          ->first();

        if (empty($hutang_awal)) {
            return redirect()->back()->with('error', 'Data hutang awal tidak ditemukan.');
        }

        $sisa = abs(min(0, $this->angsuranModel->getSaldoPinjaman($id_permohonan)));

        if ($besar_angsuran > $sisa) {
            return redirect()->back()->with('error', 'Jumlah pembayaran melebihi sisa pinjaman (Rp ' . number_format($sisa) . ').');
        }

        // Simpan ke Angsuran Pinjaman (Nilai POSITIF)
        $this->angsuranModel->save([
            'id_permohonan'    => $id_permohonan,
            'kd_karyawan'      => $hutang_awal['kd_karyawan'],
            'tanggal_bayar'    => $this->request->getPost('tanggal_bayar'), 
            'besar_angsuran'   => (float)$besar_angsuran, // Nilai POSITIF
            'tipe_pembayaran'  => 'BAYAR_LANGSUNG',
            'keterangan'       => $this->request->getPost('keterangan') ?? 'Pembayaran Manual oleh Admin',
        ]);
        
        return redirect()->to(base_url('admin/angsuran/detail/' . $id_permohonan))->with('success', 'Pembayaran manual berhasil dicatat.');
    }
    
    // ... (Helper functions getListBulan, getListTahun)
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