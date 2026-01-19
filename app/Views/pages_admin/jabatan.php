<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
            </div>
            
            <a class="btn btn-sm btn-success mb-3" href="<?= base_url('admin/jabatan/create') ?>"><i class="fas fa-plus"></i> Tambah Jabatan</a>
            
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
                                    <th class="text-center">Nama Jabatan</th>
                                    <th class="text-center">Gaji Pokok</th>
                                    <th class="text-center">Uang Transport</th>
                                    <th class="text-center">Uang Makan</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; foreach($jabatan as $j) : ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td class="text-center"><?= $j['nm_jabatan'] ?></td> 
                                        
                                        <td class="text-center">Rp. <?= number_format($j['gaji_pokok'], 0, ',', '.')?></td>
                                        <td class="text-center">Rp. <?= number_format($j['uang_transport'], 0, ',', '.')?></td> 
                                        <td class="text-center">Rp. <?= number_format($j['uang_makan'], 0, ',', '.')?></td>
                                        
                                        <td class="text-center">Rp. <?= number_format($j['gaji_pokok'] + $j['uang_transport'] + $j['uang_makan'], 0, ',', '.')?></td>
                                        
                                        <td>
                                            <center>
                                                <a class="btn btn-sm btn-info" href="<?= base_url('admin/jabatan/edit/'.$j['kd_jabatan']) ?>"><i class="fas fa-edit"></i></a>
                                                
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                        data-bs-toggle="modal"  data-bs-target="#confirmDeleteModal" 
                                                        data-id="<?= $j['kd_jabatan'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </center>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Penghapusan</h5>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data jabatan ini? Tindakan ini tidak dapat dibatalkan.
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
    $(document).ready(function() {
        // Mendengarkan saat tombol Delete (kelas .delete-btn) diklik
        $('.delete-btn').on('click', function() {
            // Ambil ID dari tombol yang diklik
            const jabatanId = $(this).data('id'); 
            
            // Tentukan URL dasar (pastikan ini sesuai dengan routes CI4 Anda)
            const baseUrl = '<?= base_url("admin/jabatan/delete") ?>'; 

            // Set href tombol Hapus di Modal
            $('#modal-delete-link').attr('href', baseUrl + '/' + jabatanId);
        });
    });
</script>