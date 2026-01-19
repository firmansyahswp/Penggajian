<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use App\Models\LokasiPresensiModel;
use App\Models\KaryawanModel;
use App\Models\PresensiModel;
use CodeIgniter\HTTP\ResponseInterface;

class Absen extends BaseController
{
    // Pastikan properti dan model terdefinisi
    protected $timezone = 'Asia/Jakarta'; 
    protected $lokasiPresensiModel; 
    protected $karyawanModel;       
    protected $presensiModel;       

    public function __construct()
    {
        \date_default_timezone_set($this->timezone);
        
        // INISIALISASI MODEL
        $this->lokasiPresensiModel = new LokasiPresensiModel();
        $this->karyawanModel = new KaryawanModel();
        $this->presensiModel = new PresensiModel(); 
    }

    // --- METHOD INDEX (Absen Masuk) ---
    public function index()
    {
        $id_karyawan = session()->get('user_id'); 
        $karyawan = $this->karyawanModel->where('id_karyawan', $id_karyawan)->first();

        $hari_ini = date('N'); 
        $is_hari_libur = ($hari_ini == 7); 
        $data['is_hari_libur'] = $is_hari_libur;
        $data['lokasi_presensi'] = null;
        $data['karyawan'] = $karyawan;
        $data['jam_masuk_kantor'] = 'N/A';
        $data['jam_pulang_kantor'] = 'N/A';
        $data['status_waktu_masuk'] = false;
        $data['status_waktu_pulang'] = false;
        
        if ($karyawan) {
            $id_lokasi = $karyawan['id_lokasi_default'] ?? null;
            if ($id_lokasi) {
                $lokasi = $this->lokasiPresensiModel->where('id', $id_lokasi)->first();
                $data['lokasi_presensi'] = $lokasi;
                if ($lokasi) {
                    $zone = $this->getTimezoneFromWaktu($lokasi['zona_waktu']);
                    \date_default_timezone_set($zone);
                    
                    $data['jam_masuk_kantor'] = $lokasi['jam_masuk']; 
                    $data['jam_pulang_kantor'] = $lokasi['jam_pulang'];
                    
                    // checkTimeConstraint() juga ditambahkan di bawah
                    $data['status_waktu_masuk'] = $this->checkTimeConstraint($lokasi['jam_masuk'], 'start');
                    $data['status_waktu_pulang'] = $this->checkTimeConstraint($lokasi['jam_pulang'], 'end');
                }
            }
        } else {
            session()->setFlashdata('pesan', 'Sesi tidak valid.');
            return redirect()->to(\base_url('/login')); 
        }

        // Cari data presensi hari ini
        $data_presensi_hari_ini = $this->presensiModel 
                                        ->where('kd_karyawan', $id_karyawan)
                                        ->where('tanggal_masuk', \date('Y-m-d')) 
                                        ->first();

        $data['title'] = 'Absensi';
        $data['status_presensi'] = $data_presensi_hari_ini;

        return view('pegawai/head', $data)
            . view('pegawai/sidebar')
            . view('pages_pegawai/absen', $data) 
            . view('pegawai/footer'); 
    }
    
    // =======================================================
    // dataAbsen (Riwayat Absensi - Target FIX 404)
    // =======================================================
    public function dataAbsen()
    {
        $kd_karyawan = session()->get('user_id'); 
        if (!$kd_karyawan) {
            return redirect()->to(base_url('logout'));
        }

        $periode = $this->request->getGet('periode') ?? date('Y-m');
        
        // Asumsi method getRiwayatAbsenByKaryawan ada di PresensiModel
        $riwayat_absen = $this->presensiModel->getRiwayatAbsenByKaryawan($kd_karyawan, $periode);

        $data = [
            'title' => 'Riwayat Data Absensi Saya',
            'riwayat_absen' => $riwayat_absen,
            'selectedPeriode' => $periode,
            'listPeriode' => $this->getListPeriode()
        ];
        
        return view('pegawai/head', $data)
             . view('pegawai/sidebar')
             . view('pages_pegawai/absen/riwayat_absen', $data) 
             . view('pegawai/footer');
    }

