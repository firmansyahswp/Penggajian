<?php namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use App\Models\KetidakhadiranModel;
use App\Models\UserModel; // Digunakan untuk mencari ID Karyawan
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 

class Ketidakhadiran extends BaseController
{
    /**
     * Fungsi untuk mendapatkan ID Karyawan yang sedang login dari sesi.
     * @return int|null ID Karyawan (id_karyawan) jika ditemukan.
     */
    private function getKaryawanId()
    {
        // PRIORITAS 1: Coba ambil ID Karyawan langsung (id_karyawan atau user_id dari sesi)
        // Kunci 'user_id' sudah terbukti menyimpan ID Karyawan dari LoginController
        $karyawanId = session()->get('id_karyawan') ?? session()->get('user_id'); 
        
        if ($karyawanId) {
            return $karyawanId;
        }

        // PRIORITAS 2: Fallback ke logika mencari ID Karyawan melalui PK user
        // Logika ini hanya diperlukan jika 'user_id' di sesi menyimpan PK User, bukan ID Karyawan.
        $userId = session()->get('kd_user') ?? session()->get('id'); 
        
        if ($userId) {
            $userModel = new UserModel(); 
            $user = $userModel->select('id_karyawan')->where('kd_user', $userId)->first(); 
            return $user['id_karyawan'] ?? null;
        }
        
        return null;
    }

    /**
     * Menampilkan riwayat pengajuan ketidakhadiran pegawai (Halaman Utama).
     */
    public function index()
    {
        $ketidakhadiranModel = new KetidakhadiranModel();
        
        $karyawanId = $this->getKaryawanId();
        
        // Memastikan ID Karyawan tersedia untuk Query
        $list_izin = $karyawanId ? $ketidakhadiranModel->getIzinWithKaryawan($karyawanId) : [];
        
        $data = [
            'title' => 'Pengajuan Ketidakhadiran',
            'list_izin' => $list_izin
        ];

        return view('pegawai/head', $data)
            . view('pegawai/sidebar')
            . view('pages_pegawai/ketidakhadiran/index', $data) 
            . view('pegawai/footer');
    }

    /**
     * Menampilkan halaman FORM AJUKAN KETIDAKHADIRAN BARU.
     */
    public function create()
    {
        $data = [
            'title' => 'Ajukan Ketidakhadiran Baru',
            'izin' => null
        ];

        return view('pegawai/head', $data)
            . view('pegawai/sidebar')
            . view('pages_pegawai/ketidakhadiran/create', $data)
            . view('pegawai/footer');
    }

    /**
     * Memproses penyimpanan pengajuan baru.
     */
    public function store()
    {
        $karyawanId = $this->getKaryawanId();

        if (!$karyawanId) {
             return redirect()->to('pegawai/ketidakhadiran')->with('error', 'Akses ditolak: Data karyawan tidak terdeteksi di sesi.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal' => 'required',
            'keterangan' => 'required',
            'deskripsi' => 'required',
            'file_bukti' => 'uploaded[file_bukti]|max_size[file_bukti,2048]|ext_in[file_bukti,pdf,jpg,jpeg,png]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->to('pegawai/ketidakhadiran/create')->withInput()->with('errors', $validation->getErrors());
        }

        $file_bukti = $this->request->getFile('file_bukti');
        $nama_file = $file_bukti->getRandomName();
        $uploadPath = ROOTPATH . 'public/files/izin';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($file_bukti->isValid() && !$file_bukti->hasMoved()) {
            $file_bukti->move($uploadPath, $nama_file);
        }

        $ketidakhadiranModel = new KetidakhadiranModel();
        
        try {
            $ketidakhadiranModel->save([
                // Kolom di DB Ketidakhadiran adalah 'kd_karyawan'
                'kd_karyawan' => $karyawanId, 
                'tanggal' => $this->request->getPost('tanggal'),
                'keterangan' => $this->request->getPost('keterangan'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'file' => $nama_file,
                'status_pengajuan' => 'Pending', 
            ]);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('pegawai/ketidakhadiran')->with('error', 'Gagal menyimpan data ke database: ' . $e->getMessage());
        }


        return redirect()->to('pegawai/ketidakhadiran')->with('success', 'Pengajuan berhasil dikirim.');
    }

    /**
     * Menampilkan halaman FORM EDIT.
     */
    public function edit($id)
    {
        $ketidakhadiranModel = new KetidakhadiranModel();
        $izin = $ketidakhadiranModel->find($id);
        $karyawanId = $this->getKaryawanId();

        // Menggunakan kolom DB yang benar (kd_karyawan)
        if (!$izin || $izin['kd_karyawan'] !== $karyawanId || $izin['status_pengajuan'] !== 'Pending') {
            return redirect()->to('pegawai/ketidakhadiran')->with('error', 'Pengajuan tidak dapat diedit atau sudah diproses.');
        }

        $data = [
            'title' => 'Edit Pengajuan Ketidakhadiran',
            'izin' => $izin
        ];

        return view('pegawai/head', $data)
            . view('pegawai/sidebar')
            . view('pages_pegawai/ketidakhadiran/edit', $data) 
            . view('pegawai/footer');
    }
    
    /**
     * Memproses pembaruan data pengajuan.
     */
    public function update($id)
    {
        $ketidakhadiranModel = new KetidakhadiranModel();
        $izin = $ketidakhadiranModel->find($id);
        $karyawanId = $this->getKaryawanId();

        if (!$izin || $izin['kd_karyawan'] !== $karyawanId || $izin['status_pengajuan'] !== 'Pending') {
            return redirect()->to('pegawai/ketidakhadiran')->with('error', 'Pengajuan tidak dapat diubah.');
        }

        $rules = [
            'tanggal' => 'required',
            'keterangan' => 'required',
            'deskripsi' => 'required',
            'file_bukti' => 'permit_empty|max_size[file_bukti,2048]|ext_in[file_bukti,pdf,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama_file = $izin['file'];
        $file_bukti = $this->request->getFile('file_bukti');

        if ($file_bukti && $file_bukti->isValid() && !$file_bukti->hasMoved()) {
            if ($izin['file']) {
                @unlink(ROOTPATH . 'public/files/izin/' . $izin['file']);
            }
            $nama_file = $file_bukti->getRandomName();
            $file_bukti->move(ROOTPATH . 'public/files/izin', $nama_file);
        }

        $ketidakhadiranModel->update($id, [
            'kd_karyawan' => $karyawanId, 
            'tanggal' => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file' => $nama_file,
        ]);

        return redirect()->to('pegawai/ketidakhadiran')->with('success', 'Pengajuan berhasil diperbarui.');
    }
    
    /**
     * Menghapus pengajuan.
     */
    public function delete($id)
    {
        $ketidakhadiranModel = new KetidakhadiranModel();
        $izin = $ketidakhadiranModel->find($id);
        $karyawanId = $this->getKaryawanId();

        if (!$izin || $izin['kd_karyawan'] !== $karyawanId || $izin['status_pengajuan'] !== 'Pending') {
            return redirect()->to('pegawai/ketidakhadiran')->with('error', 'Pengajuan tidak dapat dihapus atau sudah diproses.');
        }

        if ($izin['file']) {
            @unlink(ROOTPATH . 'public/files/izin/' . $izin['file']);
        }
        
        $ketidakhadiranModel->delete($id);
        
        return redirect()->to('pegawai/ketidakhadiran')->with('success', 'Pengajuan berhasil dihapus.');
    }

    // Export method dihilangkan untuk fokus pada fungsi CRUD
    
}