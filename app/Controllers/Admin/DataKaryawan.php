<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\JabatanModel;
use App\Models\UserModel;
use App\Models\PtkpModel; 
use App\Models\LokasiPresensiModel;

class DataKaryawan extends BaseController
{
    protected $karyawanModel;
    protected $jabatanModel;
    protected $userModel;
    protected $ptkpModel;
    protected $lokasiModel;
    
    private $uploadDir = 'foto';
    protected $helpers = ['form', 'url', 'filesystem']; 

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->jabatanModel = new JabatanModel();
        $this->userModel = new UserModel();
        $this->ptkpModel = new PtkpModel(); 
        $this->lokasiModel = new LokasiPresensiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Karyawan',
            'karyawan' => $this->karyawanModel->getKaryawanWithDetails(), 
        ];
        
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/karyawan', $data) 
             . view('layout_admin/footer');
    }

    public function detail($id_karyawan = null)
    {
        $karyawan = $this->karyawanModel->getKaryawanWithDetails($id_karyawan);

        if (empty($karyawan)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data karyawan tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Karyawan: ' . $karyawan['nm_karyawan'],
            'karyawan' => $karyawan,
            'ptkp' => $karyawan['id_ptkp'] ? $this->ptkpModel->find($karyawan['id_ptkp']) : null,
        ];

        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/karyawan/detail', $data) 
             . view('layout_admin/footer');
    }

    public function create()
    {
        $data = [
            'title' => 'Form Tambah Data Karyawan',
            'jabatan_list' => $this->jabatanModel->findAll(), 
            'ptkp_list' => $this->ptkpModel->findAll(),
            'lokasi_list' => $this->lokasiModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/karyawan/tambah', $data) 
             . view('layout_admin/footer');
    }

    /**
     * Menyimpan data karyawan dan data user baru (termasuk foto).
     */
    public function store()
    {
        // Mendefinisikan pesan error dasar 'Wajib diisi.'
        $required_msg = ['required' => 'Wajib diisi.'];
        
        // 1. Definisikan Aturan Validasi dengan Pesan Error Kustom (untuk form TAMBAH)
        if (!$this->validate([
            // Data Karyawan (Semua Wajib diisi)
            'nip' => [
                'rules' => 'required|is_unique[karyawan.nip]',
                'errors' => ['required' => 'Wajib diisi.', 'is_unique' => 'NIP sudah terdaftar.']
            ],
            'nm_karyawan' => ['rules' => 'required', 'errors' => $required_msg],
            'kd_jabatan' => ['rules' => 'required', 'errors' => $required_msg],
            'id_ptkp' => ['rules' => 'required', 'errors' => $required_msg],
            'kelamin' => ['rules' => 'required', 'errors' => $required_msg],
            'agama' => ['rules' => 'required', 'errors' => $required_msg],
            'alamat' => ['rules' => 'required', 'errors' => $required_msg],
            'no_telp' => ['rules' => 'required', 'errors' => $required_msg],
            'tempat_lahir' => ['rules' => 'required', 'errors' => $required_msg],
            'tgl_lahir' => ['rules' => 'required', 'errors' => $required_msg],
            'status_kawin' => ['rules' => 'required', 'errors' => $required_msg],
            'tanggal_masuk' => ['rules' => 'required', 'errors' => $required_msg],
            'jumlah_anak' => ['rules' => 'permit_empty|integer', 'errors' => ['integer' => 'Harus angka bulat.']],
            'id_lokasi_default' => ['rules' => 'permit_empty|integer'], 

            // Data User (Semua Wajib diisi di form tambah)
            'username' => ['rules' => 'required|is_unique[user.username]', 'errors' => ['required' => 'Wajib diisi.', 'is_unique' => 'Username sudah terdaftar.']],
            'password' => ['rules' => 'required', 'errors' => $required_msg],
            'level' => ['rules' => 'required', 'errors' => $required_msg],
            'foto' => [
                'rules' => 'if_exist|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]', 
                'errors' => [
                    'mime_in' => 'Format harus JPG/JPEG/PNG.',
                    'max_size' => 'Maksimal 2MB.'
                ]
            ],
        ])) {
            return redirect()->back()->withInput();
        }

        // 2. Siapkan Data Karyawan
        $karyawanData = [
            'nip' => $this->request->getPost('nip'),
            'nm_karyawan' => $this->request->getPost('nm_karyawan'),
            'kd_jabatan' => $this->request->getPost('kd_jabatan'),
            'id_lokasi_default' => $this->request->getPost('id_lokasi_default') ?: null,
            'kelamin' => $this->request->getPost('kelamin'),
            'agama' => $this->request->getPost('agama'),
            'alamat' => $this->request->getPost('alamat'),
            'no_telp' => $this->request->getPost('no_telp'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tgl_lahir' => $this->request->getPost('tgl_lahir'),
            'status_kawin' => $this->request->getPost('status_kawin'),
            'jumlah_anak' => $this->request->getPost('jumlah_anak') ?: 0,
            'id_ptkp' => $this->request->getPost('id_ptkp'), 
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
        ];
        
        $this->karyawanModel->insert($karyawanData);
        $id_karyawan_baru = $this->karyawanModel->insertID();

        // 3. Penanganan Unggahan Foto
        $fileFoto = $this->request->getFile('foto');
        $namaFoto = null;
        
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName(); 
            $fileFoto->move($this->uploadDir, $namaFoto); 
        }

        // 4. Buat Akun User
        $userData = [
            'id_karyawan' => $id_karyawan_baru,
            'username' => $this->request->getPost('username'),
            // Password di-hash
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), 
            'level' => $this->request->getPost('level'), 
            'foto' => $namaFoto, 
        ];
        $this->userModel->save($userData);

        // 5. Redirect
        return redirect()->to(base_url('admin/karyawan'))->with('pesan', 'Data Karyawan dan akun User berhasil ditambahkan!');
    }
    
    public function edit($id_karyawan = null)
    {
        $karyawan = $this->karyawanModel->find($id_karyawan);

        if (empty($karyawan)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data karyawan tidak ditemukan.');
        }
        
        $user = $this->userModel->where('id_karyawan', $id_karyawan)->first();

        $data = [
            'title' => 'Form Edit Data Karyawan',
            'karyawan' => $karyawan,
            'user' => $user,
            'jabatan_list' => $this->jabatanModel->findAll(),
            'ptkp_list' => $this->ptkpModel->findAll(), 
            'lokasi_list' => $this->lokasiModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/karyawan/edit', $data) 
             . view('layout_admin/footer');
    }

    /**
     * Memperbarui data karyawan dan data user terkait.
     */
    public function update()
    {
        $id_karyawan = $this->request->getPost('id_karyawan');
        $kd_user = $this->request->getPost('kd_user'); 
        $fotoLama = $this->request->getPost('foto_lama'); 
        
        $required_msg = ['required' => 'Wajib diisi.'];
        
        // Aturan validasi UPDATE - Password bersifat optional jika KD_USER sudah ada.
        if (!$this->validate([
            'nip' => ['rules' => 'required|is_unique[karyawan.nip,id_karyawan,'.$id_karyawan.']', 'errors' => ['required' => 'Wajib diisi.', 'is_unique' => 'NIP sudah terdaftar.']],
            'nm_karyawan' => ['rules' => 'required', 'errors' => $required_msg],
            'kd_jabatan' => ['rules' => 'required', 'errors' => $required_msg],
            'id_ptkp' => ['rules' => 'required', 'errors' => $required_msg],
            'kelamin' => ['rules' => 'required', 'errors' => $required_msg],
            'agama' => ['rules' => 'required', 'errors' => $required_msg],
            'alamat' => ['rules' => 'required', 'errors' => $required_msg],
            'no_telp' => ['rules' => 'required', 'errors' => $required_msg],
            'tempat_lahir' => ['rules' => 'required', 'errors' => $required_msg],
            'tgl_lahir' => ['rules' => 'required', 'errors' => $required_msg],
            'status_kawin' => ['rules' => 'required', 'errors' => $required_msg],
            'tanggal_masuk' => ['rules' => 'required', 'errors' => $required_msg],
            'jumlah_anak' => ['rules' => 'permit_empty|integer', 'errors' => ['integer' => 'Harus angka bulat.']],
            'id_lokasi_default' => ['rules' => 'permit_empty|integer'], 
            
            // Aturan User: Gunakan 'required' hanya jika akun baru (kd_user kosong)
            'username' => ['rules' => 'required|is_unique[user.username,kd_user,'.$kd_user.']', 'errors' => ['required' => 'Wajib diisi.', 'is_unique' => 'Username sudah terdaftar.']],
            'password' => ['rules' => $kd_user ? 'permit_empty' : 'required', 'errors' => ['required' => 'Wajib diisi.']], 
            'level' => ['rules' => 'required', 'errors' => $required_msg],
            'foto' => [
                'rules' => 'if_exist|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]', 
                'errors' => ['mime_in' => 'Format harus JPG/JPEG/PNG.', 'max_size' => 'Maksimal 2MB.']
            ],
        ])) {
            return redirect()->back()->withInput();
        }
        
        // 2. Siapkan Data Karyawan Update
        $karyawanData = [
            'id_karyawan' => $id_karyawan,
            'nip' => $this->request->getPost('nip'),
            'nm_karyawan' => $this->request->getPost('nm_karyawan'),
            'kd_jabatan' => $this->request->getPost('kd_jabatan'),
            'id_lokasi_default' => $this->request->getPost('id_lokasi_default') ?: null,
            'kelamin' => $this->request->getPost('kelamin'),
            'agama' => $this->request->getPost('agama'),
            'alamat' => $this->request->getPost('alamat'),
            'no_telp' => $this->request->getPost('no_telp'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tgl_lahir' => $this->request->getPost('tgl_lahir'),
            'status_kawin' => $this->request->getPost('status_kawin'),
            'jumlah_anak' => $this->request->getPost('jumlah_anak') ?: 0,
            'id_ptkp' => $this->request->getPost('id_ptkp'), 
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
        ];
        
        $this->karyawanModel->save($karyawanData);
        
        // 3. Penanganan Unggahan Foto Baru
        $fileFoto = $this->request->getFile('foto');
        $namaFoto = $fotoLama; 

        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            if ($fotoLama && is_file($this->uploadDir . '/' . $fotoLama)) {
                 unlink($this->uploadDir . '/' . $fotoLama);
            }
            $namaFoto = $fileFoto->getRandomName(); 
            $fileFoto->move($this->uploadDir, $namaFoto); 
        }
        
        // 4. Update/Buat Data User
        $userData = [
            'username' => $this->request->getPost('username'),
            'level' => $this->request->getPost('level'),
            'foto' => $namaFoto,
        ];

        if ($kd_user) {
            // Update User yang sudah ada
            $userData['kd_user'] = $kd_user;
            $newPassword = $this->request->getPost('password');
            if (!empty($newPassword)) {
                $userData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
            $this->userModel->save($userData);
        } else {
            // Buat User baru
            $userData['id_karyawan'] = $id_karyawan;
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            $this->userModel->save($userData);
        }

        // 5. Redirect
        return redirect()->to(base_url('admin/karyawan'))->with('pesan', 'Data Karyawan berhasil diupdate!');
    }

    public function delete($id_karyawan = null)
    {
        $user = $this->userModel->where('id_karyawan', $id_karyawan)->first();

        if ($user && isset($user['foto']) && is_file($this->uploadDir . '/' . $user['foto'])) {
            unlink($this->uploadDir . '/' . $user['foto']);
        }

        $this->userModel->where('id_karyawan', $id_karyawan)->delete();
        $this->karyawanModel->delete($id_karyawan);

        return redirect()->to(base_url('admin/karyawan'))->with('pesan', 'Data Karyawan dan akun User terkait berhasil dihapus!');
    }

    public function sinkronisasi_wajah()
    {
        $data = [
            'title'    => 'Sinkronisasi Wajah Karyawan',
            'karyawan' => $this->karyawanModel->getKaryawanWithDetails()
        ];

        echo view('layout_admin/head', $data);
        echo view('layout_admin/sidebar');
        echo view('pages_admin/karyawan/sync_face', $data);
        echo view('layout_admin/footer');
    }

    // Menerima kiriman data descriptor dari JavaScript
    public function update_face_descriptor()
    {
        $json = $this->request->getJSON();
        if ($json && isset($json->id_karyawan)) {
            $this->karyawanModel->update($json->id_karyawan, [
                'face_descriptor' => $json->face_descriptor
            ]);
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error'], 400);
    }
}