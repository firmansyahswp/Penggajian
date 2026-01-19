<?php

namespace App\Models;

use CodeIgniter\Model;

class PtkpModel extends Model
{
    protected $table            = 'ptkp';
    protected $primaryKey       = 'id_ptkp';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // Tambahkan 'nama_ptkp' ke allowedFields sesuai skema gambar Anda
    protected $allowedFields    = ['nama_ptkp', 'total_ptkp', 'keterangan']; 

    // Dates
    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
}