<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KetidakhadiranModel; 
use App\Models\PermohonanPinjamanModel;
class Dashboard extends BaseController
{
    public function index()
    {
        $today = date('Y-m-d');
        $karyawanModel = new \App\Models\KaryawanModel();
        $userModel = new \App\Models\LoginModel();
        $jabatanModel = new \App\Models\JabatanModel();
        $presensiModel = new \App\Models\PresensiModel(); 
        $ketidakhadiranModel = new \App\Models\KetidakhadiranModel(); 
        $pinjamanModel = new \App\Models\PermohonanPinjamanModel();

    
        $data['title'] = "Dashboard";
        $total_karyawan = $karyawanModel->countAllResults();
        $data['karyawan'] = $total_karyawan;
        $data['admin'] = $userModel->where('level', 'Admin')->countAllResults();
        $data['jabatan'] = $jabatanModel->countAllResults();
        
        // --- LOGIKA BARU: Total Permohonan Pending Hari Ini ---
        $ketidakhadiran_hari_ini = $ketidakhadiranModel
            ->where('tanggal', $today)
            ->where('status_pengajuan', 'Pending') // <-- HITUNG YANG PENDING
            ->countAllResults();
        
        // VARIABEL UNTUK VIEW
        $data['ketidakhadiran_hari_ini'] = $ketidakhadiran_hari_ini;
        
        // Data Presensi Keseluruhan 
        $data['presensi'] = $presensiModel->countAllResults(); 
        
        // Data Chart Jenis Kelamin
        $data['pria'] = $karyawanModel->where('kelamin', 'Laki-laki')->countAllResults();
        $data['wanita'] = $karyawanModel->where('kelamin', 'Perempuan')->countAllResults();

        $data['permohonan_pinjaman'] = $pinjamanModel
            ->where('status_pengajuan', 'Pending') // <== Ganti 'status_pinjaman' dan 'Pending' sesuai field DB Anda
            ->countAllResults();

        // Data Chart Kehadiran Hari Ini
        $hadir_hari_ini_builder = $presensiModel->builder()
            ->select('kd_karyawan')
            ->where('tanggal_masuk', $today)
            ->where('jam_masuk IS NOT NULL')
            ->distinct()
            ->get();
            
        $hadir_hari_ini = $hadir_hari_ini_builder->getNumRows();
        
        // Variabel untuk View
        $data['hadir'] = $hadir_hari_ini;
        $data['belum_hadir'] = $total_karyawan - $hadir_hari_ini;
        
        return view('layout_admin/head', $data)
            . view('layout_admin/sidebar')
            . view('pages_admin/dashboard', $data)
            . view('layout_admin/footer');
    }
}