    // --- METHOD PRESENSI MASUK ---
    public function presensi_masuk()
    {
        // 1. Ambil data dari POST
        $id_karyawan = $this->request->getPost('id_karyawan');
        $id_lokasi = $this->request->getPost('id_lokasi_default');
        $latitude_pegawai = (float) $this->request->getPost('latitude_pegawai');
        $longitude_pegawai = (float) $this->request->getPost('longitude_pegawai');
        $latitude_kantor = (float) $this->request->getPost('latitude_kantor');
        $longitude_kantor = (float) $this->request->getPost('longitude_kantor');
        $radius = (float) $this->request->getPost('radius');
        $accuracy = (float) $this->request->getPost('accuracy_pegawai'); 
        $user_ip = $this->request->getIPAddress();

        $lokasi = $this->lokasiPresensiModel->find($id_lokasi);
        if ($lokasi) {
            $zone = $this->getTimezoneFromWaktu($lokasi['zona_waktu']);
            \date_default_timezone_set($zone);

            $jam_sekarang = strtotime(date('H:i:s'));
            $jam_masuk_kantor = strtotime($lokasi['jam_masuk']);
            $batas_awal = $jam_masuk_kantor - (30 * 60);
            $batas_maksimal = $jam_masuk_kantor + (30 * 60); // 30 Menit

            if ($jam_sekarang < $batas_awal) {
                session()->setFlashdata('gagal', 'Gagal! Presensi masuk terlalu dini. Baru bisa dimulai pada ' . date('H:i', $batas_awal));
                return redirect()->to(base_url('pegawai/absen'));
            }

            if ($jam_sekarang > $batas_maksimal) {
                session()->setFlashdata('gagal', 'Gagal! Waktu absen masuk sudah berakhir (Maksimal 30 menit keterlambatan).');
                return redirect()->to(base_url('pegawai/absen'));
            }
        }

        // --- FITUR ANTI-FAKE GPS LAYER 1: VALIDASI AKURASI ---
        // Fake GPS sering memberikan akurasi 0 atau sangat kecil (misal 1.0m) secara konstan.
        // Kita beri toleransi minimal akurasi 1.5 - 2 meter untuk filter awal.
        if ($accuracy > 0 && $accuracy < 1.2) {
            session()->setFlashdata('gagal', 'Terdeteksi manipulasi lokasi (Fake GPS). Gunakan perangkat asli!');
            return redirect()->to(base_url('pegawai/absen'));
        }

        // --- FITUR ANTI-FAKE GPS LAYER 2: IP GEOLOCATION CHECK ---
        try {
            // Memanggil API eksternal untuk cek lokasi IP (Gratis max 45 request/min)
            $ctx = stream_context_create(['http' => ['timeout' => 3]]); // set timeout 3 detik
            $check_ip = @file_get_contents("http://ip-api.com/json/{$user_ip}?fields=status,lat,lon,proxy,mobile", false, $ctx);
            $geo = json_decode($check_ip);

            if ($geo && $geo->status === 'success') {
                // Cek apakah user menggunakan Proxy/VPN (Sering sepaket dengan Fake GPS)
                if ($geo->proxy == true) {
                    session()->setFlashdata('gagal', 'Presensi ditolak karena terdeteksi menggunakan VPN/Proxy.');
                    return redirect()->to(base_url('pegawai/absen'));
                }

                // Cek jarak antara titik koordinat GPS vs lokasi IP
                // Jika jaraknya > 100km, indikasi kuat lokasi GPS "ditembak" paksa
                $jarak_ke_ip = $this->hitung_jarak_manual($latitude_pegawai, $longitude_pegawai, $geo->lat, $geo->lon);
                if ($jarak_ke_ip > 100000) { // Toleransi 100 KM
                    session()->setFlashdata('gagal', 'Lokasi GPS tidak sinkron dengan lokasi jaringan internet Anda.');
                    return redirect()->to(base_url('pegawai/absen'));
                }
            }
        } catch (\Exception $e) {
            // Jika API Limit atau Offline, biarkan proses berlanjut ke validasi radius kantor
        }

        // --- VALIDASI GEOLOCATION DASAR ---
        if ($latitude_pegawai == 0 || $longitude_pegawai == 0) {
            session()->setFlashdata('gagal', 'Gagal mendapatkan lokasi GPS. Pastikan GPS aktif.');
            return redirect()->to(base_url('pegawai/absen'));
        }

        // --- PERHITUNGAN RADIUS KE KANTOR (HAVERSINE) ---
        $earth_radius = 6371000; 
        $dLat = deg2rad($latitude_kantor - $latitude_pegawai);
        $dLon = deg2rad($longitude_kantor - $longitude_pegawai);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($latitude_pegawai)) * cos(deg2rad($latitude_kantor)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $meter = floor($earth_radius * $c);

        // --- CEK APAKAH DALAM RADIUS ---
        if ($meter > $radius) {
            session()->setFlashdata('gagal', 'Anda berada di luar radius kantor (' . $meter . ' meter).');
            return redirect()->to(base_url('pegawai/absen'));
        } 

        // --- LANJUT KE VIEW AMBIL FOTO ---
        $karyawan = $this->karyawanModel->find($id_karyawan);
        $data = [
            'title' => 'Ambil Foto Selfie Masuk',
            'karyawan' => $karyawan,
            'id_karyawan' => $id_karyawan,
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'jam_masuk' => $this->request->getPost('jam_masuk'),
            'latitude_pegawai' => $latitude_pegawai,
            'longitude_pegawai' => $longitude_pegawai,
            'meter' => $meter,
            'id_lokasi' => $this->request->getPost('id_lokasi_default')
        ];

        return view('pegawai/head', $data)
            . view('pegawai/sidebar')
            . view('pages_pegawai/absen/ambil_foto', $data)
            . view('pegawai/footer');
    }

