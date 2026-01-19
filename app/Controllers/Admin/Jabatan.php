<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JabatanModel; // Pastikan model ini di-use

class Jabatan extends BaseController
{
    protected $jabatanModel;
    protected $helpers = ['form']; // Tambahkan helper form untuk menangani input

    public function __construct()
    {
        // Inisialisasi Model
        $this->jabatanModel = new JabatanModel();
    }

    /**
     * Menampilkan daftar semua data jabatan (Tabel)
     */
    public function index()
    {
        $data = [
            'title' => 'Data Jabatan',
            'jabatan' => $this->jabatanModel->findAll() 
        ];
        
        // Memuat View (Tabel Data)
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/jabatan', $data) // View tabel
             . view('layout_admin/footer');
    }

    /**
     * Menampilkan form untuk menambah data jabatan (View Form)
     */
    public function create()
    {
        $data = [
            'title' => 'Form Tambah Data Jabatan',
            // Kirim aturan validasi ke view (Opsional, tapi praktik yang baik)
            'validation' => \Config\Services::validation() 
        ];
        
        // Memuat View (Form)
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/jabatan/tambah', $data) 
             . view('layout_admin/footer');
    }

    public function store()
    {
        $jabatanModel = new \App\Models\JabatanModel();

        $nm_jabatan = $this->request->getPost('nm_jabatan');

        $cekData = $jabatanModel->where('nm_jabatan', $nm_jabatan)->first();

        if ($cekData) {
            // Jika data ada, kembalikan ke form dengan pesan error
            return redirect()->back()->withInput()->with('gagal', "Data jabatan '$nm_jabatan' sudah ada dalam sistem!");
        }

        if (!$this->validate([
            'nm_jabatan' => [
                'rules' => 'required|is_unique[jabatan.nm_jabatan]',
                'errors' => ['required' => 'Nama Jabatan wajib diisi.', 'is_unique' => 'Nama Jabatan sudah terdaftar.']
            ],
            'gaji_pokok' => ['rules' => 'required|numeric', 'errors' => ['required' => 'Gaji Pokok wajib diisi.', 'numeric' => 'Gaji Pokok harus berupa angka.']],
            'uang_transport' => ['rules' => 'required|numeric', 'errors' => ['required' => 'Uang Transport wajib diisi.', 'numeric' => 'Harus berupa angka.']],
            'uang_makan' => ['rules' => 'required|numeric', 'errors' => ['required' => 'Uang Makan wajib diisi.', 'numeric' => 'Harus berupa angka.']],
        ])) {
            return redirect()->back()->withInput();
        }

        // 2. Jika Validasi Berhasil, Simpan Data
        $data = [
            'nm_jabatan'     => $this->request->getPost('nm_jabatan'),
            'gaji_pokok'     => $this->request->getPost('gaji_pokok'),
            'uang_transport' => $this->request->getPost('uang_transport'),
            'uang_makan'     => $this->request->getPost('uang_makan'),
        ];

        $this->jabatanModel->save($data);

        // 3. Redirect dengan pesan sukses (Flashdata)
        return redirect()->to(base_url('admin/jabatan'))->with('pesan', 'Data Jabatan berhasil ditambahkan!');
    }
    
    public function edit($id = null)
    {
        // Cari data berdasarkan Primary Key (kd_jabatan)
        $jabatan = $this->jabatanModel->find($id);

        if (empty($jabatan)) {
            // Jika data tidak ditemukan
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data jabatan tidak ditemukan.');
        }

        $data = [
            'title' => 'Form Edit Data Jabatan',
            // Kirim data yang ditemukan ke View dalam bentuk array tunggal
            'jabatan' => $jabatan, 
            'validation' => \Config\Services::validation()
        ];
        
        // Memuat View Edit Form
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/jabatan/edit', $data) // Asumsi nama file View Edit Anda
             . view('layout_admin/footer');
    }

    /**
     * Memproses data yang dikirim dari form edit dan memperbarui DB.
     */
    public function update()
    {
        $id = $this->request->getPost('kd_jabatan'); // Ambil ID dari input hidden

        // 1. Definisikan Aturan Validasi
        // Cek apakah nama jabatan unik, kecuali untuk data yang sedang diupdate
        $rule_nm_jabatan = 'required|is_unique[jabatan.nm_jabatan,kd_jabatan,'.$id.']';
        
        if (!$this->validate([
            'nm_jabatan'     => ['rules' => $rule_nm_jabatan, 'errors' => ['required' => 'Nama Jabatan wajib diisi.', 'is_unique' => 'Nama Jabatan sudah terdaftar.']],
            'gaji_pokok'     => ['rules' => 'required|numeric'],
            'uang_transport' => ['rules' => 'required|numeric'],
            'uang_makan'     => ['rules' => 'required|numeric'],
        ])) {
            // Jika validasi GAGAL, kembali ke form edit dengan error
            return redirect()->back()->withInput();
        }

        // 2. Jika Validasi Berhasil, Siapkan Data Update
        $data = [
            'kd_jabatan'     => $id, // Penting: Menyertakan ID untuk update
            'nm_jabatan'     => $this->request->getPost('nm_jabatan'),
            'gaji_pokok'     => $this->request->getPost('gaji_pokok'),
            'uang_transport' => $this->request->getPost('uang_transport'),
            'uang_makan'     => $this->request->getPost('uang_makan'),
        ];

        // 3. Eksekusi Update
        $this->jabatanModel->save($data);

        // 4. Redirect
        return redirect()->to(base_url('admin/jabatan'))->with('pesan', 'Data Jabatan berhasil diupdate!');
    }

    public function delete($id = null)
    {
        // Eksekusi penghapusan
        $this->jabatanModel->delete($id);

        // Redirect
        return redirect()->to(base_url('admin/jabatan'))->with('pesan', 'Data Jabatan berhasil dihapus!');
    }
}