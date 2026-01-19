<?php 
namespace App\Models;

use CodeIgniter\Model;

class KetidakhadiranModel extends Model
{
    protected $table            = 'ketidakhadiran';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // HANYA field yang ada, dengan 'keterangan' ditambahkan
    protected $allowedFields    = ['kd_karyawan', 'tanggal', 'keterangan', 'deskripsi', 'file', 'status_pengajuan'];

    // Dates
    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
    
    /**
     * Mengambil data ketidakhadiran dan melakukan JOIN ke tabel Karyawan.
     * @param int|null $kd_karyawan ID Karyawan (jika null, ambil semua).
     */
    public function getIzinWithKaryawan(int $karyawanId = null, $keyword = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('ketidakhadiran.*, karyawan.nm_karyawan, karyawan.nip');
        $builder->join('karyawan', 'karyawan.id_karyawan = ketidakhadiran.kd_karyawan');
        
        if ($karyawanId !== null) {
            $builder->where('ketidakhadiran.kd_karyawan', $karyawanId);
        }
        
        // LOGIKA PENCARIAN BARU
        if ($keyword) {
            $builder->groupStart()
                ->orLike('karyawan.nm_karyawan', $keyword)
                ->orLike('karyawan.nip', $keyword)
                ->orLike('ketidakhadiran.keterangan', $keyword)
                ->orLike('ketidakhadiran.status_pengajuan', $keyword)
                ->groupEnd();
        }
        
        $builder->orderBy('ketidakhadiran.tanggal', 'DESC');
        return $builder->get()->getResultArray();
    }
}