<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KetidakhadiranModel;

class Ketidakhadiran extends BaseController
{
    /**
     * Menampilkan daftar semua pengajuan ketidakhadiran dengan filter pencarian.
     */
    public function index()
    {
        $ketidakhadiranModel = new KetidakhadiranModel();
        
        // Ambil input pencarian dari query string
        $keyword = $this->request->getGet('q'); 

        // Panggil method Model untuk mendapatkan data (tanpa parameter null untuk mengambil semua)
        $list_izin = $ketidakhadiranModel->getIzinWithKaryawan(null, $keyword); 
        
        $data = [
            'title' => 'Manajemen Pengajuan Ketidakhadiran',
            'list_izin' => $list_izin,
            'keyword' => $keyword // Kirim keyword kembali ke View untuk mempertahankan nilai filter
        ];

        // Asumsi struktur view Admin Anda
        return view('layout_admin/head', $data)
            . view('layout_admin/sidebar')
            . view('pages_admin/ketidakhadiran/index', $data) 
            . view('layout_admin/footer');
    }

    /**
     * Aksi: Mengubah status menjadi Approved atau Rejected.
     */
    public function updateStatus($id, $status)
    {
        $ketidakhadiranModel = new KetidakhadiranModel();
        
        if ($status === 'approved' || $status === 'rejected') {
            $ketidakhadiranModel->update($id, ['status_pengajuan' => ucfirst($status)]);
            return redirect()->back()->with('success', 'Status pengajuan berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    /**
     * Aksi: Menghapus pengajuan (opsional, hanya jika Pending).
     */
    public function delete($id)
    {
        $ketidakhadiranModel = new KetidakhadiranModel();
        $izin = $ketidakhadiranModel->find($id);

        if ($izin['status_pengajuan'] === 'Pending') {
            @unlink(ROOTPATH . 'public/files/izin/' . $izin['file']);
            $ketidakhadiranModel->delete($id);
            return redirect()->back()->with('success', 'Pengajuan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak dapat dihapus.');
    }
}