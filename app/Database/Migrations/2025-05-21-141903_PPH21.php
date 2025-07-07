<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PPH21 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'no_pph21' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'periode_pph21' => [
                'type' => 'DATE',
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'gaji_pokok' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'tunj_transport' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'tunj_makan' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'total_lembur' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'total_bonus' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'total_pinjaman' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'bruto' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'biaya_jabatan' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'netto_sebulan' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'netto_setahun' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'total_ptkp' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'pkp' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'pph21_setahun' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'pph21_sebulan' => ['type' => 'DECIMAL', 'constraint' => '18,2'], // Ubah dari VARCHAR
            'kd_karyawan' => [
                'type'       => 'INT', // Ubah dari VARCHAR
                'constraint' => 11,    // Sesuaikan constraint dengan id_karyawan
            ],
        ]);
        $this->forge->addKey('no_pph21', true);
        $this->forge->createTable('pph21');
    }

    public function down()
    {
        $this->forge->dropTable('pph21');
    }
}