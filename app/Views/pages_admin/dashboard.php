<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title ?></h1>
                </div>
                <div class="col-sm-6 text-right">
                    <span class="badge badge-primary p-2" id="date"></span>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $karyawan ?></h3>
                            <p>Total Karyawan</p>
                        </div>
                        <i class="small-box-icon fas fa-user"></i>
                        <a href="karyawan" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $permohonan_pinjaman ?? 0 ?></h3> 
                            <p>Permohonan Pinjaman</p>
                        </div>
                        <i class="small-box-icon fas fa-hand-holding-usd"></i> 
                        <a href="pinjaman" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= $jabatan ?></h3>
                            <p>Jumlah Jabatan</p>
                        </div>
                        <i class="small-box-icon fas fa-briefcase"></i>
                        <a href="jabatan" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $ketidakhadiran_hari_ini ?></h3>
                            <p>Permohonan Izin Pending</p>
                        </div>
                        <i class="small-box-icon fas fa-envelope-open-text"></i> <a href="ketidakhadiran" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Karyawan Berdasarkan Jenis Kelamin</h3>
                        </div>
                        <div class="card-body">
                            <div id="apexBarChart" style="height:300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Status Kehadiran Hari Ini</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <canvas id="myPieChart" style="height:250px; width:250px;"></canvas>
                            </div>
                            <div class="mt-3 text-center small">
                                <span class="mr-2"><i class="fas fa-circle text-success"></i> Hadir (<?= $hadir ?? 0 ?>)</span>
                                <span class="mr-2"><i class="fas fa-circle text-danger"></i> Belum Hadir (<?= $belum_hadir ?? 0 ?>)</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
</div>

<script>
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const date = new Date();
    document.getElementById("date").innerHTML = "Tanggal: " + date.getDate() + " " + months[date.getMonth()] + " " + date.getFullYear();
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    
    const hadir = <?= isset($hadir) ? (int)$hadir : 0 ?>;
    const belumHadir = <?= isset($belum_hadir) ? (int)$belum_hadir : 0 ?>;
    const priaCount = <?= $pria ?? 0 ?>;
    const wanitaCount = <?= $wanita ?? 0 ?>;

    // Tentukan nilai maksimum data. Minimal 5 agar skala tidak terlalu rapat.
    const maxKaryawan = Math.max(priaCount, wanitaCount, 5); 

    // --- 1. APEXCHARTS BAR CHART (Dengan Warna yang Didistribusikan) ---
    
    // Definisikan warna untuk setiap kategori: Laki-Laki (biru) dan Perempuan (merah)
    const barColors = ['#007bff', '#dc3545']; 

    const apexBarOptions = {
        series: [{
            name: 'Karyawan',
            data: [priaCount, wanitaCount]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
                // Kunci agar warna terdistribusi per bar:
                distributed: true 
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Laki-Laki', 'Perempuan'],
        },
        yaxis: {
            title: {
                text: 'Jumlah'
            },
            min: 0,
            tickAmount: maxKaryawan // Mengatur jumlah tick
        },
        fill: {
            opacity: 1,
            colors: barColors // Warna diterapkan di sini
        },
        legend: {
            show: false
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Orang"
                }
            }
        }
    };

    // Inisialisasi dan render ApexCharts
    const chart = new ApexCharts(document.querySelector("#apexBarChart"), apexBarOptions);
    chart.render();


    // --- 2. CHART.JS PIE CHART (Tetap sama) ---
    new Chart(document.getElementById("myPieChart"), {
        type: 'doughnut',
        data: {
            labels: ["Hadir", "Belum Hadir"],
            datasets: [{
                data: [
                    hadir, 
                    belumHadir 
                ],
                backgroundColor: ['#28a745', '#dc3545']
            }]
        },
        options: {responsive: false, maintainAspectRatio: false}
    });

});
</script>