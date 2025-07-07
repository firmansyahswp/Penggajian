<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Jabatan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kd_jabatan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nm_jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'gaji_pokok' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'uang_transport' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'uang_makan' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
        ]);

        $this->forge->addKey('kd_jabatan', true);
        $this->forge->createTable('jabatan');
    }

    public function down()
    {
        $this->forge->dropTable('jabatan');
    }
}