<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'kd_user';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_karyawan', 'username', 'password', 'level','foto'];

    // Dates
    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';

    public function updatePassword(int $id_user, string $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $this->update($id_user, ['password' => $hashedPassword]);
        return true;
    }
}