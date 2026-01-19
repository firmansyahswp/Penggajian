<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAbsensiToPresensi extends Migration
{
    public function up()
    {
        // 1. Rename Table: absensi -> presensi
        $this->forge->renameTable('absensi', 'presensi');

        // 2. Rename Columns
        // Ubah Primary Key: id_absen -> id
        $this->forge->modifyColumn('presensi', [
            'id_absen' => [
                'name'           => 'id',
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
        ]);

        // Ubah kd_karyawan (FK) untuk menyesuaikan constraint di tabel 'karyawan' (20)
        $this->forge->modifyColumn('presensi', [
            'kd_karyawan' => [
                'type'       => 'INT',
                'constraint' => 20,
            ],
        ]);
        
        // Ubah 'tanggal' menjadi 'tanggal_masuk'
        $this->forge->modifyColumn('presensi', [
            'tanggal' => [
                'name' => 'tanggal_masuk',
                'type' => 'DATE',
            ],
        ]);
        
        // Hapus kolom status_kehadiran dan keterangan (Karena data izin/sakit dipindah ke tabel ketidakhadiran)
        $this->forge->dropColumn('presensi', ['status_kehadiran', 'keterangan']);


        // 3. Add New Columns (Sesuai skema presensi di gambar)
        $fields = [
            'id_lokasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'kd_karyawan',
            ],
            'foto_masuk' => [
                'type'       => 'VARCHAR',
                'constraint' => '225',
                'null'       => true,
                'after'      => 'jam_masuk',
            ],
            'tanggal_keluar' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'foto_masuk',
            ],
            'foto_keluar' => [
                'type'       => 'VARCHAR',
                'constraint' => '225',
                'null'       => true,
                'after'      => 'jam_keluar',
            ],
        ];

        $this->forge->addColumn('presensi', $fields);

        // 4. Add Foreign Keys baru
        // FK ke lokasi_presensi
        $this->forge->addForeignKey('id_lokasi', 'lokasi_presensi', 'id', 'SET NULL', 'CASCADE');
        // FK ke karyawan (jika belum ada di file absensi lama) - kita tambahkan untuk memastikan.
        // Catatan: Karena kd_karyawan sudah ada di absensi, kita hanya perlu menambahkan relasinya jika belum ada.
        // Berdasarkan file absensi lama, tidak ada foreign key untuk kd_karyawan, jadi kita tambahkan.
        $this->forge->addForeignKey('kd_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Operasi balik (rollback)
        // 1. Drop Foreign Keys
        $this->forge->dropForeignKey('presensi', 'presensi_id_lokasi_foreign');
        $this->forge->dropForeignKey('presensi', 'presensi_kd_karyawan_foreign');

        // 2. Drop New Columns
        $this->forge->dropColumn('presensi', ['id_lokasi', 'foto_masuk', 'tanggal_keluar', 'foto_keluar']);
        
        // 3. Revert Renamed Columns
        $this->forge->modifyColumn('presensi', [
            'id' => [
                'name'           => 'id_absen',
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'tanggal_masuk' => [
                'name' => 'tanggal',
                'type' => 'DATE',
            ],
        ]);
        
        // Revert kd_karyawan constraint (opsional, tergantung kebutuhan)
        $this->forge->modifyColumn('presensi', [
            'kd_karyawan' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        
        // 4. Re-add Dropped Columns (status_kehadiran dan keterangan)
        $this->forge->addColumn('presensi', [
            'status_kehadiran' => [
                'type' => 'ENUM("Hadir","Izin","Sakit","Alpha")',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        // 5. Rename Table: presensi -> absensi
        $this->forge->renameTable('presensi', 'absensi');
    }
}