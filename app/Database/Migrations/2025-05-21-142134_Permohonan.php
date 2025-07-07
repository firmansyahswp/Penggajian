<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Permohonan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'kd_user' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'kd_karyawan' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'besar_pinjaman' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'timedate' => [
                'type' => 'TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('permohonan_pinjaman');
    }

    public function down()
    {
        $this->forge->dropTable('permohonan_pinjaman');
    }
}