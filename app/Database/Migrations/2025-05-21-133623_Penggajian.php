<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Penggajian extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'no_penggajian' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true, // Tambahkan ini
                'unsigned'       => true, // Tambahkan ini
            ],
            'periode_pph21' => [
                'type' => 'DATE',
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'gaji_pokok' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'tunj_transport' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'tunj_makan' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'total_lembur' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'total_bonus' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'total_pinjaman' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
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
        $this->forge->addKey('no_penggajian', true);
        $this->forge->createTable('penggajian');
    }

    public function down()
    {
        $this->forge->dropTable('penggajian');
    }
}