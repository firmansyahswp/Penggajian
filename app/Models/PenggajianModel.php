<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggajianModel extends Model
{
    protected $table            = 'penggajian'; // Menggunakan tabel penggajian
    protected $primaryKey       = 'no_penggajian';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'periode_pph21', 'tanggal', 'gaji_pokok', 'tunj_transport', 'tunj_makan', 
        'total_lembur', 'total_bonus', 'total_pinjaman', 'bruto', 
        'biaya_jabatan', 'netto_sebulan', 'netto_setahun', 'total_ptkp', 
        'pkp', 'pph21_setahun', 'pph21_sebulan', 'kd_karyawan', 
        
        // FIELD TAMBAHAN UNTUK SLIP GAJI
        'potongan_bpjs_tk', 'potongan_bpjs_kes', 'total_potongan_lain', 'gaji_bersih', 'potongan_alpa', 
        'jumlah_alpa','potongan_terlambat', 'jumlah_terlambat'
    ]; 

    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
    
    /**
     * Ambil data rekap gaji dengan detail karyawan (diperlukan untuk Slip Gaji).
     */
    public function getRekapWithKaryawan($periode = null)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, k.nm_karyawan, k.nip');
        $builder->join('karyawan k', 'k.id_karyawan = p.kd_karyawan');
        
        if ($periode) {
            $builder->where('p.periode_pph21', $periode);
        }
        
        
        $builder->orderBy('p.periode_pph21', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getGajiByKaryawanId(int $kd_karyawan)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('
            p.*, 
            k.nm_karyawan, k.nip, 
            j.nm_jabatan'
        );
        $builder->join('karyawan k', 'k.id_karyawan = p.kd_karyawan');
        // Join ke jabatan untuk ditampilkan di detail
        $builder->join('jabatan j', 'j.kd_jabatan = k.kd_jabatan', 'left'); 

        $builder->where('p.kd_karyawan', $kd_karyawan);
        $builder->orderBy('p.periode_pph21', 'DESC');
        return $builder->get()->getResultArray();
    }
}