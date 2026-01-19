<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <!-- Judul halaman yang dikirim dari Controller -->
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0"><?= $title ?></h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Alert Selamat Datang (Gaya Mirip Gambar) -->
            <div class="alert alert-success" style="width: 100%; max-width: 800px; background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
                Selamat datang. Anda login sebagai Karyawan.
            </div>

            <!-- Card Profil Karyawan -->
            <div class="card shadow" style="width: 100%; max-width: 800px; margin-bottom: 120px;">
                
                <!-- Card Header (Header biru solid seperti di gambar) -->
                <div class="card-header text-white" style="background-color: #007bff; border-bottom: 1px solid #007bff;">
                    <h3 class="card-title font-weight-bold" style="color: white !important;">
                        Data Karyawan
                    </h3>
                </div>

                <?php if (!empty($karyawan)): ?>
                    <?php foreach($karyawan as $k) : ?>
                    <div class="card-body">
                        <div class="row align-items-center">
                            
                            <!-- Kolom Foto -->
                            <div class="col-md-4 text-center">
                                <!-- Mengambil $k['foto'] sebagai elemen array -->
                                <img 
                                    style="width: 100%; max-width: 200px; height: auto; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" 
                                    src="<?= base_url('foto/' . $k['foto']) ?>" 
                                    alt="Foto Karyawan"
                                    onerror="this.onerror=null; this.src='https://placehold.co/200x250/007bff/ffffff?text=Karyawan';"
                                >
                            </div>

                            <!-- Kolom Detail Data -->
                            <div class="col-md-8">
                                <table class="table" style="border: none;">
                                    <tr>
                                        <!-- Label (Karyawan) -->
                                        <td style="width: 35%; padding-left: 20px;">Nama Karyawan</td>
                                        <td style="width: 5%;">:</td>
                                        <td><?= session()->get('nama') ?></td>
                                    </tr>

                                    <tr>
                                        <!-- Label (Jabatan) -->
                                        <td style="padding-left: 20px;">Jabatan</td>
                                        <td>:</td>
                                        <td><?= $k['jabatan'] ?></td> <!-- Diubah dari $k->jabatan -->
                                    </tr>

                                    <tr>
                                        <!-- Label (Tanggal Masuk) -->
                                        <td style="padding-left: 20px;">Tanggal Masuk</td>
                                        <td>:</td>
                                        <td><?= $k['tanggal_masuk'] ?></td> <!-- Diubah dari $k->tanggal_masuk -->
                                    </tr>

                                    <tr>
                                        <!-- Label (Status) -->
                                        <td style="padding-left: 20px;">Jabatan</td>
                                        <td>:</td>
                                        <td><?= $k['jabatan'] ?></td> <!-- Diubah dari $k->status -->
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card-body">
                        <p class="text-danger">Data profil karyawan tidak ditemukan.</p>
                    </div>
                <?php endif; ?>

            </div>
            <!-- /.card -->

        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->