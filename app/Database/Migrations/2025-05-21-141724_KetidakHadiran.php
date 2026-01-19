<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Ketidakhadiran extends Migration
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
            'kd_karyawan' => [ // FK ke tabel karyawan
                'type'       => 'INT',
                'constraint' => 20, // Mengikuti id_karyawan di tabel karyawan
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'deskripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'file' => [ // Sesuai nama kolom di gambar
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status_pengajuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
        ]);
        $this->forge->addKey('id', true);
        // Menambahkan Foreign Key ke tabel karyawan
        $this->forge->addForeignKey('kd_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ketidakhadiran');
    }

    public function down()
    {
        $this->forge->dropTable('ketidakhadiran');
    }
}