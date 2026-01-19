<?php

namespace App\Controllers;

use App\Models\UserModel;

class UbahPassword extends BaseController
{
    protected $userModel;
    protected $session;
    protected $helpers = ['form', 'session']; // Memastikan session helper dimuat

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Menampilkan formulir Ubah Password.
     */
    public function index()
    {
        // Mendapatkan segment URI pertama (misalnya 'admin' atau 'pegawai')
        $level_prefix = service('uri')->getSegment(1); 
        
        $data = [
            'title' => 'Ubah Password',
            'validation' => \Config\Services::validation()
        ];

        // LOGIKA PENENTUAN LAYOUT BERDASARKAN URL PREFIX
        if ($level_prefix == 'admin') {
            $viewPath = 'pages_admin/ubah_password/index';
            return view('layout_admin/head', $data)
                 . view('layout_admin/sidebar')
                 . view($viewPath, $data)
                 . view('layout_admin/footer');
        } else { // Jika bukan 'admin' (asumsi 'pegawai')
            $viewPath = 'pages_pegawai/ubah_password/index';
            return view('pegawai/head', $data)
                 . view('pegawai/sidebar')
                 . view($viewPath, $data)
                 . view('pegawai/footer');
        }
    }

    /**
     * Memproses permintaan perubahan password.
     */
    public function update($id)
    {
        $userId = $this->session->get('kd_user'); 
        $userLevel = $this->session->get('level');
        $level_prefix = service('uri')->getSegment(1); // Mendapatkan prefix URL saat ini

        // PENTING: Jika id_user tidak ada, redirect ke login
        if (!$userId) {
             return redirect()->to(base_url('login'))->with('error', 'Sesi berakhir.');
        }

        $rules = [
            'password_lama' => 'required',
            'password_baru' => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password_baru]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $passwordLama = $this->request->getPost('password_lama');
        $user = $this->userModel->find($userId);

        // 1. Verifikasi Password Lama
        if (!password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->with('error', 'Password Lama salah. Gagal memperbarui password.');
        }

        // 2. Update Password
        $this->userModel->updatePassword($userId, $this->request->getPost('password_baru'));

        // REDIRECT KE RUTE YANG SESUAI DENGAN PREFIX URL
        $redirectUrl = base_url($level_prefix . '/ubah-password');
        
        return redirect()->to($redirectUrl)->with('success', 'Password berhasil diperbarui.');
    }
}