<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="<?= base_url('admin/dashboard') ?>" class="brand-link">
      <i class="brand-image fas fa-money-bill opacity-75 shadow me-2"></i>
      <span class="brand-text fw-light">APP PENGGAJIAN</span>
    </a>
  </div>
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul
        class="nav sidebar-menu flex-column"
        data-lte-toggle="treeview"
        role="navigation"
        data-accordion="false"
        id="navigation"
      >
        <li class="nav-item">
          <a href="<?= base_url('admin/dashboard') ?>" class="nav-link active">
            <i class="nav-icon fas fa-fw fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-solid fa-fw fa-database"></i>
            <p>
              Master Data
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= base_url('admin/karyawan') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Data Pegawai</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('admin/jabatan') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Data Jabatan</p>
              </a>
            </li>
             <li class="nav-item">
              <a href="<?= base_url('admin/lokasipresensi') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Lokasi Presensi</p>
              </a>
            </li>
            </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-fw fa-money-check-alt"></i>
            <p>
              Laporan Absensi
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= base_url('admin/rekap-harian') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Data Absensi Harian</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('admin/rekap-mingguan') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Data Absensi Mingguan</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('admin/rekap-bulanan') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Data Absensi Bulanan</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('admin/ketidakhadiran') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Ketidakhadiran</p>
              </a>
            </li>
          </ul>
        </li>
        
        <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-fw fa-hand-holding-usd"></i>
              <p>
                Manajemen Pinjaman
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url('admin/pinjaman') ?>" class="nav-link">
                    <i class="nav-icon bi bi-circle-fill"></i>
                    <p>Persetujuan Permohonan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url('admin/angsuran') ?>" class="nav-link">
                    <i class="nav-icon bi bi-circle-fill"></i>
                    <p>Angsuran & Saldo Aktif</p>
                  </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-fw fa-calculator"></i>
              <p>
                Proses Penggajian
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= base_url('admin/lembur') ?>" class="nav-link">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>Input Uang Lembur</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/potongan') ?>" class="nav-link">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>Input Potongan Lain</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/gaji') ?>" class="nav-link">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>Hitung Gaji</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/gaji/rekap') ?>" class="nav-link">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>Rekap Gaji & Slip</p>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('admin/ubah-password') ?>">
            <i class="nav-icon fas fa-fw fa-lock"></i>
            <p>Ubah Password</p>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('logout') ?>" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="nav-icon fas fa-fw fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
<main class="app-main">