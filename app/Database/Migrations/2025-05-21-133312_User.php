<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kd_user' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'id_karyawan' => [
                'type'           => 'INT',
                'constraint'     => 20,
                'unsigned'       => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'level' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
        ]);
        $this->forge->addKey('kd_user', true);
        $this->forge->addForeignKey('id_karyawan', 'karyawan','id_karyawan', 'CASCADE', 'CASCADE'); // Ubah 'pegawai' menjadi 'karyawan'
        $this->forge->createTable('user');
    }

    public function down()
    {
        $this->forge->dropTable('user');
    }
}