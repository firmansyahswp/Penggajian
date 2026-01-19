<style>
    /* Styling tetap sama */
    .parent-clock{
        display: grid;
        grid-template-columns: auto auto auto auto auto;
        font-size: 35px;
        font-weight : bold;
        justify-content: center;
    }
    .status-waktu {
        margin-top: 10px;
        font-size: 0.9em;
    }
    /* === STYLING LEAFLET === */
    #map_absen {
        height: 300px;
        width: 100%;
        border-radius: 8px;
    }
</style>

<div class="container-fluid">
    
    <div class="row mb-2">
        <div class="col-sm-12">
            <h1 class="m-0"><?= $title ?></h1>
        </div>
    </div>
    
    <?php if (session()->getFlashdata('sukses')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('sukses') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('gagal')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('gagal') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <hr>
    
    <?php if (isset($is_hari_libur) && $is_hari_libur) : ?>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-calendar-times fa-5x text-secondary"></i>
                        </div>
                        <h2 class="text-secondary font-weight-bold">HARI INI LIBUR</h2>
                        <p class="lead text-muted">Sistem absensi dinonaktifkan sementara karena hari ini adalah hari libur (Sabtu/Minggu).</p>
                        <p>Silakan kembali lagi pada hari kerja berikutnya untuk melakukan presensi.</p>
                        <a href="<?= base_url('pegawai/dashboard') ?>" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="row">
            <div class="col-md-2"></div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header h4 text-center">Presensi Masuk</div>
                    <div class="card-body text-center">
                        <div class="fw-bold h4 mb-0"><?= date('d F Y') ?></div>
                        
                        <?php if (!$status_presensi) : ?>
                            <div class="parent-clock" id="clock-masuk">
                                <div id="jam-masuk"></div>
                                <div>:</div>
                                <div id="menit-masuk"></div>
                                <div>:</div>
                                <div id="detik-masuk"></div>
                            </div>
                            
                            <form method="POST" action="<?= base_url('pegawai/absen/presensi_masuk') ?>" id="form_masuk">
                                <input type="hidden" name="latitude_kantor" value="<?= $lokasi_presensi['latitude'] ?? '' ?>" id="lat_kantor_masuk">
                                <input type="hidden" name="longitude_kantor" value="<?= $lokasi_presensi['longitude'] ?? '' ?>" id="lon_kantor_masuk">
                                <input type="hidden" name="radius" value="<?= $lokasi_presensi['radius'] ?? 0 ?>" id="radius_input">
                                <input type="hidden" name="id_lokasi_default" value="<?= $lokasi_presensi['id'] ?? '' ?>">
                                <input type="hidden" name="id_karyawan" value="<?= $karyawan['id_karyawan'] ?? '' ?>">
                                <input type="hidden" name="latitude_pegawai" id="latitude_pegawai">
                                <input type="hidden" name="longitude_pegawai" id="longitude_pegawai">
                                <input type="hidden" name="accuracy_pegawai" id="accuracy_pegawai">
                                <input type="hidden" name="tanggal_masuk" value="<?= date('Y-m-d') ?>">
                                <input type="hidden" name="jam_masuk" id="jam_masuk_input"> 
                                <input type="hidden" id="jam_masuk_kantor_js" value="<?= substr($jam_masuk_kantor ?? '08:00', 0, 5) ?>">
                                
                                <button class="btn btn-primary mt-3" type="submit" id="btn_masuk" disabled>Masuk</button>
                                <p class="text-muted mt-2 small" id="loc_status_masuk">Menunggu GPS...</p>
                            </form>
                        <?php else : ?>
                            <div class="fw-bold h1 my-3"><?= $status_presensi['jam_masuk'] ?? 'N/A' ?></div>
                            <p class="text-success mt-3 h5">✅ Sudah Presensi Masuk</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header h4 text-center">Presensi Keluar</div>
                    <div class="card-body text-center">
                        <div class="fw-bold h4 mb-0"><?= date('d F Y') ?></div>
                        
                        <?php 
                            $jam_keluar = $status_presensi['jam_keluar'] ?? null;
                            $sudah_masuk = !empty($status_presensi);
                            $sudah_keluar = $sudah_masuk && !empty($jam_keluar) && $jam_keluar !== '00:00:00';
                        ?>

                        <?php if ($sudah_masuk && !$sudah_keluar) : ?>
                            <div class="parent-clock">
                                <div id="jam-keluar"></div>
                                <div>:</div>
                                <div id="menit-keluar"></div>
                                <div>:</div>
                                <div id="detik-keluar"></div>
                            </div>
                            
                            <form method="POST" action="<?= base_url('pegawai/absen/presensi_keluar') ?>" id="form_keluar">
                                <input type="hidden" name="latitude_kantor" value="<?= $lokasi_presensi['latitude'] ?? '' ?>" id="lat_kantor_keluar">
                                <input type="hidden" name="longitude_kantor" value="<?= $lokasi_presensi['longitude'] ?? '' ?>" id="lon_kantor_keluar">
                                <input type="hidden" name="radius" value="<?= $lokasi_presensi['radius'] ?? 0 ?>" id="radius_input">
                                <input type="hidden" name="id_lokasi_default" value="<?= $lokasi_presensi['id'] ?? '' ?>">
                                <input type="hidden" name="id_karyawan" value="<?= $karyawan['id_karyawan'] ?? '' ?>">
                                <input type="hidden" name="latitude_pegawai" id="latitude_pegawai_keluar"> 
                                <input type="hidden" name="accuracy_pegawai" id="accuracy_pegawai_keluar">
                                <input type="hidden" name="longitude_pegawai" id="longitude_pegawai_keluar"> 
                                <input type="hidden" id="jam_pulang_kantor_js" value="<?= substr($jam_pulang_kantor ?? '17:00', 0, 5) ?>">

                                <button class="btn btn-danger mt-3" type="submit" id="btn_keluar" disabled>Pulang</button>
                                <p class="text-muted mt-2 small" id="loc_status_keluar">Menunggu GPS...</p>
                            </form>
                        <?php elseif ($sudah_keluar) : ?>
                            <div class="fw-bold h1 my-3"><?= $jam_keluar ?></div>
                            <p class="text-info mt-3 h5">✅ Anda sudah Presensi Keluar</p>
                        <?php else : ?>
                            <div class="parent-clock">
                                <div id="jam-keluar"></div>
                                <div>:</div>
                                <div id="menit-keluar"></div>
                                <div>:</div>
                                <div id="detik-keluar"></div>
                            </div>
                            <p class="text-danger mt-3 h5">Belum Presensi Masuk.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2"></div>
        </div>
    <?php endif; ?> <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header h4 text-center text-primary">
                    Peta Lokasi Presensi
                </div>
                <div class="card-body p-0">
                    <div id="map_absen"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/SVRKdeZ1V+zB7x5f4=" 
     crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20n6a9K1q/6sXjJg7/e38c4p2T7dFaqx86S/e8JgAFA=" 
     crossorigin=""></script>

<script>
    // -------------------
    // Konfigurasi Umum
    // -------------------
    let map = null;
    let markerKantor = null;
    let markerPegawai = null;
    let circleRadius = null;
    const TOLERANCE_HOURS = 0.5; // ± jam toleransi

    document.addEventListener('DOMContentLoaded', function() {
        startClock();
        startGeolocation(); // mulai watchPosition & map
        // Event form
        const formMasuk = document.getElementById('form_masuk');
        const formKeluar = document.getElementById('form_keluar');

        if (formMasuk) formMasuk.addEventListener('submit', validatePresensiMasuk);
        if (formKeluar) formKeluar.addEventListener('submit', validatePresensiKeluar);
    });

    // -------------------
    // UTIL: Haversine (meter)
    // -------------------
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = (x) => x * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    // -------------------
    // UTIL: Validasi Waktu (targetTimeStr format "HH:MM")
    // -------------------
    function isWithinTimeWindow(currentTime, targetTimeStr, toleranceHours = TOLERANCE_HOURS) {
        if (!targetTimeStr) return false;
        const [targetHour, targetMinute] = targetTimeStr.split(':').map(Number);
        const targetDate = new Date(currentTime.getTime());
        targetDate.setHours(targetHour, targetMinute, 0, 0);

        const minTime = new Date(targetDate.getTime());
        minTime.setHours(targetHour);
        const maxTime = new Date(targetDate.getTime());
        maxTime.setHours(targetHour + toleranceHours);

        return currentTime >= minTime && currentTime <= maxTime;
    }

    // -------------------
    // VALIDASI PRESENSI MASUK
    // -------------------
    function validatePresensiMasuk(event) {
        const latPegawai = parseFloat(document.getElementById('latitude_pegawai')?.value ?? 0);
        const lonPegawai = parseFloat(document.getElementById('longitude_pegawai')?.value ?? 0);
        const latKantor = parseFloat(document.getElementById('lat_kantor_masuk')?.value ?? 0);
        const lonKantor = parseFloat(document.getElementById('lon_kantor_masuk')?.value ?? 0);
        const radius = parseFloat(document.getElementById('radius_input')?.value ?? 0);
        const jamMasukKantor = document.getElementById('jam_masuk_kantor_js')?.value ?? '';
        const currentTime = new Date();
        if (latPegawai === 0 && lonPegawai === 0) {
            alert("⚠️ Gagal Mendapatkan Lokasi GPS. Pastikan izin lokasi diberikan dan coba muat ulang halaman.");
            event.preventDefault();
            return false;
        }

        
       if (jamMasukKantor) {
            const [targetHour, targetMinute] = jamMasukKantor.split(':').map(Number);
            const timeKantor = new Date();
            timeKantor.setHours(targetHour, targetMinute +30, 0, 0);

            const timeLimit = new Date(timeKantor.getTime() + (30 * 60000)); 

            if (currentTime > timeLimit) {
                alert(`⏰ Gagal: Batas waktu absen sudah lewat (maksimal 30 menit dari ${jamMasukKantorStr}). Anda dianggap Alpa.`);
                event.preventDefault();
                return false;
            }
            

            // Warning denda (Informasi saja sebelum submit)
            const timeDenda = new Date(timeKantor.getTime() + (10 * 60000));
            if (currentTime > timeDenda) {
                if (!confirm("⚠️ Anda terlambat lebih dari 10 menit. Gaji Anda akan dipotong Rp 75.000 hari ini. Lanjutkan absen?")) {
                    event.preventDefault();
                    return false;
                }
            }
        }

        const distance = calculateDistance(latPegawai, lonPegawai, latKantor, lonKantor);
        if (distance > radius) {
            const distanceM = Math.round(distance);
            alert(`📍 Gagal Presensi: Anda berada ${distanceM} meter dari kantor. Harus berada dalam radius ${radius} meter.`);
            event.preventDefault();
            return false;
        }

        return true;
    }

    // -------------------
    // VALIDASI PRESENSI KELUAR
    // -------------------
    function validatePresensiKeluar(event) {
        const latPegawai = parseFloat(document.getElementById('latitude_pegawai_keluar')?.value ?? 0);
        const lonPegawai = parseFloat(document.getElementById('longitude_pegawai_keluar')?.value ?? 0);
        const latKantor = parseFloat(document.getElementById('lat_kantor_keluar')?.value ?? 0);
        const lonKantor = parseFloat(document.getElementById('lon_kantor_keluar')?.value ?? 0);
        const radius = parseFloat(document.getElementById('radius_input')?.value ?? 0);
        const jamPulangKantor = document.getElementById('jam_pulang_kantor_js')?.value ?? '';
        const currentTime = new Date();

        if (latPegawai === 0 && lonPegawai === 0) {
            alert("⚠️ Gagal Mendapatkan Lokasi GPS. Pastikan izin lokasi diberikan dan coba muat ulang halaman.");
            event.preventDefault();
            return false;
        }

        if (!isWithinTimeWindow(currentTime, jamPulangKantor)) {
            alert(`⏰ Gagal Presensi: Presensi Keluar hanya dapat dilakukan antara jam ${jamPulangKantor} + 30 menit .`);
            event.preventDefault();
            return false;
        }

        const distance = calculateDistance(latPegawai, lonPegawai, latKantor, lonKantor);
        if (distance > radius) {
            const distanceM = Math.round(distance);
            alert(`📍 Gagal Presensi: Anda berada ${distanceM} meter dari kantor. Harus berada dalam radius ${radius} meter.`);
            event.preventDefault();
            return false;
        }

        return true;
    }

    // -------------------
    // Leaflet: initMap dan update
    // -------------------
    function initMap(lat, lon) {
        const latKantor = parseFloat(document.getElementById('lat_kantor_masuk')?.value || document.getElementById('lat_kantor_keluar')?.value || 0);
        const lonKantor = parseFloat(document.getElementById('lon_kantor_masuk')?.value || document.getElementById('lon_kantor_keluar')?.value || 0);
        const radius = parseFloat(document.getElementById('radius_input')?.value ?? 0);

        const DEFAULT_LAT = -6.200000; // Jakarta fallback
        const DEFAULT_LON = 106.816666;

        const centerLat = lat || latKantor || DEFAULT_LAT;
        const centerLon = lon || lonKantor || DEFAULT_LON;

        if (map) map.remove();

        map = L.map('map_absen').setView([centerLat, centerLon], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        if (latKantor && lonKantor) {
            markerKantor = L.marker([latKantor, lonKantor], {
                icon: L.divIcon({className: 'red-dot-icon', html: '<div style="background-color: red; width: 10px; height: 10px; border-radius: 50%;"></div>', iconSize: [10, 10]})
            }).addTo(map)
              .bindPopup("<b>Lokasi Kantor</b>");

            if (circleRadius) map.removeLayer(circleRadius);
            circleRadius = L.circle([latKantor, lonKantor], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.1,
                radius: radius
            }).addTo(map);
        }

        setTimeout(()=>{ if (map) map.invalidateSize(); }, 100);
    }

    function updateMap(lat, lon, accuracy) {
        if (!map) {
            initMap(lat, lon);
            return;
        }

        if (markerPegawai) map.removeLayer(markerPegawai);

        markerPegawai = L.marker([lat, lon]).addTo(map)
            .bindPopup(`<b>Lokasi Anda</b><br>Akurasi: ${Math.round(accuracy)}m`).openPopup();

        map.setView([lat, lon], 17);
        if (map) map.invalidateSize();
    }

    // -------------------
    // Geolocation: start watchPosition
    // -------------------
    function startGeolocation() {
        const statusMasuk = document.getElementById('loc_status_masuk');
        const statusKeluar = document.getElementById('loc_status_keluar');

        if (statusMasuk) statusMasuk.textContent = 'Mencari lokasi...';
        if (statusKeluar) statusKeluar.textContent = 'Mencari lokasi...';

        if (!navigator.geolocation) {
            initMap();
            showGeoError({ code: 99, message: 'Browser tidak mendukung Geolocation.' });
            return;
        }

        navigator.geolocation.watchPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lon = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                // Anti-Fake GPS
                if (accuracy > 0 && accuracy < 1.2) {
                    alert("⚠️ Perangkat manipulasi lokasi terdeteksi! Mohon gunakan lokasi asli.");
                    location.reload();
                    return;
                }

                // Update input accuracy (Masuk & Keluar)
                const inputAccMasuk = document.getElementById('accuracy_pegawai');
                const inputAccKeluar = document.getElementById('accuracy_pegawai_keluar');
                if (inputAccMasuk) inputAccMasuk.value = accuracy;
                if (inputAccKeluar) inputAccKeluar.value = accuracy;

                // Isi hidden input (masuk)
                if (document.getElementById('latitude_pegawai')) document.getElementById('latitude_pegawai').value = lat;
                if (document.getElementById('longitude_pegawai')) document.getElementById('longitude_pegawai').value = lon;
                
                // Isi hidden input (keluar)
                if (document.getElementById('latitude_pegawai_keluar')) document.getElementById('latitude_pegawai_keluar').value = lat;
                if (document.getElementById('longitude_pegawai_keluar')) document.getElementById('longitude_pegawai_keluar').value = lon;

                // Update peta & status
                updateMap(lat, lon, accuracy);
                if (statusMasuk) statusMasuk.textContent = `Lokasi GPS Ditemukan. Akurasi: ${Math.round(accuracy)} meter.`;
                if (statusKeluar) statusKeluar.textContent = `Lokasi GPS Ditemukan. Akurasi: ${Math.round(accuracy)} meter.`;

                // Auto enable/disable tombol
                validateAutoEnable();
            },
            (err) => {
                showGeoError(err);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    function showGeoError(error) {
        let message = "Gagal mendapatkan lokasi.";
        switch (error.code) {
            case error.PERMISSION_DENIED: message = "Izin lokasi DITOLAK. Harap izinkan akses."; break;
            case error.TIMEOUT: message = "Permintaan mendapatkan lokasi kedaluwarsa."; break;
            case error.POSITION_UNAVAILABLE: message = "Informasi lokasi tidak tersedia."; break;
            default: message = error.message || "Gagal mendapatkan lokasi.";
        }

        if (document.getElementById('latitude_pegawai')) document.getElementById('latitude_pegawai').value = 0;
        if (document.getElementById('longitude_pegawai')) document.getElementById('longitude_pegawai').value = 0;
        if (document.getElementById('latitude_pegawai_keluar')) document.getElementById('latitude_pegawai_keluar').value = 0;
        if (document.getElementById('longitude_pegawai_keluar')) document.getElementById('longitude_pegawai_keluar').value = 0;

        if (!map) initMap();

        const statusMasuk = document.getElementById('loc_status_masuk');
        const statusKeluar = document.getElementById('loc_status_keluar');
        if (statusMasuk) statusMasuk.textContent = `⚠️ ${message}`;
        if (statusKeluar) statusKeluar.textContent = `⚠️ ${message}`;

        const btnMasuk = document.getElementById('btn_masuk');
        const btnKeluar = document.getElementById('btn_keluar');
        if (btnMasuk) btnMasuk.disabled = true;
        if (btnKeluar) btnKeluar.disabled = true;
    }

    function validateAutoEnable() {
        const latPegawai = parseFloat(document.getElementById('latitude_pegawai')?.value || document.getElementById('latitude_pegawai_keluar')?.value || 0);
        const lonPegawai = parseFloat(document.getElementById('longitude_pegawai')?.value || document.getElementById('longitude_pegawai_keluar')?.value || 0);
        const latKantor = parseFloat(document.getElementById('lat_kantor_masuk')?.value || document.getElementById('lat_kantor_keluar')?.value || 0);
        const lonKantor = parseFloat(document.getElementById('lon_kantor_masuk')?.value || document.getElementById('lon_kantor_keluar')?.value || 0);
        const radius = parseFloat(document.getElementById('radius_input')?.value ?? 0);

        const distance = calculateDistance(latPegawai, lonPegawai, latKantor, lonKantor);
        
        const btnMasuk = document.getElementById('btn_masuk');
        const btnKeluar = document.getElementById('btn_keluar');

        if (btnMasuk) btnMasuk.disabled = (distance > radius || latPegawai === 0);
        if (btnKeluar) btnKeluar.disabled = (distance > radius || latPegawai === 0);
    }

    function startClock(){
        setInterval(()=>{
            const t = new Date();
            const jam = String(t.getHours()).padStart(2,"0");
            const menit = String(t.getMinutes()).padStart(2,"0");
            const detik = String(t.getSeconds()).padStart(2,"0");

            ["masuk", "keluar"].forEach(type => {
                if (document.getElementById(`jam-${type}`)) document.getElementById(`jam-${type}`).textContent = jam;
                if (document.getElementById(`menit-${type}`)) document.getElementById(`menit-${type}`).textContent = menit;
                if (document.getElementById(`detik-${type}`)) document.getElementById(`detik-${type}`).textContent = detik;
            });

            const jamMasukInput = document.getElementById("jam_masuk_input");
            if (jamMasukInput) jamMasukInput.value = `${jam}:${menit}:${detik}`;
        }, 1000);
    }
</script>
