<?php 

namespace App\Models;

use CodeIgniter\Model;

class PresensiModel extends Model
{
    protected $table      = 'presensi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = [
        'tanggal_masuk', 'jam_masuk', 'jam_keluar', 'kd_user', 'kd_karyawan', 
        'id_lokasi', 'foto_masuk', 'tanggal_keluar', 'foto_keluar','is_terlambat_denda'
    ];

    /**
     * Mengambil data rekap absensi untuk satu tanggal tertentu
     * Menggabungkan data Hadir (tabel presensi) dan Alpa (tabel ketidakhadiran)
     */
    public function getRekapHarian($tanggal)
    {
        $db = \Config\Database::connect();
        
        $sql_hadir = $db->table('presensi p')
            ->select('
                p.kd_karyawan as id_karyawan, 
                k.nm_karyawan, 
                p.tanggal_masuk, 
                p.jam_masuk, 
                p.jam_keluar, 
                p.foto_masuk,
                p.foto_keluar,
                lp.nama_lokasi, 
                lp.jam_masuk as jam_masuk_lokasi, 
                "HADIR" as tipe_data'
            )
            ->join('karyawan k', 'k.id_karyawan = p.kd_karyawan', 'left')
            ->join('lokasi_presensi lp', 'lp.id = p.id_lokasi', 'left')
            ->where('p.tanggal_masuk', $tanggal)
            ->getCompiledSelect();

        $sql_alpa = $db->table('ketidakhadiran kh')
            ->select('
                kh.kd_karyawan as id_karyawan, 
                k.nm_karyawan, 
                kh.tanggal as tanggal_masuk, 
                "-" as jam_masuk, 
                "-" as jam_keluar, 
                NULL as foto_masuk,
                NULL as foto_keluar,
                kh.keterangan as nama_lokasi, 
                "-" as jam_masuk_lokasi, 
                "ALPA" as tipe_data'
            )
            ->join('karyawan k', 'k.id_karyawan = kh.kd_karyawan', 'left')
            ->where('kh.tanggal', $tanggal)
            ->where('kh.keterangan', 'ALPA')
            ->getCompiledSelect();

        // Gabungkan dengan UNION
        return $db->query("$sql_hadir UNION ALL $sql_alpa")->getResultArray();
    }

    /**
     * Mengambil data rekap absensi dalam rentang tanggal tertentu (Mingguan/Periode)
     */
    public function getRekapMingguan($tglAwal, $tglAkhir)
    {
        $db = \Config\Database::connect();
        
        // 1. Data Karyawan yang HADIR
        $sql_hadir = $db->table('presensi p')
            ->select('
                p.kd_karyawan as id_karyawan, 
                k.nm_karyawan, 
                p.tanggal_masuk, 
                p.jam_masuk, 
                p.jam_keluar, 
                lp.nama_lokasi, 
                lp.jam_masuk as jam_masuk_lokasi, 
                "HADIR" as tipe_data'
            )
            ->join('karyawan k', 'k.id_karyawan = p.kd_karyawan', 'left')
            ->join('lokasi_presensi lp', 'lp.id = p.id_lokasi', 'left')
            ->where('p.tanggal_masuk >=', $tglAwal)
            ->where('p.tanggal_masuk <=', $tglAkhir)
            ->getCompiledSelect();

        // 2. Data Karyawan yang ALPA
        $sql_alpa = $db->table('ketidakhadiran kh')
            ->select('
                kh.kd_karyawan as id_karyawan, 
                k.nm_karyawan, 
                kh.tanggal as tanggal_masuk, 
                "-" as jam_masuk, 
                "-" as jam_keluar, 
                kh.keterangan as nama_lokasi, 
                "-" as jam_masuk_lokasi, 
                "ALPA" as tipe_data'
            )
            ->join('karyawan k', 'k.id_karyawan = kh.kd_karyawan', 'left')
            ->where('kh.tanggal >=', $tglAwal)
            ->where('kh.tanggal <=', $tglAkhir)
            ->where('kh.keterangan', 'ALPA')
            ->getCompiledSelect();

        return $db->query("SELECT * FROM ($sql_hadir UNION ALL $sql_alpa) AS gabungan ORDER BY nm_karyawan ASC, tanggal_masuk ASC")
                  ->getResultArray();
    }

    public function getRekapBulanan($bulanTahun)
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil data karyawan yang HADIR di bulan tersebut
        $sql_hadir = $db->table('presensi p')
            ->select('
                p.kd_karyawan as id_karyawan,         
                k.nip,
                k.nm_karyawan,
                p.tanggal_masuk,
                p.jam_masuk,
                p.jam_keluar,
                l.nama_lokasi,
                l.jam_masuk as jam_masuk_lokasi,
                "HADIR" as tipe_data
            ')
            ->join('karyawan k', 'k.id_karyawan = p.kd_karyawan', 'left')
            ->join('lokasi_presensi l', 'l.id = p.id_lokasi', 'left')
            ->where("DATE_FORMAT(p.tanggal_masuk, '%Y-%m') =", $bulanTahun)
            ->getCompiledSelect();

        // 2. Ambil data karyawan yang ALPA di bulan tersebut
        $sql_alpa = $db->table('ketidakhadiran kh')
            ->select('
                kh.kd_karyawan as id_karyawan,
                k.nip,
                k.nm_karyawan,
                kh.tanggal as tanggal_masuk,
                "-" as jam_masuk,
                "-" as jam_keluar,
                kh.keterangan as nama_lokasi,
                "-" as jam_masuk_lokasi,
                "ALPA" as tipe_data
            ')
            ->join('karyawan k', 'k.id_karyawan = kh.kd_karyawan', 'left')
            ->where("DATE_FORMAT(kh.tanggal, '%Y-%m') =", $bulanTahun)
            ->where('kh.keterangan', 'ALPA')
            ->getCompiledSelect();

        return $db->query("SELECT * FROM ($sql_hadir UNION ALL $sql_alpa) AS gabungan ORDER BY nm_karyawan ASC, tanggal_masuk ASC")
                ->getResultArray();
    }

    
    public function getRiwayatAbsenByKaryawan(int $kd_karyawan, string $periode)
    {
        $builder = $this->db->table($this->table . ' p');
        
        $builder->select('
            p.*, 
            lp.nama_lokasi,
            lp.jam_masuk as jam_masuk_kantor, 
            (
                CASE 
                    WHEN p.is_terlambat_denda = 1 THEN "Terlambat"
                    WHEN p.jam_masuk IS NOT NULL THEN "Tepat Waktu"
                    ELSE "Belum Absen"
                END
            ) AS status_masuk'
        );
        
        $builder->join('lokasi_presensi lp', 'lp.id = p.id_lokasi', 'left'); 
        $builder->where('p.kd_karyawan', $kd_karyawan); 
        $builder->like('p.tanggal_masuk', $periode, 'after'); 
        
        $builder->orderBy('p.tanggal_masuk', 'DESC');
        $builder->orderBy('p.jam_masuk', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}