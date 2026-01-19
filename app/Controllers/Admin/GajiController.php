<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PenggajianModel;        
use App\Models\KaryawanModel;          
use App\Models\PtkpModel;              
use App\Models\AngsuranPinjamanModel;  
use App\Models\PotonganGajiModel;      
use App\Models\TarifKonstantaModel;    
use App\Models\UangLemburModel;        
use App\Models\KetidakhadiranModel;
use App\Models\PresensiModel; 
use CodeIgniter\Database\Exceptions\DatabaseException;

class GajiController extends BaseController
{
    protected $penggajianModel;
    protected $karyawanModel;
    protected $ptkpModel;
    protected $angsuranModel;
    protected $potonganModel;
    protected $tarifKonstantaModel;
    protected $uangLemburModel;
    protected $presensiModel; 
    protected $ketidakhadiranModel;
    protected $TARIF = []; 
    
    public function __construct()
    {
        \date_default_timezone_set('Asia/Jakarta');
        
        helper(['form', 'url', 'filesystem']);
        
        $this->penggajianModel = new PenggajianModel();
        $this->karyawanModel = new KaryawanModel();
        $this->ptkpModel = new PtkpModel();
        $this->angsuranModel = new AngsuranPinjamanModel();
        $this->potonganModel = new PotonganGajiModel();
        $this->ketidakhadiranModel = new KetidakhadiranModel();
        $this->tarifKonstantaModel = new TarifKonstantaModel(); 
        $this->uangLemburModel = new UangLemburModel(); 
        $this->presensiModel = new PresensiModel(); 
        
        $this->TARIF = $this->loadKonstanta();
    }
    
    private function loadKonstanta()
    {
        $tarif = $this->tarifKonstantaModel->getTarifAsArray(); 
        
        $tarif['BPJS_KES_KARYAWAN_PERCENT'] = $tarif['BPJS_KES_KARYAWAN_PERCENT'] ?? 0.01;
        $tarif['BPJS_JHT_KARYAWAN_PERCENT'] = $tarif['BPJS_JHT_KARYAWAN_PERCENT'] ?? 0.02;
        $tarif['BPJS_JP_KARYAWAN_PERCENT'] = $tarif['BPJS_JP_KARYAWAN_PERCENT'] ?? 0.01;
        $tarif['BPJS_KES_MAX_GAJI'] = $tarif['BPJS_KES_MAX_GAJI'] ?? 12000000.00;
        $tarif['BPJS_JP_MAX_GAJI'] = $tarif['BPJS_JP_MAX_GAJI'] ?? 10547400.00;
        $tarif['BIAYA_JABATAN_MAX'] = $tarif['BIAYA_JABATAN_MAX'] ?? 500000.00;
        
        return $tarif;
    }

    public function index()
    {
        $data = [
            'title' => 'Hitung & Rekap Gaji Bulanan',
            'listPeriode' => $this->getListPeriode()
        ];
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/penggajian/form_hitung', $data)
             . view('layout_admin/footer');
    }

