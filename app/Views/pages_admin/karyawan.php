<div class="content-wrapper">
    <!-- Container untuk Header & Notifikasi -->
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        </div>
        
        <!-- Tombol Tambah Karyawan -->
         <div class="mb-3">
            <a href="<?= base_url('admin/karyawan/create') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Karyawan
            </a>
            <a href="<?= base_url('admin/datakaryawan/sinkronisasi_wajah') ?>" class="btn btn-info shadow-sm">
                <i class="fas fa-sync fa-sm text-white-50"></i> Sinkronisasi Wajah
            </a>
        </div>
                
        <!-- Notifikasi (Flashdata CI4) -->
        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('pesan') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Container untuk Tabel Data -->
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow mb-4">
               <div class="card-body">
                   <div class="table-responsive">
                       <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                           <thead class="thead-dark">
                               <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIP</th>
                                    <th class="text-center">Nama Pegawai</th>
                                    <th class="text-center">Jenis Kelamin</th>
                                    <th class="text-center">Jabatan</th>
                                    <th class="text-center">Tanggal Masuk</th>
                                    <th class="text-center">Status Kawin</th>
                                    <th class="text-center">Hak Akses</th>
                                    <th class="text-center">Foto</th>
                                    <th class="text-center">Actions</th>
                               </tr>
                           </thead>
                           <tbody>
                               <?php $no=1; foreach($karyawan as $k) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center"><?= esc($k['nip']) ?></td>
                                    <td><?= esc($k['nm_karyawan']) ?></td>
                                    <td class="text-center"><?= esc($k['kelamin']) ?></td>
                                    <td class="text-center"><?= esc($k['nm_jabatan']) ?: 'N/A' ?></td>
                                    <td class="text-center"><?= esc($k['tanggal_masuk']) ?></td>
                                    <td class="text-center"><?= esc($k['status_kawin']) ?></td>
                                    
                                    <!-- Hak Akses (Level dari tabel user) -->
                                    <td class="text-center">
                                        <?php if ($k['level']): ?>
                                            <?= esc($k['level']) ?>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">No Account</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Foto Profil -->
                                    <td class="text-center">
                                        <?php 
                                            // Menyesuaikan path ke public/foto/ sesuai Controller
                                            $foto_file = $k['foto'] ?? 'default.png';
                                            $foto_url = base_url('foto/' . $foto_file); 
                                        ?>
                                        <img src="<?= $foto_url ?>" width="50px" height="50px" style="object-fit: cover;" onerror="this.onerror=null;this.src='<?= base_url('foto/default.png')?>';">
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="text-center" style="width: 150px;">
                                        <a class="btn btn-sm btn-primary" href="<?= base_url('admin/karyawan/detail/'.$k['id_karyawan']) ?>" title="Detail"><i class="fas fa-eye"></i></a>
                                        <a class="btn btn-sm btn-info" href="<?= base_url('admin/karyawan/edit/'.$k['id_karyawan']) ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                        
                                        <!-- Tombol Delete (Memicu Modal) -->
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" 
                                                data-id="<?= $k['id_karyawan'] ?>" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                               <?php endforeach; ?>
                           </tbody>
                       </table>
                       
                        <!-- Modal Konfirmasi Hapus (Sama seperti yang digunakan di view LokasiPresensi) -->
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Penghapusan</h5>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data karyawan ini? Tindakan ini tidak dapat dibatalkan.
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                                        <a id="modal-delete-link" href="#" class="btn btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                   </div>
               </div>
            </div>
        </div>
    </section>
</div>

<!-- Script untuk mengaitkan ID dengan Modal Delete -->
<script>
    $(document).ready(function() {
        $('.delete-btn').on('click', function() {
            const karyawanId = $(this).data('id'); 
            // Pastikan URL mengarah ke Controller DataKaryawan/delete
            const baseUrl = '<?= base_url("admin/karyawan/delete") ?>'; 

            $('#modal-delete-link').attr('href', baseUrl + '/' + karyawanId);
        });
    });
</script>