    // --- METHOD SIMPAN FOTO MASUK ---
    public function simpan_foto()
    {
        
        $rules = [
            'id_karyawan' => 'required',
            'tanggal_masuk' => 'required|valid_date',
            'jam_masuk' => 'required',
            'image' => 'required', 
            'id_lokasi' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('gagal', 'Gagal memproses data absensi. Data tidak lengkap atau tidak valid.');
            return redirect()->back();
        }

        $kd_karyawan = $this->request->getPost('id_karyawan'); 
        $id_lokasi_presensi = $this->request->getPost('id_lokasi'); 
        $jam_masuk_absen = $this->request->getPost('jam_masuk'); 
        $lokasi = $this->lokasiPresensiModel->find($id_lokasi_presensi);
        $is_terlambat_denda = 0;

        if ($lokasi) {
            $jam_masuk_kantor = strtotime($lokasi['jam_masuk']);
            $jam_absen = strtotime($jam_masuk_absen);
            
            // Hitung selisih dalam menit
            $selisih_menit = ($jam_absen - $jam_masuk_kantor) / 60;

            // Jika terlambat lebih dari 10 menit, tandai untuk denda 75rb
            if ($selisih_menit > 10) {
                $is_terlambat_denda = 1;
            }
        }

        $base64_image = $this->request->getPost('image');

        $base64_image = \preg_replace('/^data:image\/\w+;base64,/', '', $base64_image);
        $data_biner = \base64_decode($base64_image);
        
        $folder_path = FCPATH . 'uploads/absen/';
        if (!\is_dir($folder_path)) {
            \mkdir($folder_path, 0777, true); 
        }

        \date_default_timezone_set($this->timezone); 
        $file_name = $kd_karyawan . '_' . \date('YmdHis') . '.jpeg';
        $full_file_path = $folder_path . $file_name; 
        $db_file_path = 'uploads/absen/' . $file_name; 

        if (!\file_put_contents($full_file_path, $data_biner)) { 
            session()->setFlashdata('gagal', 'Presensi gagal. Gagal menyimpan file foto ke server.');
            return redirect()->back();
        }

        $data_presensi = [
            'kd_karyawan' => $kd_karyawan,
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'jam_masuk' => $jam_masuk_absen,
            'foto_masuk' => $db_file_path,
            'id_lokasi' => $id_lokasi_presensi,
            'is_terlambat_denda' => $is_terlambat_denda, // Simpan status denda
        ];

        if ($this->presensiModel->insert($data_presensi)) { 
            session()->setFlashdata('sukses', 'Presensi Masuk Berhasil dicatat!');
            return redirect()->to(\base_url('pegawai/absen'));
        } else {
            \unlink($full_file_path); 
            session()->setFlashdata('gagal', 'Presensi gagal! Data tidak dapat disimpan ke database.');
            return redirect()->back();
        }
    }

