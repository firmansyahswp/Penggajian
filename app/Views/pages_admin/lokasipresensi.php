<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
            </div>
            
            <a class="btn btn-sm btn-success mb-3" href="<?= base_url('admin/lokasipresensi/create') ?>"><i class="fas fa-plus"></i> Tambah Lokasi Presensi</a>
            
            <?php if (session()->getFlashdata('pesan')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('pesan') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

        </div>
    </div>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Lokasi</th>
                                    <th class="text-center">Alamat Lokasi</th> 
                                    <th class="text-center">Tipe Lokasi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; foreach($lokasi_presensi as $lokasi) : ?>
                                    <tr>
                                        <td class="text-center" style="width: 50px;"><?= $no++ ?></td>
                                        <td><?= $lokasi['nama_lokasi'] ?></td> 
                                        <td><?= $lokasi['alamat_lokasi'] ?></td> 
                                        <td class="text-center"><?= $lokasi['tipe_lokasi'] ?></td> 
                                        
                                        <td class="text-center" style="width: 150px;">
                                            <a class="btn btn-sm btn-primary" href="<?= base_url('admin/lokasipresensi/detail/'.$lokasi['id']) ?>" title="Detail Lokasi"><i class="fas fa-eye"></i></a>
                                            
                                            <a class="btn btn-sm btn-info" href="<?= base_url('admin/lokasipresensi/edit/'.$lokasi['id']) ?>" title="Edit Lokasi"><i class="fas fa-edit"></i></a>
                                                
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" 
                                                    data-id="<?= $lokasi['id'] ?>" title="Hapus Lokasi">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- Modal Konfirmasi Hapus -->
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Penghapusan</h5>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data lokasi presensi ini? Tindakan ini tidak dapat dibatalkan.
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

<script>
    // JQuery script untuk mengelola modal delete
    $(document).ready(function() {
        // Mendengarkan saat tombol Delete (kelas .delete-btn) diklik
        $('.delete-btn').on('click', function() {
            // Ambil ID dari tombol yang diklik (Primary Key 'id')
            const lokasiId = $(this).data('id'); 
            
            // Tentukan URL dasar yang baru (sesuai Controller LokasiPresensi)
            const baseUrl = '<?= base_url("admin/lokasipresensi/delete") ?>'; 

            // Set href tombol Hapus di Modal
            $('#modal-delete-link').attr('href', baseUrl + '/' + lokasiId);
        });
    });
</script>