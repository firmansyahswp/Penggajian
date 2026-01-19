<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        // Inisialisasi Model yang diperlukan
        $karyawanModel = new \App\Models\KaryawanModel();
        
        // Mengambil ID Karyawan dari kunci session yang benar ('user_id')
        $id_karyawan = session()->get('user_id'); 
        
        $data['karyawan'] = []; // Default data karyawan kosong
        
        if ($id_karyawan) {
            
            // Lakukan Query Detail Karyawan
            $karyawan_detail = $karyawanModel
                                // Pilih semua kolom karyawan dan nama jabatan (di-alias 'jabatan')
                                ->select('karyawan.*, jabatan.nm_jabatan AS jabatan')
                                ->join('jabatan', 'jabatan.kd_jabatan = karyawan.kd_jabatan') // JOIN ke tabel jabatan
                                // JOIN ke tabel user tidak diperlukan lagi, karena foto sudah ada di session
                                ->where('karyawan.id_karyawan', $id_karyawan) 
                                ->first();
                                
            // Tambahkan kolom 'foto' dari session ke hasil query sebelum dikirim ke view
            if ($karyawan_detail) {
                // Tambahkan nama file foto (yang diambil dari session saat login)
                $karyawan_detail['foto'] = session()->get('foto'); 
                // Konversi hasil array/object tunggal menjadi array dengan satu elemen.
                $data['karyawan'] = [$karyawan_detail];
            }
        }
        
        $data['title'] = "Dashboard";

        // Memuat semua views yang dibutuhkan
        return view('pegawai/head', $data)
             . view('pegawai/sidebar')
             . view('pages_pegawai/dashboard', $data) 
             . view('pegawai/footer');   
    }
}