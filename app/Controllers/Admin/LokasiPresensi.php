<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LokasiPresensiModel; 

class LokasiPresensi extends BaseController
{
    protected $lokasiPresensiModel;
    protected $helpers = ['form']; 

    public function __construct()
    {
        // Inisialisasi Model LokasiPresensi
        $this->lokasiPresensiModel = new LokasiPresensiModel();
        // Memuat form helper, meskipun sudah di BaseController, ini memastikan ketersediaan.
        helper('form'); 
    }

    /**
     * Menampilkan daftar semua data lokasi presensi (Tabel)
     */
    public function index()
    {
        $data = [
            'title' => 'Data Lokasi Presensi',
            'lokasi_presensi' => $this->lokasiPresensiModel->findAll() 
        ];
        
        // Memuat View (Tabel Data)
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/lokasipresensi', $data) 
             . view('layout_admin/footer');
    }

    /**
     * Menampilkan form untuk menambah data lokasi presensi (View Form)
     */
    public function create()
    {
        $data = [
            'title' => 'Form Tambah Data Lokasi Presensi',
            'validation' => \Config\Services::validation() 
        ];
        
        // Memuat View (Form)
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/lokasi_presensi/tambah', $data) 
             . view('layout_admin/footer');
    }

    /**
     * Memproses data yang dikirim dari form tambah dan menyimpannya ke DB.
     */
    public function store()
    {
        // 1. Definisikan Aturan Validasi untuk Lokasi Presensi
        if (!$this->validate([
            'nama_lokasi' => [
                'rules' => 'required|is_unique[lokasi_presensi.nama_lokasi]',
                'errors' => ['required' => 'Nama Lokasi wajib diisi.', 'is_unique' => 'Nama Lokasi sudah terdaftar.']
            ],
            'alamat_lokasi' => ['rules' => 'required', 'errors' => ['required' => 'Alamat Lokasi wajib diisi.']],
            'tipe_lokasi' => ['rules' => 'required', 'errors' => ['required' => 'Tipe Lokasi wajib dipilih.']],
            'latitude' => ['rules' => 'required', 'errors' => ['required' => 'Latitude wajib diisi.']],
            'longitude' => ['rules' => 'required', 'errors' => ['required' => 'Longitude wajib diisi.']],
            'radius' => ['rules' => 'required|integer', 'errors' => ['required' => 'Radius wajib diisi.', 'integer' => 'Radius harus berupa angka bulat.']],
            'zona_waktu' => ['rules' => 'required', 'errors' => ['required' => 'Zona Waktu wajib diisi.']],
            'jam_masuk' => ['rules' => 'required', 'errors' => ['required' => 'Jam Masuk wajib diisi.']],
            'jam_pulang' => ['rules' => 'required', 'errors' => ['required' => 'Jam Pulang wajib diisi.']],
        ])) {
            // Jika Validasi Gagal, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput();
        }

        // 2. Jika Validasi Berhasil, Simpan Data
        $data = [
            'nama_lokasi'   => $this->request->getPost('nama_lokasi'),
            'alamat_lokasi' => $this->request->getPost('alamat_lokasi'), 
            'tipe_lokasi'   => $this->request->getPost('tipe_lokasi'),
            'latitude'      => $this->request->getPost('latitude'),
            'longitude'     => $this->request->getPost('longitude'),
            'radius'        => $this->request->getPost('radius'),
            'zona_waktu'    => $this->request->getPost('zona_waktu'),
            'jam_masuk'     => $this->request->getPost('jam_masuk'),
            'jam_pulang'    => $this->request->getPost('jam_pulang'),
        ];

        $this->lokasiPresensiModel->save($data);

        // 3. Redirect dengan pesan sukses (Flashdata)
        return redirect()->to(base_url('admin/lokasipresensi'))->with('pesan', 'Data Lokasi Presensi berhasil ditambahkan!');
    }
    
