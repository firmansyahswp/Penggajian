<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiPresensiModel extends Model
{
    protected $table            = 'lokasi_presensi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        // 'alamat_lokasi' telah ditambahkan di sini
        'nama_lokasi', 'alamat_lokasi', 'tipe_lokasi', 'latitude', 'longitude', 
        'radius', 'zona_waktu', 'jam_masuk', 'jam_pulang' 
    ];

    // Dates
    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
}