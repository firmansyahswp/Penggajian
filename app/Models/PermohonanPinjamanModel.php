<?php

namespace App\Models;

use CodeIgniter\Model;

class PermohonanPinjamanModel extends Model
{
    protected $table            = 'permohonan_pinjaman';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // Pastikan 'status_pengajuan' sudah ada di sini
    protected $allowedFields    = ['kd_user', 'kd_karyawan', 'besar_pinjaman', 'keterangan', 'timedate', 'status_pengajuan']; //

    // Dates
    protected $useTimestamps    = false; 
    protected $dateFormat       = 'datetime';

    /**
     * Mengambil data permohonan dengan detail Karyawan.
     * @param string|null $status Filter status: PENDING, DISETUJUI, DITOLAK, atau 'ALL'.
     */
    public function getPermohonanWithKaryawan($status = null)
    {
        $builder = $this->db->table($this->table);
        // Select Permohonan data and Karyawan details (nm_karyawan, nip)
        $builder->select('permohonan_pinjaman.*, karyawan.nm_karyawan, karyawan.nip');
        
        // JOIN ke tabel karyawan
        $builder->join('karyawan', 'karyawan.id_karyawan = permohonan_pinjaman.kd_karyawan');
        
        // Filter status
        if ($status !== null && $status !== 'ALL') {
            // Menggunakan status 'PENDING' (huruf kapital) yang sesuai dengan input Pegawai
            $builder->where('permohonan_pinjaman.status_pengajuan', $status);
        }
        
        $builder->orderBy('permohonan_pinjaman.timedate', 'DESC');
        return $builder->get()->getResultArray();
    }
}