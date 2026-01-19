<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>
    <div class="card shadow mb-4">
        <div class="card-body text-center">
            <p>Fitur ini akan mengekstrak data wajah dari foto profil karyawan yang sudah diunggah.</p>
            <button id="startSync" class="btn btn-primary btn-lg">
                <i class="fas fa-sync"></i> Mulai Proses Sinkronisasi
            </button>
            <div id="progressArea" class="mt-4" style="display:none;">
                <div class="progress mb-2">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <small id="statusText" class="text-muted">Memuat Model AI...</small>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/face-api.min.js') ?>"></script>

<script>
    const btnSync = document.getElementById('startSync');
    const progressArea = document.getElementById('progressArea');
    const progressBar = document.getElementById('progressBar');
    const statusText = document.getElementById('statusText');

    const karyawanData = <?= json_encode($karyawan) ?>;
    console.log("Data Karyawan:", karyawanData);

    btnSync.addEventListener('click', async () => {
        console.log("Tombol ditekan!");
        btnSync.disabled = true;
        progressArea.style.display = 'block';

        try {
            // 1. Load Model
            statusText.innerText = "Memuat Model AI...";
            await faceapi.nets.ssdMobilenetv1.loadFromUri('<?= base_url('models') ?>');
            await faceapi.nets.faceLandmark68Net.loadFromUri('<?= base_url('models') ?>');
            await faceapi.nets.faceRecognitionNet.loadFromUri('<?= base_url('models') ?>');

            let sukses = 0;
            for (let i = 0; i < karyawanData.length; i++) {
                const k = karyawanData[i];
                const persen = Math.round(((i + 1) / karyawanData.length) * 100);
                
                progressBar.style.width = persen + '%';
                statusText.innerText = `Memproses (${i+1}/${karyawanData.length}): ${k.nm_karyawan}`;

                if (!k.foto || k.foto === '') continue;

                try {
                    // 2. Deteksi Wajah dari Foto Profil
                    const imgUrl = '<?= base_url('foto/') ?>' + k.foto;
                    const img = await faceapi.fetchImage(imgUrl);
                    const detection = await faceapi.detectSingleFace(img)
                                             .withFaceLandmarks()
                                             .withFaceDescriptor();

                    if (detection) {
                        // 3. Simpan ke Database via AJAX
                        const descriptor = JSON.stringify(Array.from(detection.descriptor));
                        await fetch('<?= base_url('admin/datakaryawan/update_face_descriptor') ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                id_karyawan: k.id_karyawan,
                                face_descriptor: descriptor
                            })
                        });
                        sukses++;
                       console.log("✅ Berhasil: " + k.nm_karyawan);
                } else {
                    // TAMPILKAN INI DI CONSOLE
                    console.error("❌ Wajah TIDAK ditemukan pada: " + k.nm_karyawan + " (URL: " + imgUrl + ")");
                } 
                    
                } catch (err) {
                    console.error("Gagal memproses " + k.nm_karyawan, err);
                }
            }

            statusText.innerHTML = `<span class="text-success font-weight-bold">Selesai! ${sukses} wajah berhasil disinkronisasi.</span>`;
        } catch (error) {
            statusText.innerHTML = `<span class="text-danger">Error: ${error.message}</span>`;
        } finally {
            btnSync.disabled = false;
        }
    });
</script>