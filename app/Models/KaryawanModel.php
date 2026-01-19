<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'id_karyawan';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // Tambahkan 'id_lokasi_default' ke daftar field yang diizinkan
    protected $allowedFields    = [
        'nip', 'nm_karyawan', 'kd_jabatan', 'id_lokasi_default', 'kelamin', 'agama', 'alamat', 
        'no_telp', 'tempat_lahir', 'tgl_lahir', 'status_kawin', 'jumlah_anak', 
        'id_ptkp', 'tanggal_masuk','face_descriptor'
    ]; //

    // Dates
    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';

    /**
     * Mengambil data karyawan dengan detail Jabatan, User, Lokasi, dan PTKP (melalui JOIN).
     *
     * @param int|null $id_karyawan ID Karyawan untuk detail data tunggal.
     * @return array|object Hasil query.
     */
   public function getKaryawanWithDetails(int $id_karyawan = null)
{
    $builder = $this->db->table('karyawan');

    $builder->select("
        karyawan.*, 
        jabatan.nm_jabatan, 
        jabatan.gaji_pokok,           
        jabatan.uang_transport,       
        jabatan.uang_makan,           
        user.username, 
        user.level, 
        user.kd_user,
        user.foto,
        lokasi_presensi.nama_lokasi as nama_lokasi_default, 
        lokasi_presensi.tipe_lokasi as tipe_lokasi_default,
        lokasi_presensi.id as id_lokasi_default,
        ptkp.nama_ptkp,
        ptkp.total_ptkp
    ");

    $builder->join('jabatan', 'jabatan.kd_jabatan = karyawan.kd_jabatan', 'left');
    $builder->join('user', 'user.id_karyawan = karyawan.id_karyawan', 'left');
    $builder->join('lokasi_presensi', 'lokasi_presensi.id = karyawan.id_lokasi_default', 'left');
    $builder->join('ptkp', 'ptkp.id_ptkp = karyawan.id_ptkp', 'left');

    if ($id_karyawan !== null) {
        return $builder->where('karyawan.id_karyawan', $id_karyawan)->get()->getRowArray();
    }

    return $builder->get()->getResultArray();
}

}