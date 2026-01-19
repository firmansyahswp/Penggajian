<?php

namespace App\Models;

use CodeIgniter\Model;

class UangLemburModel extends Model
{
    protected $table            = 'uang_lembur';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'kd_karyawan', 'besar_lembur', 'periode_gaji', 'keterangan', 'timedate'
    ];

    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
    
    /**
     * Mengambil total uang lembur per karyawan untuk satu periode gaji.
     y*/
    public function getTotalLemburByKaryawan($kd_karyawan, $periode_gaji)
    {
        $total = $this->selectSum('besar_lembur', 'total_lembur')
                      ->where('kd_karyawan', $kd_karyawan)
                      ->where('periode_gaji', $periode_gaji)
                      ->first();
                      
        return (float) ($total['total_lembur'] ?? 0.0);
    }

    public function getLemburWithKaryawan($periode = null)
    {
        $builder = $this->db->table($this->table . ' ul');
        $builder->select('ul.*, k.nm_karyawan, k.nip');
        $builder->join('karyawan k', 'k.id_karyawan = ul.kd_karyawan');
        
        if ($periode) {
            $builder->where('ul.periode_gaji', $periode);
        }
        
        $builder->orderBy('ul.periode_gaji', 'DESC');
        $builder->orderBy('k.nm_karyawan', 'ASC');
        return $builder->get()->getResultArray();
    }
}