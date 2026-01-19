<style>
    /* Mengubah ID map_absen menjadi map_detail dan memastikan tinggi */
    #map_detail { 
        height: 400px; 
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Mengubah Struktur Row Utama menjadi 12 kolom penuh -->
            <div class="row">
                <div class="col-12">
                    
                    <!-- === ROW UTAMA UNTUK DETAIL DAN PETA === -->
                    <div class="row">
                        
                        <!-- KOLOM 1 (Kiri): CARD DETAIL LOKASI (6 kolom) -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Lengkap Lokasi</h6>
                                </div>
                                <div class="card-body">
                                    
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                            <tr>
                                                <th style="width: 35%;">Nama Lokasi</th>
                                                <td><?= esc($lokasi['nama_lokasi']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Alamat Lokasi</th>
                                                <td><?= nl2br(esc($lokasi['alamat_lokasi'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Tipe Lokasi</th>
                                                <td><?= esc($lokasi['tipe_lokasi']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Latitude</th>
                                                <td><?= esc($lokasi['latitude']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Longitude</th>
                                                <td><?= esc($lokasi['longitude']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Radius Toleransi</th>
                                                <td><?= number_format(esc($lokasi['radius']), 0, ',', '.') ?> Meter</td>
                                            </tr>
                                            <tr>
                                                <th>Zona Waktu</th>
                                                <td><?= esc($lokasi['zona_waktu']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Jam Masuk</th>
                                                <td><?= esc($lokasi['jam_masuk']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Jam Pulang</th>
                                                <td><?= esc($lokasi['jam_pulang']) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <a href="<?= base_url('admin/lokasipresensi')?>" class="btn btn-warning mt-3">Kembali ke Daftar</a>
                                    <a href="<?= base_url('admin/lokasipresensi/edit/'.$lokasi['id'])?>" class="btn btn-info mt-3"><i class="fas fa-edit"></i> Edit Data</a>

                                </div>
                            </div>
                        </div>
                        
                        <!-- KOLOM 2 (Kanan): PETA LEAFLET (6 kolom) -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Peta Lokasi Kantor</h6>
                                </div>
                                <div class="card-body p-0">
                                    <!-- ID PETA DIPERBAIKI -->
                                    <div id="map_detail"></div>
                                </div>
                                <div class="card-footer text-muted small">
                                    Marker menunjukkan pusat lokasi presensi. Lingkaran adalah batas radius <?= $lokasi['radius'] ?> meter.
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
</div>

<!-- === ASET LEAFLET.JS === -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/SVRKdeZ1V+zB7x5f4=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20n6a9K1q/6sXjJg7/e38c4p2T7dFaqx86S/e8JgAFA=" crossorigin=""></script>

<script>
    // Ambil koordinat dari PHP
    const latKantor = parseFloat("<?= esc($lokasi['latitude']) ?>");
    const lonKantor = parseFloat("<?= esc($lokasi['longitude']) ?>");
    const radius = parseFloat("<?= esc($lokasi['radius']) ?>");
    
    // Fungsi inisialisasi peta
    function initMapDetail() {
        if (!latKantor || !lonKantor) {
            console.error("Koordinat kantor tidak valid.");
            return;
        }

        // 1. Inisialisasi Peta pada ID 'map_detail'
        const mapDetail = L.map('map_detail').setView([latKantor, lonKantor], 16);

        // 2. Tambahkan Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(mapDetail);

        // 3. Tambahkan Marker Lokasi Kantor
        L.marker([latKantor, lonKantor]).addTo(mapDetail)
            .bindPopup(`<b><?= esc($lokasi['nama_lokasi']) ?></b><br>Pusat Presensi`)
            .openPopup();
            
        // 4. Tambahkan Lingkaran Radius
        L.circle([latKantor, lonKantor], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: radius
        }).addTo(mapDetail);
        
        // PENTING: InvalidateSize diperlukan jika map dimuat dalam elemen tersembunyi/tab/card
        // Map perlu di-render ulang setelah dimuat.
        setTimeout(function () {
            mapDetail.invalidateSize();
        }, 100);
    }

    // Panggil inisialisasi saat window dimuat
    window.onload = initMapDetail;
    
</script>