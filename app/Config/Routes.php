<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Rute Publik (Login/Logout)
$routes->get('/', 'Login::index');
$routes->get('login', 'Login::index'); // TAMBAHKAN BARIS INI
$routes->post('login', 'Login::login_action');
$routes->get('logout', 'Login::logout');

// =========================================================================
// RUTE ADMINISTRATOR (Menggunakan Grouping untuk Filter)
// =========================================================================
$routes->group('admin', ['filter' => 'adminFilter'], function($routes) {

    // Dashboard
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Jabatan
    $routes->get('jabatan', 'Admin\Jabatan::index');
    $routes->get('jabatan/create', 'Admin\Jabatan::create');
    $routes->post('jabatan/store', 'Admin\Jabatan::store');
    $routes->get('jabatan/edit/(:segment)', 'Admin\Jabatan::edit/$1');
    $routes->post('jabatan/update/(:segment)', 'Admin\Jabatan::update/$1');
    $routes->get('jabatan/delete/(:segment)', 'Admin\Jabatan::delete/$1');
    
    $routes->get('datakaryawan/sinkronisasi_wajah', 'Admin\DataKaryawan::sinkronisasi_wajah');
    $routes->post('datakaryawan/update_face_descriptor', 'Admin\DataKaryawan::update_face_descriptor');
    
    // Lokasi Presensi
    $routes->get('lokasipresensi', 'Admin\LokasiPresensi::index');
    $routes->get('lokasipresensi/create', 'Admin\LokasiPresensi::create');
    $routes->post('lokasipresensi/store', 'Admin\LokasiPresensi::store');
    $routes->get('lokasipresensi/edit/(:segment)', 'Admin\LokasiPresensi::edit/$1');
    $routes->post('lokasipresensi/update/(:segment)', 'Admin\LokasiPresensi::update/$1');
    $routes->get('lokasipresensi/delete/(:segment)', 'Admin\LokasiPresensi::delete/$1');
    $routes->get('lokasipresensi/detail/(:num)', 'Admin\LokasiPresensi::detail/$1');

    // Data Karyawan
    $routes->get('karyawan', 'Admin\DataKaryawan::index');
    $routes->get('karyawan/create', 'Admin\DataKaryawan::create');
    $routes->post('karyawan/store', 'Admin\DataKaryawan::store');
    $routes->get('karyawan/detail/(:segment)', 'Admin\DataKaryawan::detail/$1');
    $routes->get('karyawan/edit/(:segment)', 'Admin\DataKaryawan::edit/$1');
    $routes->post('karyawan/update', 'Admin\DataKaryawan::update');
    $routes->get('karyawan/delete/(:segment)', 'Admin\DataKaryawan::delete/$1');

    // REKAP PRESENSI
    $routes->get('rekap-harian', 'Admin\RekapPresensiController::index');
    $routes->get('rekap-harian/(:segment)', 'Admin\RekapPresensiController::index/$1'); 
    $routes->get('rekap-bulanan', 'Admin\RekapPresensiController::bulanan');
    $routes->get('rekap-bulanan/(:any)', 'Admin\RekapPresensiController::bulanan/$1');
    $routes->get('export-harian', 'Admin\RekapPresensiController::exportHarian');
    $routes->get('export-harian/(:segment)', 'Admin\RekapPresensiController::exportHarian/$1'); 
    $routes->get('export-bulanan', 'Admin\RekapPresensiController::exportBulanan');
    $routes->get('export-bulanan/(:any)', 'Admin\RekapPresensiController::exportBulanan/$1');
    $routes->get('rekap-presensi/sinkronisasi-alpa/(:any)', 'Admin\RekapPresensiController::sinkronisasiAlpa/$1');
    $routes->get('rekap-mingguan', 'Admin\RekapPresensiController::mingguan');
    $routes->get('rekap-mingguan/export', 'Admin\RekapPresensiController::exportMingguan');

    // Ketidakhadiran
    $routes->get('ketidakhadiran', 'Admin\Ketidakhadiran::index');
    $routes->get('ketidakhadiran/updatestatus/(:num)/(:segment)', 'Admin\Ketidakhadiran::updateStatus/$1/$2');
    $routes->get('ketidakhadiran/delete/(:num)', 'Admin\Ketidakhadiran::delete/$1');

    // 💰 PINJAMAN & PERSETUJUAN (Admin) - CONTROLLER: Admin\Pinjaman
    $routes->group('pinjaman', function ($routes) {
        $routes->get('/', 'Admin\Pinjaman::index'); // Daftar Permohonan Pending
        $routes->post('approve/(:num)', 'Admin\Pinjaman::approve/$1'); // Setuju & Catat DEBT (POST diperlukan)
        $routes->post('reject/(:num)', 'Admin\Pinjaman::reject/$1');   // Tolak Permohonan (POST diperlukan)
    });

    // 💸 ANGSURAN & PEMBAYARAN (Admin) - CONTROLLER: Admin\Angsuran (BARU)
    $routes->group('angsuran', function ($routes) {
        $routes->get('/', 'Admin\Angsuran::index'); // Daftar Pinjaman Aktif (search/bulanan)
        $routes->get('detail/(:num)', 'Admin\Angsuran::detail/$1'); // Detail Riwayat Angsuran
        $routes->post('manual', 'Admin\Angsuran::storeManualPayment'); // Simpan Pembayaran Manual
    });

    $routes->group('potongan', function ($routes) {
        $routes->get('/', 'Admin\PotonganGaji::index'); 
        $routes->get('create', 'Admin\PotonganGaji::create');
        $routes->post('store', 'Admin\PotonganGaji::store');
        
        // RUTE BARU UNTUK EDIT DAN UPDATE
        $routes->get('edit/(:num)', 'Admin\PotonganGaji::edit/$1');
        $routes->post('update/(:num)', 'Admin\PotonganGaji::update/$1');
        
        // RUTE BARU UNTUK DELETE
        $routes->get('delete/(:num)', 'Admin\PotonganGaji::delete/$1');
    });
    $routes->group('lembur', function ($routes) {
        $routes->get('/', 'Admin\UangLembur::index'); 
        $routes->get('create', 'Admin\UangLembur::create');
        $routes->post('store', 'Admin\UangLembur::store');
        $routes->get('edit/(:num)', 'Admin\UangLembur::edit/$1');
        $routes->get('delete/(:num)', 'Admin\UangLembur::delete/$1');
    });

    $routes->group('gaji', function ($routes) {
        // Tampilkan Form Pilih Periode (CREATE)
        $routes->get('/', 'Admin\GajiController::index'); 
        // Proses Perhitungan Gaji (CREATE)
        $routes->post('proses', 'Admin\GajiController::prosesHitung');
        
        // Tampilkan Rekap Gaji (READ & FILTER)
        $routes->get('rekap', 'Admin\GajiController::rekap');
        
        // Hapus Record Gaji (DELETE / Re-run Trigger)
        $routes->get('delete/(:num)', 'Admin\GajiController::delete/$1'); 
        
        // Export Slip PDF
        $routes->get('export/(:num)/(:segment)', 'Admin\GajiController::exportSlipPdf/$1/$2'); 
    });

    $routes->get('ubah-password', 'UbahPassword::index');
    $routes->post('ubah-password/update/(:num)', 'UbahPassword::update/$1');
});


