<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PotonganGajiModel;
use App\Models\KaryawanModel; 
use CodeIgniter\I18n\Time;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PotonganGaji extends BaseController
{
    protected $potonganModel;
    protected $karyawanModel;
    protected $timezone = 'Asia/Jakarta';
    
    protected $helpers = ['form', 'number'];

    public function __construct()
    {
        \date_default_timezone_set($this->timezone);
        $this->potonganModel = new PotonganGajiModel();
        $this->karyawanModel = new KaryawanModel();
    }

    // =======================================================
    // 1. List Potongan Gaji (GET /admin/potongan)
    // =======================================================
    public function index()
    {
        $periode = $this->request->getGet('periode') ?? date('Y-m'); 

        $data = [
            'title' => 'Manajemen Potongan Gaji',
            'list_potongan' => $this->potonganModel->getPotonganByPeriode($periode),
            'selectedPeriode' => $periode,
            'listPeriode' => $this->getListPeriode()
        ];

        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/potongan_gaji/index', $data) 
             . view('layout_admin/footer');
    }
    
    // ... (Fungsi create dan store tetap sama)
    
    public function create()
    {
        $data = [
            'title' => 'Tambah Potongan Gaji',
            'list_karyawan' => $this->karyawanModel->findAll(),
            'validation' => \Config\Services::validation(),
            'potongan' => null // Untuk form yang sama
        ];
        
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/potongan_gaji/create', $data) 
             . view('layout_admin/footer');
    }

    public function store()
    {
        $rules = [
            'kd_karyawan'    => 'required|numeric',
            'jenis_potongan' => 'required|max_length[50]',
            'besar_potongan' => 'required|numeric|greater_than[0]',
            'periode_gaji'   => 'required|regex_match[/^\d{4}-\d{2}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->potonganModel->save([
            'kd_karyawan'    => $this->request->getPost('kd_karyawan'),
            'jenis_potongan' => $this->request->getPost('jenis_potongan'),
            'besar_potongan' => $this->request->getPost('besar_potongan'),
            'periode_gaji'   => $this->request->getPost('periode_gaji'),
            'keterangan'     => $this->request->getPost('keterangan'),
            'timedate'       => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/potongan'))->with('success', 'Potongan gaji berhasil dicatat.');
    }

    // =======================================================
    // 3. Form Edit Potongan (GET /admin/potongan/edit/{id})
    // =======================================================
    public function edit($id)
    {
        $potongan = $this->potonganModel->find($id);

        if (!$potongan) {
            return redirect()->to(base_url('admin/potongan'))->with('error', 'Data potongan tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Potongan Gaji',
            'potongan' => $potongan,
            'list_karyawan' => $this->karyawanModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        // Menggunakan view yang sama dengan CREATE, tetapi diisi data
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/potongan_gaji/create', $data) 
             . view('layout_admin/footer');
    }

    // =======================================================
    // 4. Update Potongan (POST /admin/potongan/update/{id})
    // =======================================================
    public function update($id)
    {
        $potongan = $this->potonganModel->find($id);

        if (!$potongan) {
            return redirect()->to(base_url('admin/potongan'))->with('error', 'Data potongan tidak ditemukan.');
        }
        
        $rules = [
            'kd_karyawan'    => 'required|numeric',
            'jenis_potongan' => 'required|max_length[50]',
            'besar_potongan' => 'required|numeric|greater_than[0]',
            'periode_gaji'   => 'required|regex_match[/^\d{4}-\d{2}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->potonganModel->update($id, [
            'kd_karyawan'    => $this->request->getPost('kd_karyawan'),
            'jenis_potongan' => $this->request->getPost('jenis_potongan'),
            'besar_potongan' => $this->request->getPost('besar_potongan'),
            'periode_gaji'   => $this->request->getPost('periode_gaji'),
            'keterangan'     => $this->request->getPost('keterangan'),
            // timedate tidak diupdate
        ]);

        return redirect()->to(base_url('admin/potongan'))->with('success', 'Potongan gaji berhasil diperbarui.');
    }
    
    // =======================================================
    // 5. Delete Potongan (GET /admin/potongan/delete/{id})
    // =======================================================
    public function delete($id)
    {
        $potongan = $this->potonganModel->find($id);

        if (!$potongan) {
            return redirect()->to(base_url('admin/potongan'))->with('error', 'Data potongan tidak ditemukan.');
        }

        try {
            $this->potonganModel->delete($id);
        } catch (DatabaseException $e) {
            return redirect()->to(base_url('admin/potongan'))->with('error', 'Gagal menghapus potongan. Mungkin data ini sudah digunakan dalam laporan gaji final.');
        }

        return redirect()->to(base_url('admin/potongan'))->with('success', 'Potongan gaji berhasil dihapus.');
    }

    // ... (Helper function getListPeriode tetap sama)
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