    public function prosesHitung()
    {
        $periodeInput = $this->request->getPost('periode_gaji'); 

        if (empty($periodeInput)) {
            return redirect()->back()->with('error', 'Periode gaji harus dipilih.');
        }

        $periodeGajiDB = $periodeInput . '-01';

        $listKaryawan = $this->karyawanModel->getKaryawanWithDetails(); 
        $totalKaryawanDiproses = 0;

        foreach ($listKaryawan as $karyawan) {
            $idKaryawan = $karyawan['id_karyawan'];

            // LOGIKA PEMISAH: Cari data di periode yang dipilih
            $exist = $this->penggajianModel->where('kd_karyawan', $idKaryawan)
                                          ->where('periode_pph21', $periodeGajiDB)
                                          ->first();

            // 1. PENGHASILAN DAN BRUTO
            $gajiPokok = (float) $karyawan['gaji_pokok'];
            $tunjTransport = (float) $karyawan['uang_transport'];
            $tunjMakan = (float) $karyawan['uang_makan'];
            $totalLembur = $this->uangLemburModel->getTotalLemburByKaryawan($idKaryawan, $periodeInput); 
            $totalBonus = 0; 
            
            $gajiBruto = $gajiPokok + $tunjTransport + $tunjMakan + $totalLembur + $totalBonus;
            $dasarBPJS = $gajiPokok + $tunjTransport + $tunjMakan; 

            // 2. POTONGAN WAJIB
            $potBPJSKes = min($dasarBPJS, $this->TARIF['BPJS_KES_MAX_GAJI']) * $this->TARIF['BPJS_KES_KARYAWAN_PERCENT'];
            $potBPJSJHT = $dasarBPJS * $this->TARIF['BPJS_JHT_KARYAWAN_PERCENT'];
            $potBPJSJP  = min($dasarBPJS, $this->TARIF['BPJS_JP_MAX_GAJI']) * $this->TARIF['BPJS_JP_KARYAWAN_PERCENT'];
            $totalPotBPJSTK = $potBPJSJHT + $potBPJSJP;
            
            // 3. PPh 21
            $pph21Result = $this->hitungPPH21($gajiBruto, $karyawan, $totalPotBPJSTK); 

            // 4. POTONGAN LAIN (Pinjaman, Alpa, Terlambat)
            $besarAngsuranPinjaman = $this->prosesPotonganPinjaman($idKaryawan, $periodeGajiDB);
            $totalPotonganLain = $this->potonganModel->getTotalPotonganByKaryawan($idKaryawan, $periodeInput);
            
            $jumlahAlpa = $this->ketidakhadiranModel
                ->where('kd_karyawan', $idKaryawan)
                ->where('keterangan', 'ALPA')
                ->where("DATE_FORMAT(tanggal, '%Y-%m')", $periodeInput)
                ->countAllResults();
            $totalPotonganAlpa = $jumlahAlpa * 150000;

            $jumlahTerlambat = $this->presensiModel
                ->where('kd_karyawan', $idKaryawan)
                ->where('is_terlambat_denda', 1)
                ->where("DATE_FORMAT(tanggal_masuk, '%Y-%m')", $periodeInput)
                ->countAllResults();
            $totalPotonganTerlambat = $jumlahTerlambat * 75000;

            // 5. FINAL GAJI BERSIH
            $totalPotonganWajib = $pph21Result['pph21_sebulan'] + ($potBPJSKes + $totalPotBPJSTK);
            $totalPotonganLainnya = $besarAngsuranPinjaman + $totalPotonganLain + $totalPotonganAlpa + $totalPotonganTerlambat;            
            $gajiBersih = max(0, $gajiBruto - $totalPotonganWajib - $totalPotonganLainnya);

            // 6. DATA SIMPAN
            $dataSimpan = [
                'periode_pph21' => $periodeGajiDB, 
                'tanggal' => date('Y-m-d H:i:s'), 
                'kd_karyawan' => $idKaryawan,
                'gaji_pokok' => $gajiPokok, 
                'tunj_transport' => $tunjTransport, 
                'tunj_makan' => $tunjMakan,
                'total_lembur' => $totalLembur, 
                'total_bonus' => $totalBonus, 
                'bruto' => $gajiBruto,
                'total_pinjaman' => $besarAngsuranPinjaman, 
                'potongan_bpjs_tk' => $totalPotBPJSTK, 
                'potongan_bpjs_kes' => $potBPJSKes,
                'pph21_sebulan' => $pph21Result['pph21_sebulan'], 
                'total_potongan_lain' => $totalPotonganLain, 
                'biaya_jabatan' => $pph21Result['biaya_jabatan'], 
                'netto_sebulan' => $pph21Result['netto_sebulan'],
                'netto_setahun' => $pph21Result['netto_setahun'], 
                'total_ptkp' => $pph21Result['total_ptkp'],
                'pkp' => $pph21Result['pkp'], 
                'pph21_setahun' => $pph21Result['pph21_setahun'],
                'gaji_bersih' => $gajiBersih,
                'potongan_alpa' => $totalPotonganAlpa,
                'jumlah_alpa'   => $jumlahAlpa,
                'potongan_terlambat' => $totalPotonganTerlambat, 
                'jumlah_terlambat'   => $jumlahTerlambat,
            ];

            // LOGIKA UPDATE: Jika sudah ada di bulan yang sama, sertakan Primary Key
            if ($exist) {
                $dataSimpan['no_penggajian'] = $exist['no_penggajian'];
            }

            // Jika periodeGaji berbeda, CI4 otomatis melakukan INSERT (membuat record baru)
            $this->penggajianModel->save($dataSimpan);
            $totalKaryawanDiproses++;
        }

        return redirect()->to(base_url('admin/gaji/rekap?periode='.$periodeInput))
                         ->with('success', "Data periode $periodeInput berhasil diproses/diperbarui.");
    }
    
