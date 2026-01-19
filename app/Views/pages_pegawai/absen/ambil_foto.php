<div class="container-fluid">
    <h3>Pengambilan Foto Absen</h3>
    <p id="instruction_text">Silakan pastikan wajah Anda berada di dalam bingkai sebelum menekan tombol "Ambil Foto".</p>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Live Camera</h6>
                </div>
                <div class="card-body text-center">
                    <div id="loading_overlay" style="display: block; margin-bottom: 10px;">
                        <span class="badge badge-info p-2"><i class="fas fa-spinner fa-spin"></i> Memuat Model AI...</span>
                    </div>
                    
                    <div id="my_camera" class="mb-3 mx-auto"></div>
                    
                    <button type="button" class="btn btn-primary" id="take_snapshot_btn" onclick="take_snapshot();" style="display:none;">
                        <i class="fas fa-camera"></i> Ambil Foto
                    </button>
                    <button type="button" class="btn btn-warning" id="reset_camera_btn" onclick="reset_snapshot();" style="display:none;">
                        <i class="fas fa-undo"></i> Ambil Ulang Foto
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Foto & Verifikasi</h6>
                </div>
                <div class="card-body text-center">
                    <div id="my_result" class="mb-3">Foto hasil pengambilan akan muncul di sini.</div>
                    
                    <form method="POST" action="<?= base_url('pegawai/absen/simpan_foto') ?>" id="photo_form">
                        <input type="hidden" name="id_karyawan" value="<?= $id_karyawan ?? '' ?>">
                        <input type="hidden" name="tanggal_masuk" value="<?= $tanggal_masuk ?? '' ?>">
                        <input type="hidden" name="jam_masuk" value="<?= $jam_masuk ?? '' ?>">
                        <input type="hidden" name="id_lokasi" value="<?= $id_lokasi ?? '' ?>"> 
                        <input type="hidden" name="latitude_pegawai" value="<?= $latitude_pegawai ?? '' ?>">
                        <input type="hidden" name="longitude_pegawai" value="<?= $longitude_pegawai ?? '' ?>">
                        <input type="hidden" name="meter" value="<?= $meter ?? '' ?>">
                        <input type="hidden" name="image" class="image-tag">
                        
                        <button type="submit" class="btn btn-success" id="save_photo_btn" style="display:none;">
                            <i class="fas fa-save"></i> Wajah Cocok, Simpan Absen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="<?= base_url('js/face-api.min.js') ?>"></script>

<script>
    let faceMatcher = null;

    // 1. Inisialisasi & Load Model AI
    window.onload = async function() {
        initWebcam();

        try {
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri('<?= base_url('models') ?>'),
                faceapi.nets.faceLandmark68Net.loadFromUri('<?= base_url('models') ?>'),
                faceapi.nets.faceRecognitionNet.loadFromUri('<?= base_url('models') ?>')
            ]);

            const savedDescriptorJson = '<?= $karyawan['face_descriptor'] ?? "[]" ?>';
            const savedDescriptor = JSON.parse(savedDescriptorJson);

            if (savedDescriptor && savedDescriptor.length > 0) {
                const floatArray = new Float32Array(savedDescriptor);
                const labeledDescriptor = new faceapi.LabeledFaceDescriptors(
                    '<?= $karyawan['nm_karyawan'] ?>', 
                    [floatArray]
                );
                // Threshold 0.4 agar verifikasi lebih ketat
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptor, 0.6); 
            }

            document.getElementById('loading_overlay').style.display = 'none';
            document.getElementById('take_snapshot_btn').style.display = 'inline-block';
        } catch (error) {
            console.error(error);
            alert("Sistem AI gagal dimuat. Pastikan folder 'models' tersedia di public/.");
        }
    };

    function initWebcam() {
        Webcam.set({
            width: 320,
            height: 240,
            dest_width: 320, 
            dest_height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90,
        });
        Webcam.attach('#my_camera');
    }

    // 2. Fungsi Mengambil Snapshot & Verifikasi
    async function take_snapshot() {
        if (typeof Webcam !== 'undefined' && Webcam.loaded) {
            Webcam.snap(async function(data_uri) {
                document.getElementById('my_result').innerHTML = '<div class="text-info"><i class="fas fa-spinner fa-spin"></i> Memverifikasi Identitas...</div>';
                
                const img = await faceapi.fetchImage(data_uri);
                const detection = await faceapi.detectSingleFace(img)
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();

                if (!detection) {
                    alert("Wajah tidak terdeteksi! Pastikan wajah terlihat jelas di depan kamera.");
                    reset_snapshot();
                    return;
                }

                if (!faceMatcher) {
                    document.getElementById('my_result').innerHTML = '<div class="alert alert-warning">Data wajah Anda belum disinkronisasi. Hubungi Admin.</div>';
                    document.getElementById('reset_camera_btn').style.display = 'inline-block';
                    document.getElementById('take_snapshot_btn').style.display = 'none';
                    return;
                }

                const match = faceMatcher.findBestMatch(detection.descriptor);
                
                if (match.label === 'unknown') {
                    document.getElementById('my_result').innerHTML = '<div class="alert alert-danger">Verifikasi Gagal: Wajah tidak cocok!</div>';
                    document.getElementById('reset_camera_btn').style.display = 'inline-block';
                    document.getElementById('take_snapshot_btn').style.display = 'none';
                    return;
                }

                document.getElementById('my_result').innerHTML = 
                    '<div class="alert alert-success">Verifikasi Berhasil! Halo, ' + match.label + '</div>' + 
                    '<img src="'+data_uri+'" class="img-thumbnail"/>';

                document.querySelector('.image-tag').value = data_uri;
                document.getElementById('save_photo_btn').style.display = 'inline-block';
                document.getElementById('take_snapshot_btn').style.display = 'none';
                document.getElementById('reset_camera_btn').style.display = 'inline-block';
            });
        } else {
             alert('Kamera belum siap.');
        }
    }
    
    // 3. Fungsi Reset (Foto Ulang tanpa Logout)
    function reset_snapshot() {
        // Membersihkan UI
        document.getElementById('my_result').innerHTML = 'Foto hasil pengambilan akan muncul di sini.';
        document.querySelector('.image-tag').value = '';
        document.getElementById('save_photo_btn').style.display = 'none';
        document.getElementById('reset_camera_btn').style.display = 'none';
        document.getElementById('take_snapshot_btn').style.display = 'inline-block';
        
        // Melepas dan memasang ulang kamera agar stream segar kembali
        Webcam.unattach('#my_camera');
        initWebcam();
    }
</script>