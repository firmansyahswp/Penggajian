<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="<?= base_url('pegawai/dashboard') ?>" class="brand-link">
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
          <a href="<?= base_url('pegawai/dashboard') ?>" class="nav-link active">
            <i class="nav-icon fas fa-fw fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-fw fa-solid fa-user-clock"></i>
            <p>
              Absensi
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= base_url('pegawai/absen') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Absen Sekarang</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('pegawai/data_absen') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Data Absen Saya</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('pegawai/ketidakhadiran') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Ketidakhadiran/Cuti</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-fw fa-receipt"></i>
            <p>
              Keuangan & Gaji
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= base_url('pegawai/gaji') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Riwayat Slip Gaji</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('pegawai/pinjaman/status') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle-fill"></i>
                <p>Permohonan Pinjaman</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('pegawai/ubah-password') ?>">
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