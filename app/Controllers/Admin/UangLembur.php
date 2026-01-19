<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UangLemburModel;
use App\Models\KaryawanModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class UangLembur extends BaseController
{
    protected $uangLemburModel;
    protected $karyawanModel;
    protected $timezone = 'Asia/Jakarta';
    protected $helpers = ['form'];

    public function __construct()
    {
        \date_default_timezone_set($this->timezone);
        $this->uangLemburModel = new UangLemburModel();
        $this->karyawanModel = new KaryawanModel();
    }
    
    // =======================================================
    // 1. READ: List Uang Lembur (index)
    // =======================================================
    public function index()
    {
        $periode = $this->request->getGet('periode') ?? date('Y-m'); 

        $data = [
            'title' => 'Manajemen Uang Lembur',
            'list_lembur' => $this->uangLemburModel->getLemburWithKaryawan($periode),
            'selectedPeriode' => $periode,
            'listPeriode' => $this->getListPeriode()
        ];

        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/uang_lembur/index', $data) 
             . view('layout_admin/footer');
    }
    
    // =======================================================
    // 2. CREATE/UPDATE: Form Tambah/Edit
    // =======================================================
    public function create()
    {
        $data = [
            'title' => 'Tambah Uang Lembur Manual',
            'list_karyawan' => $this->karyawanModel->findAll(),
            'validation' => \Config\Services::validation(),
            'lembur' => null 
        ];
        
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/uang_lembur/create_edit', $data) 
             . view('layout_admin/footer');
    }
    
    public function edit($id)
    {
        $lembur = $this->uangLemburModel->find($id);

        if (!$lembur) {
            return redirect()->to(base_url('admin/lembur'))->with('error', 'Data lembur tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Uang Lembur',
            'lembur' => $lembur,
            'list_karyawan' => $this->karyawanModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/uang_lembur/create_edit', $data) 
             . view('layout_admin/footer');
    }
    
    // =======================================================
    // 3. STORE/UPDATE: Simpan Data
    // =======================================================
    public function store()
    {
        $id = $this->request->getPost('id');
        $isUpdate = !empty($id);
        
        $rules = [
            'kd_karyawan'    => 'required|numeric',
            'besar_lembur'   => 'required|numeric|greater_than_equal_to[0]',
            'periode_gaji'   => 'required|regex_match[/^\d{4}-\d{2}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id'             => $isUpdate ? $id : null,
            'kd_karyawan'    => $this->request->getPost('kd_karyawan'),
            'besar_lembur'   => $this->request->getPost('besar_lembur'),
            'periode_gaji'   => $this->request->getPost('periode_gaji'),
            'keterangan'     => $this->request->getPost('keterangan'),
            'timedate'       => date('Y-m-d H:i:s'),
        ];
        
        // Catatan: Anda mungkin perlu menambahkan logika untuk mencegah double entry per karyawan per periode
        
        $this->uangLemburModel->save($data);

        $message = $isUpdate ? 'diperbarui' : 'dicatat';
        return redirect()->to(base_url('admin/lembur'))->with('success', "Uang lembur berhasil $message.");
    }
    
    // =======================================================
    // 4. DELETE: Hapus Data
    // =======================================================
    public function delete($id)
    {
        $this->uangLemburModel->delete($id);
        return redirect()->to(base_url('admin/lembur'))->with('success', 'Uang lembur berhasil dihapus.');
    }

    // Helper untuk list periode (dari GajiController.php)
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