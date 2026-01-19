<?php

namespace App\Controllers;

use App\Models\LoginModel;
use App\Models\KaryawanModel; // Digunakan untuk mengambil nama lengkap karyawan
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{
    public function index()
    {
        $data = [
            'validation' => service('validation'),
        ];
        return view('login', $data);
    }

    public function login_action()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if(!$this->validate($rules)){
            $data['validation'] = $this->validator;
            return view ('login',$data);
        }else{
            $session = session();
            $loginModel = new LoginModel;
            $karyawanModel = new KaryawanModel; // Inisialisasi KaryawanModel

            $username = $this->request ->getVar('username');
            $password = $this->request ->getVar('password');
            $cekusername = $loginModel->where('username', $username)->first();
            
            if($cekusername) {
                $password_db = $cekusername['password'];
                $cek_password = password_verify($password, $password_db);
                
                if($cek_password){
                    // ASUMSI: Tabel login (cekusername) memiliki kolom 'id_karyawan'
                    // Kolom ini akan digunakan untuk mencari data di tabel karyawan.
                    $id_karyawan = $cekusername['id_karyawan'] ?? null; // Gunakan id_karyawan dari tabel login
                    
                    $nama_lengkap = null;
                    
                    if ($id_karyawan) {
                        // Ambil data karyawan berdasarkan id_karyawan
                        $data_karyawan = $karyawanModel->find($id_karyawan);
                        
                        if ($data_karyawan && isset($data_karyawan['nm_karyawan'])) {
                            // Ambil nama dari kolom 'nm_karyawan' di tabel karyawan
                            $nama_lengkap = $data_karyawan['nm_karyawan'];
                        }
                    }

                    // --- SIMPAN DATA KE SESSION ---
                    $session_data = [
                        'logged_in' => TRUE,
                        'kd_user'   => $cekusername['kd_user'] ?? $cekusername['id'],
                        'user_id'   => $cekusername['id_karyawan'], // ID Karyawan
                        'username'  => $cekusername['username'], // Username dari tabel login
                        'nama'      => $nama_lengkap, // Nama lengkap dari tabel karyawan
                        'role_id'   => $cekusername['level'],
                        'foto'      => $cekusername['foto'] ?? 'default.jpg'
                    ];
                    
                    $session->set($session_data);
                    
                    $user_level = strtolower($cekusername['level']);
                    switch($user_level){
                        case "admin":
                            return redirect()->to('admin/dashboard');
                        case "pegawai":
                            return redirect()->to('pegawai/dashboard');
                        default:
                           $session->setFlashdata('pesan','Akun Anda belum terdaftar');
                           return redirect()->to('/');
                    }
                }else{
                    $session->setFlashdata('pesan','Password salah, silahkan coba lagi');
                    return redirect()->to('/');
                }
            }else{
                $session->setFlashdata('pesan','Username salah, silahkan coba lagi');
                return redirect()->to('/');
            }
        }
    }

    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }
}