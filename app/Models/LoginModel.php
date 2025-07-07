<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id';
    protected $allowedFields = ['id_karyawan', 'username', 'password', 'level'];

}