    /**
     * Menampilkan form untuk mengedit data berdasarkan ID (Primary Key)
     */
    public function edit($id = null)
    {
        // Cari data berdasarkan Primary Key (id)
        $lokasi = $this->lokasiPresensiModel->find($id);

        if (empty($lokasi)) {
            // Jika data tidak ditemukan
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data lokasi presensi tidak ditemukan.');
        }

        $data = [
            'title' => 'Form Edit Data Lokasi Presensi',
            // Kirim data yang ditemukan ke View dalam bentuk array tunggal
            'lokasi' => $lokasi, 
            'validation' => \Config\Services::validation()
        ];
        
        // Memuat View Edit Form
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/lokasi_presensi/edit', $data) // Asumsi nama file View Edit
             . view('layout_admin/footer');
    }

    /**
     * Memproses data yang dikirim dari form edit dan memperbarui DB.
     * Menerima ID lokasi dari URL (sesuai route: /lokasipresensi/update/ID)
     */
    public function update($id_dari_url = null)
    {
        // Ambil ID dari input hidden (Primary Key di LokasiPresensi adalah 'id')
        $id = $this->request->getPost('id'); 

        // 1. Definisikan Aturan Validasi
        // Cek apakah nama lokasi unik, kecuali untuk data yang sedang diupdate
        $rule_nama_lokasi = 'required|is_unique[lokasi_presensi.nama_lokasi,id,'.$id.']';
        
        if (!$this->validate([
            'nama_lokasi'   => ['rules' => $rule_nama_lokasi, 'errors' => ['required' => 'Nama Lokasi wajib diisi.', 'is_unique' => 'Nama Lokasi sudah terdaftar.']],
            'alamat_lokasi' => ['rules' => 'required', 'errors' => ['required' => 'Alamat Lokasi wajib diisi.']],
            'tipe_lokasi'   => ['rules' => 'required'],
            'latitude'      => ['rules' => 'required'],
            'longitude'     => ['rules' => 'required'],
            'radius'        => ['rules' => 'required|integer'],
            'zona_waktu'    => ['rules' => 'required'],
            'jam_masuk'     => ['rules' => 'required'],
            'jam_pulang'    => ['rules' => 'required'],
        ])) {
            // Jika validasi GAGAL, kembali ke form edit dengan error
            return redirect()->back()->withInput();
        }

        // 2. Jika Validasi Berhasil, Siapkan Data Update
        $data = [
            'id'            => $id, // Penting: Menyertakan ID untuk update
            'nama_lokasi'   => $this->request->getPost('nama_lokasi'),
            'alamat_lokasi' => $this->request->getPost('alamat_lokasi'), 
            'tipe_lokasi'   => $this->request->getPost('tipe_lokasi'),
            'latitude'      => $this->request->getPost('latitude'),
            'longitude'     => $this->request->getPost('longitude'),
            'radius'        => $this->request->getPost('radius'),
            'zona_waktu'    => $this->request->getPost('zona_waktu'),
            'jam_masuk'     => $this->request->getPost('jam_masuk'),
            'jam_pulang'    => $this->request->getPost('jam_pulang'),
        ];

        // 3. Eksekusi Update
        $this->lokasiPresensiModel->save($data);

        // 4. Redirect
        return redirect()->to(base_url('admin/lokasipresensi'))->with('pesan', 'Data Lokasi Presensi berhasil diupdate!');
    }

    /**
     * Menampilkan rincian (detail) data lokasi presensi berdasarkan ID.
     */
    public function detail($id = null)
    {
        $lokasi = $this->lokasiPresensiModel->find($id);

        if (empty($lokasi)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data lokasi presensi tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Lokasi Presensi: ' . $lokasi['nama_lokasi'],
            'lokasi' => $lokasi,
        ];

        // Memuat View Detail
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/lokasi_presensi/detail', $data) 
             . view('layout_admin/footer');
    }

    /**
     * Menghapus data berdasarkan ID (Primary Key)
     */
    public function delete($id = null)
    {
        // Eksekusi penghapusan
        $this->lokasiPresensiModel->delete($id);

        // Redirect
        return redirect()->to(base_url('admin/lokasipresensi'))->with('pesan', 'Data Lokasi Presensi berhasil dihapus!');
    }
}