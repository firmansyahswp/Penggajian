<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pegawai extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_karyawan' => [
                'type'           => 'INT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'nm_karyawan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'kd_jabatan' => [
                'type'       => 'INT',
                'constraint' => 20,
                'unsigned'       => true,
            ],
            'kelamin' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'agama' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
            ],
            'tempat_lahir' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'tgl_lahir' => [
                'type' => 'DATE',
            ],
            'status_kawin' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
            ],
            'jumlah_anak' => [
                'type'       => 'INT',
                'constraint' => '2',
            ],
            'id_ptkp' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'       => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
        ]);

        $this->forge->addKey('id_karyawan', true);
        $this->forge->addForeignKey('id_ptkp', 'ptkp', 'id_ptkp', 'CASCADE', 'CASCADE');
        $this->forge->createTable('karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('karyawan');
    }

}
