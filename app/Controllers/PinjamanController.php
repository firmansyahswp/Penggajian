?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PermohonanPinjamanModel;
use App\Models\PinjamanModel; 

class PinjamanController extends BaseController
{
    protected $permohonanModel;
    protected $pinjamanModel;
    protected $session;
    protected $employeeLevel = 2; // Asumsi: Pegawai adalah level 2
    protected $adminLevel = 1;    // Asumsi: Admin adalah level 1

    public function __construct()
    {
        $this->permohonanModel = new PermohonanPinjamanModel();
        $this->pinjamanModel = new PinjamanModel();
        $this->session = \Config\Services::session();
        
        // Middleware: Cek autentikasi dasar
        if (!$this->session->get('isLoggedIn')) {
            // Ubah ke rute login Anda
            // return redirect()->to(base_url('login'));
        }
    }

    // =======================================================
    // 1. PEGAWAI: Tampilkan Form Pengajuan
    // =======================================================

    public function ajukan()
    {
        // Cek hanya untuk level Pegawai
        if ($this->session->get('level') != $this->employeeLevel) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => 'Formulir Pengajuan Pinjaman'
        ];
        return view('pinjaman/ajuan_pinjaman', $data);
    }

    // =======================================================
    // 2. PEGAWAI: Simpan Pengajuan
    // =======================================================

    public function saveAjuan()
    {
        if ($this->session->get('level') != $this->employeeLevel) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        // 1. Validasi input
        if (!$this->validate([
            'besar_pinjaman' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[10]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Ambil data Session
        $kd_user = $this->session->get('kd_user');
        $kd_karyawan = $this->session->get('kd_karyawan'); 

        if (empty($kd_karyawan)) {
             return redirect()->back()->with('error', 'Data Karyawan tidak ditemukan dalam sesi. Harap login ulang.');
        }

        // 3. Simpan data ke PermohonanPinjamanModel
        $data = [
            'kd_user'          => $kd_user,
            'kd_karyawan'      => $kd_karyawan,
            'besar_pinjaman'   => $this->request->getPost('besar_pinjaman'),
            'keterangan'       => $this->request->getPost('keterangan'),
            'timedate'         => date('Y-m-d H:i:s'),
            'status_pengajuan' => 'PENDING', // Status awal
        ];

        $this->permohonanModel->save($data);
        
        return redirect()->to(base_url('pinjaman/status'))->with('success', 'Pengajuan Pinjaman berhasil dikirim dan menunggu persetujuan Admin.');
    }
    
    // =======================================================
    // 3. PEGAWAI: Lihat Status Pengajuan Sendiri
    // =======================================================

    public function status()
    {
        if ($this->session->get('level') != $this->employeeLevel) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $kd_karyawan = $this->session->get('kd_karyawan');
        
        // Ambil semua permohonan yang diajukan oleh karyawan yang sedang login
        $data = [
            'title' => 'Status Pengajuan Pinjaman Saya',
            'permohonan' => $this->permohonanModel->where('kd_karyawan', $kd_karyawan)->findAll()
        ];
        return view('pinjaman/status_ajuan', $data);
    }


    // =======================================================
    // ADMIN FUNCTIONS (ADMIN: Persetujuan Pinjaman)
    // =======================================================
    // Fungsi-fungsi ini untuk Admin, dapat diabaikan atau disembunyikan
    // jika Anda hanya ingin fokus pada sisi Pegawai.
    public function index()
    {
        if ($this->session->get('level') != $this->adminLevel) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        // ... Logic untuk Admin menampilkan daftar PENDING
    }
    public function approve($id)
    {
        if ($this->session->get('level') != $this->adminLevel) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        // ... Logic untuk Admin menyetujui
    }
    public function reject($id)
    {
        if ($this->session->get('level') != $this->adminLevel) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        // ... Logic untuk Admin menolak
    }
}