// =========================================================================
// RUTE PEGAWAI (Menggunakan Grouping untuk Filter)
// =========================================================================
$routes->group('pegawai', ['filter' => 'pegawaiFilter'], function($routes) {
    
    // Dashboard
    $routes->get('dashboard', 'Pegawai\Dashboard::index');

    // Absensi
    $routes->get('absen', 'Pegawai\Absen::index');
    $routes->post('absen/presensi_masuk', 'Pegawai\Absen::presensi_masuk');
    $routes->post('absen/simpan_foto', 'Pegawai\Absen::simpan_foto'); 
    $routes->post('absen/presensi_keluar', 'Pegawai\Absen::presensi_keluar');
    $routes->post('absen/simpan_foto_keluar', 'Pegawai\Absen::simpan_foto_keluar'); 
    $routes->get('data_absen', 'Pegawai\Absen::dataAbsen');

    // Ketidakhadiran
    $routes->get('ketidakhadiran', 'Pegawai\Ketidakhadiran::index');
    $routes->get('ketidakhadiran/create', 'Pegawai\Ketidakhadiran::create');
    $routes->post('ketidakhadiran/store', 'Pegawai\Ketidakhadiran::store');
    $routes->get('ketidakhadiran/edit/(:num)', 'Pegawai\Ketidakhadiran::edit/$1');
    $routes->post('ketidakhadiran/update/(:num)', 'Pegawai\Ketidakhadiran::update/$1');
    $routes->get('ketidakhadiran/delete/(:num)', 'Pegawai\Ketidakhadiran::delete/$1');
    $routes->get('ketidakhadiran/export', 'Pegawai\Ketidakhadiran::export');

    // 💸 PINJAMAN & PENGAJUAN (Pegawai) - CONTROLLER: Pegawai\Pinjaman
    $routes->group('pinjaman', function ($routes) {
        $routes->get('ajukan', 'Pegawai\Pinjaman::ajukan');      
        $routes->post('saveAjuan', 'Pegawai\Pinjaman::saveAjuan'); 
        $routes->get('status', 'Pegawai\Pinjaman::status');      // Status Pengajuan, dan Saldo LUNAS/AKTIF
    });
    
    $routes->group('gaji', function($routes) {
    // List riwayat gaji
        $routes->get('/', 'Pegawai\Gaji::index'); 
        // Detail slip gaji
        $routes->get('detail/(:num)', 'Pegawai\Gaji::detail/$1'); 
        // Download PDF
        $routes->get('download/(:num)', 'Pegawai\Gaji::downloadPdf/$1'); 
    });

    $routes->get('ubah-password', 'UbahPassword::index');
    $routes->post('ubah-password/update/(:num)', 'UbahPassword::update/$1');
});
