<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use App\Models\PermohonanPinjamanModel;
use App\Models\AngsuranPinjamanModel; 
use CodeIgniter\Database\Exceptions\DatabaseException;

class Pinjaman extends BaseController
{
    protected $permohonanModel;
    protected $angsuranModel; 
    protected $helpers = ['form'];
    protected $session; 
    protected $timezone = 'Asia/Jakarta';

    public function __construct()
    {
        \date_default_timezone_set($this->timezone); 

        $this->permohonanModel = new PermohonanPinjamanModel();
        $this->angsuranModel = new AngsuranPinjamanModel(); 
        $this->session = \Config\Services::session();
    }
    
    /**
     * Fungsi untuk mendapatkan ID Karyawan (id_karyawan) dari sesi.
     * @return int|null ID Karyawan jika ditemukan.
     */
    private function getKaryawanId()
    {
        // Kunci 'user_id' menyimpan ID Karyawan (id_karyawan)
        return $this->session->get('user_id'); 
    }
    
    /**
     * Fungsi untuk mendapatkan Primary Key User (kd_user) dari sesi.
     * @return int|null KD User jika ditemukan.
     */
    private function getUserId()
    {
        // Kunci 'kd_user' menyimpan Primary Key User (kd_user)
        return $this->session->get('kd_user');
    }

    // =======================================================
    // 1. Tampilkan Form Pengajuan (GET /pegawai/pinjaman/ajukan)
    // =======================================================
    public function ajukan()
    {
        $data = [
            'title' => 'Formulir Pengajuan Pinjaman',
            'validation' => \Config\Services::validation()
        ];
        
        // Menggunakan path pages_pegawai/pinjaman/ajuan_pinjaman
        return view('pegawai/head', $data)
             . view('pegawai/sidebar')
             . view('pages_pegawai/pinjaman/ajuan_pinjaman', $data) 
             . view('pegawai/footer');
    }

    // =======================================================
    // 2. Simpan Pengajuan (POST /pegawai/pinjaman/saveAjuan)
    // =======================================================
    public function saveAjuan()
    {
        if (!$this->validate([
            'besar_pinjaman' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[10]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kd_karyawan = $this->getKaryawanId();
        $kd_user = $this->getUserId(); 

        if (empty($kd_karyawan) || empty($kd_user)) {
             return redirect()->to(base_url('logout'))->with('error', 'Sesi login tidak lengkap. Mohon login ulang.');
        }

        $current_datetime = date('Y-m-d H:i:s'); 
        
        $data = [
            'kd_user'          => $kd_user, 
            'kd_karyawan'      => $kd_karyawan,
            'besar_pinjaman'   => $this->request->getPost('besar_pinjaman'),
            'keterangan'       => $this->request->getPost('keterangan'),
            'timedate'         => $current_datetime,
            'status_pengajuan' => 'PENDING',
        ];

        try {
            $this->permohonanModel->save($data);
        } catch (DatabaseException $e) {
             return redirect()->back()->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage());
        }
        
        return redirect()->to(base_url('pegawai/pinjaman/status'))->with('success', 'Pengajuan Pinjaman berhasil dikirim dan menunggu persetujuan Admin.');
    }
    
    // =======================================================
    // 3. Lihat Status Pengajuan Sendiri (GET /pegawai/pinjaman/status)
    //    Memastikan key 'status_pinjaman_akhir' selalu ada.
    // =======================================================
    public function status()
    {
        $kd_karyawan = $this->getKaryawanId();
        
        $list_permohonan = $kd_karyawan ? 
                           $this->permohonanModel->where('kd_karyawan', $kd_karyawan)
                                               ->orderBy('timedate', 'DESC')
                                               ->findAll() 
                           : [];
        
        $data_status = [];
        foreach ($list_permohonan as $permohonan) {
            
            // 1. Definisikan status akhir awal (untuk kasus PENDING, DITOLAK)
            $status_akhir = $permohonan['status_pengajuan'];
            
            if ($permohonan['status_pengajuan'] == 'DISETUJUI') {
                // 2. Jika DISETUJUI, cek saldo di ledger angsuran
                $saldo = $this->angsuranModel->getSaldoPinjaman($permohonan['id']);
                
                if ($saldo >= 0) {
                    $status_akhir = 'LUNAS'; // Selesai / Lunas
                } else {
                    $status_akhir = 'AKTIF'; // Masih ada sisa hutang
                }
            }
            
            // 3. Tambahkan key status_pinjaman_akhir ke array yang akan dikirim
            $permohonan['status_pinjaman_akhir'] = $status_akhir;
            
            $data_status[] = $permohonan;
        }

        $data = [
            'title' => 'Status Pengajuan Pinjaman Saya',
            'permohonan' => $data_status // Mengirim array yang sudah dijamin memiliki key
        ];
        
        // Menggunakan path pages_pegawai/pinjaman/status_ajuan
        return view('pegawai/head', $data)
             . view('pegawai/sidebar')
             . view('pages_pegawai/pinjaman/status_ajuan', $data) 
             . view('pegawai/footer');
    }
}