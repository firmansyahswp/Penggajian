<?php

namespace App\Models;

use CodeIgniter\Model;

class TarifKonstantaModel extends Model
{
    protected $table            = 'tarif_konstanta';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_konstanta', 'tipe_nilai', 'nilai', 'keterangan'];

    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
    
    /**
     * Mengambil semua konstanta dalam format key-value.
     */
    public function getTarifAsArray()
    {
        $data = $this->findAll();
        $tarif = [];
        foreach ($data as $row) {
            $tarif[$row['nama_konstanta']] = (float) $row['nilai'];
        }
        return $tarif;
    }
}