    private function hitung_jarak_manual($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344) * 1000; // Hasil dalam meter
    }

    // --- METHOD PRESENSI KELUAR ---
    public function presensi_keluar()
    {
        // 1. Ambil data dari POST
        $id_karyawan = $this->request->getPost('id_karyawan');
        $latitude_pegawai = (float) $this->request->getPost('latitude_pegawai');
        $longitude_pegawai = (float) $this->request->getPost('longitude_pegawai');
        $latitude_kantor = (float) $this->request->getPost('latitude_kantor');
        $longitude_kantor = (float) $this->request->getPost('longitude_kantor');
        $radius = (float) $this->request->getPost('radius');
        $accuracy = (float) $this->request->getPost('accuracy_pegawai'); // Ambil akurasi dari view
        $user_ip = $this->request->getIPAddress();

        // 2. Cari data presensi masuk hari ini
        $data_masuk = $this->presensiModel
                            ->where('kd_karyawan', $id_karyawan)
                            ->where('tanggal_masuk', date('Y-m-d'))
                            ->first();

        if (!$data_masuk) {
            session()->setFlashdata('gagal', 'Anda belum melakukan presensi masuk hari ini!');
            return redirect()->to(base_url('pegawai/absen'));
        }
        

        // --- ANTI-FAKE GPS LAYER 1: VALIDASI AKURASI ---
        // Emulator atau Fake GPS seringkali memberikan akurasi yang konstan/sangat kecil.
        if ($accuracy > 0 && $accuracy < 1.2) {
            session()->setFlashdata('gagal', 'Presensi keluar ditolak. Terdeteksi manipulasi lokasi (Fake GPS)!');
            return redirect()->to(base_url('pegawai/absen'));
        }

        // --- ANTI-FAKE GPS LAYER 2: IP GEOLOCATION CHECK ---
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 2]]);
            $check_ip = @file_get_contents("http://ip-api.com/json/{$user_ip}?fields=status,lat,lon,proxy", false, $ctx);
            $geo = json_decode($check_ip);

            if ($geo && $geo->status === 'success') {
                // Cek penggunaan VPN/Proxy
                if ($geo->proxy == true) {
                    session()->setFlashdata('gagal', 'Presensi keluar ditolak. Harap matikan VPN/Proxy Anda.');
                    return redirect()->to(base_url('pegawai/absen'));
                }

                // Cek Sinkronisasi Lokasi GPS vs Lokasi Jaringan (Toleransi 100km)
                $jarak_ke_ip = $this->hitung_jarak_manual($latitude_pegawai, $longitude_pegawai, $geo->lat, $geo->lon);
                if ($jarak_ke_ip > 100000) { 
                    session()->setFlashdata('gagal', 'Lokasi GPS Keluar tidak sesuai dengan lokasi jaringan internet Anda.');
                    return redirect()->to(base_url('pegawai/absen'));
                }
            }
        } catch (\Exception $e) {
            // Lanjutkan jika API error
        }

        // --- VALIDASI GEOLOCATION DASAR ---
        if ($latitude_pegawai == 0 || $longitude_pegawai == 0) {
            session()->setFlashdata('gagal', 'Gagal mendapatkan lokasi GPS Keluar.');
            return redirect()->to(base_url('pegawai/absen'));
        }

        // --- PERHITUNGAN RADIUS KE KANTOR (HAVERSINE) ---
        $earth_radius = 6371000; 
        $dLat = deg2rad($latitude_kantor - $latitude_pegawai);
        $dLon = deg2rad($longitude_kantor - $longitude_pegawai);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($latitude_pegawai)) * cos(deg2rad($latitude_kantor)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $meter = floor($earth_radius * $c);

        // --- CEK RADIUS ---
        if ($meter > $radius) {
            session()->setFlashdata('gagal', 'Gagal Keluar: Anda berada ' . $meter . ' meter dari kantor (Batas: ' . $radius . 'm).');
            return redirect()->to(base_url('pegawai/absen'));
        }

        // --- KIRIM DATA KE VIEW FOTO KELUAR ---
        $karyawan = $this->karyawanModel->find($id_karyawan);
        $data = [
            'title' => 'Ambil Foto Selfie Keluar',
            'karyawan' => $karyawan,
            'id_presensi' => $data_masuk['id'], 
            'kd_karyawan' => $data_masuk['kd_karyawan'], 
            'tanggal_keluar' => date('Y-m-d'),
            'jam_keluar' => date('H:i:s'),
            'latitude_pegawai' => $latitude_pegawai,
            'longitude_pegawai' => $longitude_pegawai,
            'meter' => $meter,
        ];

        return view('pegawai/head', $data)
            . view('pegawai/sidebar')
            . view('pages_pegawai/absen/ambil_foto_keluar', $data)
            . view('pegawai/footer');
    }

    // --- METHOD SIMPAN FOTO KELUAR ---
    public function simpan_foto_keluar()
    {
        
        $rules = [
            'id_presensi' => 'required|numeric',
            'kd_karyawan' => 'required',
            'tanggal_keluar' => 'required|valid_date',
            'jam_keluar' => 'required',
            'image' => 'required', 
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('gagal', 'Gagal memproses data absensi keluar. Data tidak lengkap atau tidak valid.');
            return redirect()->back();
        }

        $id_presensi = $this->request->getPost('id_presensi');
        $kd_karyawan = $this->request->getPost('kd_karyawan'); 
        $base64_image = $this->request->getPost('image');

        $base64_image = \preg_replace('/^data:image\/\w+;base64,/', '', $base64_image);
        $data_biner = \base64_decode($base64_image);
        
        $folder_path = FCPATH . 'uploads/absen/';
        if (!\is_dir($folder_path)) {
            \mkdir($folder_path, 0777, true); 
        }

        \date_default_timezone_set($this->timezone); 
        $file_name = $kd_karyawan . '_keluar_' . \date('YmdHis') . '.jpeg';
        $full_file_path = $folder_path . $file_name; 
        $db_file_path = 'uploads/absen/' . $file_name; 

        if (!\file_put_contents($full_file_path, $data_biner)) { 
            session()->setFlashdata('gagal', 'Presensi keluar gagal. Gagal menyimpan file foto ke server.');
            return redirect()->back();
        }

        $data_update = [
            'tanggal_keluar' => $this->request->getPost('tanggal_keluar'),
            'jam_keluar' => $this->request->getPost('jam_keluar'),
            'foto_keluar' => $db_file_path,
        ];
        
        if ($this->presensiModel->update($id_presensi, $data_update)) { 
            session()->setFlashdata('sukses', 'Presensi Keluar Berhasil dicatat!');
            return redirect()->to(\base_url('pegawai/absen'));
        } else {
            \unlink($full_file_path); 
            session()->setFlashdata('gagal', 'Presensi keluar gagal! Data tidak dapat diupdate di database.');
            return redirect()->back();
        }
    }
    
    // =======================================================
    // --- HELPER METHODS ---
    // =======================================================

    /**
     * @param string $zone_waktu Contoh: 'WIB'
     * @return string Zona waktu PHP yang valid. Contoh: 'Asia/Jakarta'
     */
    private function getTimezoneFromWaktu(string $zone_waktu): string
    {
        // Method ini diperlukan untuk menyelesaikan error "Call to undefined method"
        switch (strtoupper($zone_waktu)) {
            case 'WIB':
                return 'Asia/Jakarta';
            case 'WITA':
                return 'Asia/Makassar';
            case 'WIT':
                return 'Asia/Jayapura';
            default:
                return $this->timezone; // Default kembali ke Asia/Jakarta atau properti class
        }
    }

    /**
     * Memeriksa apakah waktu saat ini masih berada dalam batas waktu presensi.
     * @param string $jam_kantor Format HH:mm:ss (contoh: '08:00:00' atau '17:00:00')
     * @param string $type 'start' untuk jam masuk, 'end' untuk jam pulang
     * @return bool True jika masih dalam batas waktu, False jika sudah di luar.
     */
    private function checkTimeConstraint(string $jam_kantor, string $type): bool
    {
        // Method ini diperlukan karena dipanggil di method index()
        $now = strtotime(\date('H:i:s'));
        $target = strtotime($jam_kantor);
        
        if ($type === 'start') {
            // Untuk presensi masuk, biasanya ada batas waktu keterlambatan, 
            // misalnya 30 menit setelah jam kantor.
            $batas_akhir = $target + (30 * 60); // Contoh toleransi 30 menit
            return $now <= $batas_akhir;
        } elseif ($type === 'end') {
            // Untuk presensi pulang, biasanya diizinkan setelah jam pulang kantor.
            return $now >= $target;
        }

        return false;
    }


    /**
     * Helper untuk List Periode
     */
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