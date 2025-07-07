<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Absen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_absen' => [
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
            'status_kehadiran' => [
                'type' => 'ENUM("Hadir","Izin","Sakit","Alpha")',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kd_user' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'kd_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id_absen', true);
        $this->forge->createTable('absensi');
    }

    public function down()
    {
        $this->forge->dropTable('absensi');
    }

}
