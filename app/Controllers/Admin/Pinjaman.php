<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PermohonanPinjamanModel;
use App\Models\AngsuranPinjamanModel; 
use CodeIgniter\Database\Exceptions\DatabaseException;

class Pinjaman extends BaseController
{
    protected $permohonanModel;
    protected $angsuranModel; 
    protected $timezone = 'Asia/Jakarta';

    public function __construct()
    {
        \date_default_timezone_set($this->timezone);
        $this->permohonanModel = new PermohonanPinjamanModel(); //
        $this->angsuranModel = new AngsuranPinjamanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Permohonan Pinjaman (Admin)',
            'list_permohonan' => $this->permohonanModel->getPermohonanWithKaryawan('PENDING') 
        ];
        
        return view('layout_admin/head', $data)
             . view('layout_admin/sidebar')
             . view('pages_admin/pinjaman/list_permohonan', $data) 
             . view('layout_admin/footer');
    }
    
    // =======================================================
    // 2. Setujui Pinjaman (POST /admin/pinjaman/approve/{id})
    // =======================================================
    public function approve($id)
    {
        $permohonan = $this->permohonanModel->find($id);
        
        if (empty($permohonan) || $permohonan['status_pengajuan'] !== 'PENDING') {
            return redirect()->to(base_url('admin/pinjaman'))->with('error', 'Permohonan tidak ditemukan atau sudah diproses.');
        }

        try {
            // 1. Catat TOTAL DEBT (Hutang) sebagai angsuran pertama (Nilai NEGATIF)
            $this->angsuranModel->save([
                'id_permohonan'    => $id, 
                'kd_karyawan'      => $permohonan['kd_karyawan'],
                'tanggal_bayar'    => date('Y-m-d H:i:s'), 
                // KRITIS: Simpan jumlah pinjaman sebagai nilai NEGATIF (DEBT)
                'besar_angsuran'   => (float) $permohonan['besar_pinjaman'] * -1, 
                'tipe_pembayaran'  => 'DEBT', 
                'keterangan'       => 'Pencairan Pinjaman (Hutang Awal Permohonan ID ' . $id . ')',
            ]);
            
            // 2. Update status permohonan
            $this->permohonanModel->update($id, [
                'status_pengajuan' => 'DISETUJUI'
            ]);
            
        } catch (DatabaseException $e) {
             return redirect()->to(base_url('admin/pinjaman'))->with('error', 'Gagal mencatat hutang: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/pinjaman'))->with('success', 'Pinjaman disetujui & hutang dicatat di ledger angsuran.');
    }
    
    public function reject($id)
    {
        $permohonan = $this->permohonanModel->find($id);
        
        if (empty($permohonan) || $permohonan['status_pengajuan'] !== 'PENDING') {
            return redirect()->to(base_url('admin/pinjaman'))->with('error', 'Permohonan tidak ditemukan atau sudah diproses.');
        }
        
        try {
            // Update status permohonan menjadi DITOLAK
            $this->permohonanModel->update($id, [
                'status_pengajuan' => 'DITOLAK'
            ]);
        } catch (DatabaseException $e) {
             return redirect()->to(base_url('admin/pinjaman'))->with('error', 'Gagal menolak permohonan: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/pinjaman'))->with('success', 'Permohonan pinjaman berhasil ditolak.');
    }
}