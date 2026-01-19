<?php 

namespace App\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
    protected $table      = 'jabatan';
    protected $primaryKey = 'kd_jabatan';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = ['nm_jabatan', 'gaji_pokok', 'uang_transport', 'uang_makan'];
}