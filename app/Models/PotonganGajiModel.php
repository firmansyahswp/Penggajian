<?php

namespace App\Models;

use CodeIgniter\Model;

class PotonganGajiModel extends Model
{
    protected $table            = 'potongan_gaji';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'kd_karyawan', 'jenis_potongan', 'besar_potongan', 
        'periode_gaji', 'keterangan', 'timedate'
    ];

    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
    
    /**
     * Mengambil semua potongan untuk periode tertentu, digabung dengan data Karyawan.
     */
    public function getPotonganByPeriode($periode = null)
    {
        $builder = $this->db->table($this->table . ' pg');
        $builder->select('pg.*, k.nm_karyawan, k.nip');
        $builder->join('karyawan k', 'k.id_karyawan = pg.kd_karyawan');
        
        if ($periode) {
            $builder->where('pg.periode_gaji', $periode);
        }
        
        $builder->orderBy('pg.periode_gaji', 'DESC');
        return $builder->get()->getResultArray();
    }
    
    /**
     * Mengambil total potongan per karyawan untuk satu periode gaji.
     */
    public function getTotalPotonganByKaryawan($kd_karyawan, $periode_gaji)
    {
        $total = $this->selectSum('besar_potongan', 'total_potongan')
                      ->where('kd_karyawan', $kd_karyawan)
                      ->where('periode_gaji', $periode_gaji)
                      ->first();
                      
        return (float) ($total['total_potongan'] ?? 0.0);
    }
}