    public function rekap()
    {
        
        $periodeInput = $this->request->getGet('periode') ?? date('Y-m');
        $keyword = $this->request->getGet('q'); 
        $periodeDB = $periodeInput . '-01';
        
        $data = [
            'title' => 'Rekap Gaji Bulanan',
            'list_rekap' => $this->penggajianModel->getRekapWithKaryawan($periodeDB, $keyword), 
            'listPeriode' => $this->getListPeriode(),
            'selectedPeriode' => $periodeInput,
            'keyword' => $keyword
        ];
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/penggajian/rekap_gaji', $data)
             . view('layout_admin/footer');
    }
    
    public function delete($no_penggajian)
    {
        $penggajian = $this->penggajianModel->find($no_penggajian);
        if (!$penggajian) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        try {
            $this->angsuranModel->where('kd_karyawan', $penggajian['kd_karyawan'])
                                ->where('periode_gaji', $penggajian['periode_pph21'])
                                ->where('tipe_pembayaran', 'POTONGAN_GAJI')
                                ->delete(); 
            $this->penggajianModel->delete($no_penggajian);
        } catch (DatabaseException $e) {
            return redirect()->back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function exportSlipPdf($kd_karyawan, $periode)
    {
        $dataGaji = $this->penggajianModel->where('kd_karyawan', $kd_karyawan)->where('periode_pph21', $periode)->first();
        if (!$dataGaji) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $data = [
            'gaji' => $dataGaji,
            'karyawan' => $this->karyawanModel->getKaryawanWithDetails($kd_karyawan), 
            'periode_gaji' => $periode
        ];

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('pages_admin/penggajian/slip_pdf', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Slip_'.$data['karyawan']['nip'].'_'.$periode.'.pdf', ['Attachment' => 1]); 
        exit();
    }

    private function getTerCategory(string $nama_ptkp)
    {
        if (in_array($nama_ptkp, ['TK/0', 'TK/1', 'K/0'])) return 'A';
        if ($nama_ptkp == 'K/3') return 'C';
        return 'B'; 
    }

    private function getTerTariff(string $category, float $bruto)
    {
        $tarif = 0.00; 
        if ($category == 'A') {
            if ($bruto <= 5400000) $tarif = 0.00; 
            else if ($bruto <= 5650000) $tarif = 0.0025; 
            else if ($bruto <= 5950000) $tarif = 0.005;  
            else if ($bruto <= 6300000) $tarif = 0.0075; 
            else if ($bruto <= 6750000) $tarif = 0.01;   
            else if ($bruto <= 7500000) $tarif = 0.0125; 
            else if ($bruto <= 8550000) $tarif = 0.015;  
            else if ($bruto <= 9650000) $tarif = 0.0175; 
            else if ($bruto <= 10050000) $tarif = 0.02;  
            else if ($bruto <= 10350000) $tarif = 0.0225;
            else if ($bruto <= 10700000) $tarif = 0.025;
            else if ($bruto <= 11050000) $tarif = 0.03;
            else if ($bruto <= 11600000) $tarif = 0.035;
            else if ($bruto <= 12500000) $tarif = 0.04;
            else if ($bruto <= 13750000) $tarif = 0.05;
            else if ($bruto <= 15100000) $tarif = 0.06;
            else if ($bruto <= 16950000) $tarif = 0.07;
            else if ($bruto <= 19750000) $tarif = 0.08;
            else if ($bruto <= 24150000) $tarif = 0.09;
            else if ($bruto <= 26450000) $tarif = 0.10;
            else if ($bruto <= 28000000) $tarif = 0.11;
            else if ($bruto <= 30050000) $tarif = 0.12;
            else if ($bruto <= 32400000) $tarif = 0.13;
            else if ($bruto <= 35400000) $tarif = 0.14;
            else $tarif = 0.15;
        } else if ($category == 'B') {
            if ($bruto <= 6200000) $tarif = 0.00;
            else if ($bruto <= 6500000) $tarif = 0.0025;
            else if ($bruto <= 6850000) $tarif = 0.005;
            else if ($bruto <= 7300000) $tarif = 0.0075; 
            else if ($bruto <= 9200000) $tarif = 0.01; 
            else if ($bruto <= 10750000) $tarif = 0.015;
            else if ($bruto <= 11250000) $tarif = 0.02;
            else if ($bruto <= 11600000) $tarif = 0.025; 
            else if ($bruto <= 12600000) $tarif = 0.03;
            else if ($bruto <= 13600000) $tarif = 0.04;
            else $tarif = 0.15;
        } else if ($category == 'C') {
            if ($bruto <= 6600000) $tarif = 0.00;
            else if ($bruto <= 6950000) $tarif = 0.0025;
            else if ($bruto <= 7350000) $tarif = 0.005;
            else if ($bruto <= 7800000) $tarif = 0.0075;
            else if ($bruto <= 8850000) $tarif = 0.01;
            else if ($bruto <= 9800000) $tarif = 0.0125;
            else if ($bruto <= 10950000) $tarif = 0.015;
            else $tarif = 0.15; 
        }
        return $tarif; 
    }

    private function hitungPPH21($gajiBruto, $karyawan, $totalPotonganBPJSTK)
    {
        $terCategory = $this->getTerCategory($karyawan['nama_ptkp']); 
        $terTariff = $this->getTerTariff($terCategory, $gajiBruto); 
        $biayaJabatan = min($gajiBruto * 0.05, $this->TARIF['BIAYA_JABATAN_MAX']);
        $nettoSebulan = $gajiBruto - $biayaJabatan - $totalPotonganBPJSTK;
        $nettoSetahun = $nettoSebulan * 12;
        $totalPtkp = $karyawan['total_ptkp'] ?? 0; 
        $pkp = max(0, $nettoSetahun - $totalPtkp);
        
        return [
            'pph21_sebulan' => $gajiBruto * $terTariff,
            'biaya_jabatan' => $biayaJabatan,
            'netto_sebulan' => $nettoSebulan,
            'netto_setahun' => $nettoSetahun,
            'total_ptkp' => $totalPtkp,
            'pkp' => $pkp,
            'pph21_setahun' => $pkp * 0.05
        ];
    }
    
    private function prosesPotonganPinjaman($kd_karyawan, $periodeGajiDB)
    {
        $activeDebts = $this->angsuranModel->where('kd_karyawan', $kd_karyawan)->where('tipe_pembayaran', 'DEBT')->findAll();
        $totalPotonganBulanIni = 0;

        foreach ($activeDebts as $debt) {
            $idPermohonan = $debt['id_permohonan'];
            $saldo = $this->angsuranModel->getSaldoPinjaman($idPermohonan);

            if ($saldo < 0) {
                // Cek apakah angsuran untuk PERIODE INI sudah tercatat
                $sudahDipotong = $this->angsuranModel->where('id_permohonan', $idPermohonan)
                                                     ->where('tipe_pembayaran', 'POTONGAN_GAJI')
                                                     ->where('periode_gaji', $periodeGajiDB)
                                                     ->first();
                if (!$sudahDipotong) {
                    $jumlahPotongan = min(500000, abs($saldo));
                    $this->angsuranModel->save([
                        'id_permohonan'    => $idPermohonan,
                        'kd_karyawan'      => $kd_karyawan,
                        'tanggal_bayar'    => date('Y-m-d H:i:s'), 
                        'besar_angsuran'   => $jumlahPotongan, 
                        'tipe_pembayaran'  => 'POTONGAN_GAJI',
                        'periode_gaji'     => $periodeGajiDB,
                        'keterangan'       => 'Angsuran Pinjaman Otomatis Periode ' . $periodeGajiDB,
                    ]);
                    $totalPotonganBulanIni += $jumlahPotongan;
                } else {
                    // Jika sudah ada, kembalikan nilai yang sudah dipotong sebelumnya
                    $totalPotonganBulanIni += $sudahDipotong['besar_angsuran'];
                }
            }
        }
        return $totalPotonganBulanIni;
    }
    
    private function getListPeriode()
    {
        $currentYear = (int) date('Y');
        $list = [];
        for ($y = $currentYear; $y >= $currentYear - 2; $y--) {
            for ($m = 12; $m >= 1; $m--) {
                $month = str_pad($m, 2, '0', STR_PAD_LEFT);
                $periode = $y . '-' . $month;
                $list[$periode] = $month . '-' . $y;
            }
        }
        return $list;
    }
}