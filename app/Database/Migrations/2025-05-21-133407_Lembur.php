<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Lembur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_lembur' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jam_masuk' => [
                'type' => 'TIME',
            ],
            'jam_keluar' => [
                'type' => 'TIME',
            ],
            'jml_jam' => [
                'type' => 'INT',
            ],
            'uang_lembur' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kd_user' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'kd_karyawan' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id_lembur', true);
        $this->forge->createTable('lembur');
    }

    public function down()
    {
        $this->forge->dropTable('lembur');
    }
}