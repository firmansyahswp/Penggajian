<?php

namespace App\Models;

use CodeIgniter\Model;

class AngsuranPinjamanModel extends Model
{
    protected $table            = 'angsuran_pinjaman';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // Sesuaikan dengan perbaikan SQL (menggunakan id_permohonan)
    protected $allowedFields    = [
        'id_permohonan', 'kd_karyawan', 'tanggal_bayar', 'besar_angsuran', 
        'tipe_pembayaran', 'periode_gaji', 'keterangan'
    ];

    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';

    /**
     * Menghitung saldo pinjaman (total debt vs total payments) untuk satu permohonan.
     * Saldo > 0 berarti LUNAS. Saldo < 0 berarti BELUM LUNAS/AKTIF.
     */
    public function getSaldoPinjaman(int $id_permohonan)
    {
        $total = $this->selectSum('besar_angsuran', 'total_saldo')
                      ->where('id_permohonan', $id_permohonan)
                      ->first();
                      
        return (float) ($total['total_saldo'] ?? 0.0);
    }
    
    /**
     * Mendapatkan daftar pinjaman (berdasarkan record DEBT) dengan filter Bulan/Tahun dan Search Keyword.
     * Digunakan oleh Admin\Angsuran::index().
     */
    public function getDaftarPinjamanAktif($bulanTahun = null, $keyword = null)
    {
        $builder = $this->db->table($this->table . ' ap');
        $builder->select('
            ap.*,
            k.nm_karyawan,
            k.nip
        ');
        
        $builder->join('karyawan k', 'k.id_karyawan = ap.kd_karyawan'); //
        $builder->where('ap.tipe_pembayaran', 'DEBT'); // Hanya ambil record yang menandai hutang
        
        // Filter Pencarian
        if ($keyword) {
            $builder->groupStart()
                ->orLike('k.nm_karyawan', $keyword)
                ->orLike('k.nip', $keyword)
                ->groupEnd();
        }

        // Filter Bulanan (Berdasarkan Tanggal Pencairan/DEBT record)
        if ($bulanTahun) {
            $builder->where("DATE_FORMAT(ap.tanggal_bayar, '%Y-%m') =", $bulanTahun);
        }

        $builder->orderBy('ap.tanggal_bayar', 'DESC');
        return $builder->get()->getResultArray();
    }
    
    /**
     * Mengambil semua riwayat angsuran untuk satu ID Permohonan.
     */
    public function getAngsuranByPermohonanId(int $id_permohonan)
    {
        return $this->where('id_permohonan', $id_permohonan)
                    ->orderBy('tanggal_bayar', 'ASC')
                    ->findAll();
    }
}