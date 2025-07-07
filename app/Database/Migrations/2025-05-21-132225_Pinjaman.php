<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pinjaman extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'no_pinjaman' => [
                'type'           => 'INT',
                'constraint'     => 30,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'besar_pinjaman' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_lunas' => [
                'type'       => 'BOOLEAN',
            ],
            'kd_user' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'kd_karyawan' => [
                'type'       => 'INT',
                'constraint' => 20,       // <-- Ubah constraint menjadi 20
                'unsigned'   => true,     // <-- Tambahkan properti unsigned
            ],
        ]);
        $this->forge->addKey('no_pinjaman', true);
        $this->forge->addForeignKey('kd_karyawan', 'karyawan', 'id_karyawan');
        $this->forge->createTable('pinjaman');
    }

    public function down()
    {
        $this->forge->dropTable('pinjaman');
    }
}