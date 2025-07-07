<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Ptkp extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_ptkp' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'total_ptkp' => [
                'type'       => 'DECIMAL', // Ubah dari VARCHAR
                'constraint' => '18,2',    // Sesuaikan presisi dan skala
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
        ]);
        $this->forge->addKey('id_ptkp', true);
        $this->forge->createTable('ptkp');
    }

    public function down()
    {
        $this->forge->dropTable('ptkp');
    }
}