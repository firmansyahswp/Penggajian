<!doctype html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>APP PENGGAJIAN | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media='all'"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <link href="<?php echo base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="<?php echo base_url('assets1/css/adminlte.css') ?>" />
    <link rel="preload" href="<?php echo base_url('assets1/css/adminlte.css') ?>" as="style" />
    </head>

  <body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <h4 class="font-weight-bold my-0 py-2">CV. GEMILANG SUKSES MANDIRI</h4>
            </li>
          </ul>

          <ul class="navbar-nav ms-auto">

            <li class="nav-item dropdown">
              <button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center"
                id="bd-theme"
                type="button"
                aria-expanded="false"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-label="Toggle theme (auto)">
                <i class="bi bi-sun-fill theme-icon-active" style="font-size: 1.25rem;"></i>
                <span class="d-lg-none ms-2" id="bd-theme-text">Toggle theme</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
                <li>
                  <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                    <i class="bi bi-sun-fill me-2 opacity-50"></i>
                    Light
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                    <i class="bi bi-moon-stars-fill me-2 opacity-50"></i>
                    Dark
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto">
                    <i class="bi bi-circle-half me-2 opacity-50"></i>
                    Auto
                  </button>
                </li>
              </ul>
            </li>
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <span class="d-none d-md-inline">Selamat Datang</span>
                <img
                  src="<?php echo base_url('assets/img/user2-160x160.jpg') ?>"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                />
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <li class="user-footer">
                  <a href="charts.html" class="btn btn-default btn-flat">Ubah Password</a>
                  <a href="tables.html" class="btn btn-default btn-flat float-end">Logout</a>
                </li>
              </ul>
            </li>

          </ul>
        </div>
      </nav>
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
          <a href="<?php echo base_url('admin/dashboard') ?>" class="brand-link">
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
                <a href="<?php echo base_url('admin/dashboard') ?>" class="nav-link active">
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
                    <a href="<?php echo base_url('admin/data_pegawai') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle-fill"></i>
                      <p>Data Pegawai</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url('admin/data_jabatan') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle-fill"></i>
                      <p>Data Jabatan</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-fw fa-money-check-alt"></i>
                  <p>
                    Transaksi
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo base_url('admin/data_absensi') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle-fill"></i>
                      <p>Data Absensi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url('admin/data_gaji') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle-fill"></i>
                      <p>Data Gaji</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-fw fa-receipt"></i>
                  <p>
                    Laporan
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo base_url('admin/laporan_gaji') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle-fill"></i>
                      <p>Laporan Gaji</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url('admin/laporan_absensi') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle-fill"></i>
                      <p>Laporan Absensi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url('admin/slip_gaji') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle-fill"></i>
                      <p>Slip Gaji</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="charts.html">
                  <i class="nav-icon fas fa-fw fa-lock"></i>
                  <p>Ubah Password</p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                  <i class="nav-icon fas fa-fw fa-sign-out-alt"></i>
                  <p>Logout</p>
                </a>
              </li>

            </ul>
          </nav>
        </div>
        </aside>
      <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        
        <div class="app-content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Area Konten Utama Anda</h3>
                  </div>
                  <div class="card-body">
                    Di sinilah Anda akan menempatkan semua elemen seperti Data Pegawai, Data Jabatan, dll.
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </main>
      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <strong>
          Copyright &copy; 2014-2025&nbsp;
          <a href="#" class="text-decoration-none">CV. GEMILANG SUKSES MANDIRI</a>
        </strong>
        All rights reserved.
      </footer>
      </div>
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                  <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
              <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                  <a class="btn btn-primary" href="login.html">Logout</a>
              </div>
          </div>
      </div>
    </div>


    <script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
    
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="<?php echo base_url('assets1/js/adminlte.js') ?>"></script>
    
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    
    <script>
        // ... (Script Dark Mode Toggle seperti sebelumnya)
        (() => {
            'use strict'

            const getStoredTheme = () => localStorage.getItem('theme')
            const setStoredTheme = theme => localStorage.setItem('theme', theme)

            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme()
                if (storedTheme) {
                    return storedTheme
                }

                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            }

            const setTheme = theme => {
                if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark')
                    document.querySelector('body').setAttribute('data-bs-theme', 'dark')
                    document.querySelector('.app-sidebar').setAttribute('data-bs-theme', 'dark')
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme)
                    document.querySelector('body').setAttribute('data-bs-theme', theme)
                    document.querySelector('.app-sidebar').setAttribute('data-bs-theme', 'dark') // Sidebar tetap gelap
                }
            }

            setTheme(getPreferredTheme())

            const showActiveTheme = (theme, focus = false) => {
                const themeSwitcher = document.querySelector('#bd-theme')
                if (!themeSwitcher) {
                    return
                }

                const themeSwitcherText = document.querySelector('#bd-theme-text')
                const activeThemeIcon = document.querySelector('.theme-icon-active')
                const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
                const iconOfActiveBtn = btnToActive.querySelector('i').className.replace('me-2 opacity-50', '')

                document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
                    element.classList.remove('active')
                    element.setAttribute('aria-pressed', 'false')
                })

                btnToActive.classList.add('active')
                btnToActive.setAttribute('aria-pressed', 'true')
                activeThemeIcon.className = `${iconOfActiveBtn} theme-icon-active`
                const themeSwitcherLabel = `${theme.charAt(0).toUpperCase() + theme.slice(1)}`
                themeSwitcherText.textContent = themeSwitcherLabel

                if (focus) {
                    themeSwitcher.focus()
                }
            }

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const storedTheme = getStoredTheme()
                if (storedTheme !== 'light' && storedTheme !== 'dark') {
                    setTheme(getPreferredTheme())
                }
            })

            window.addEventListener('DOMContentLoaded', () => {
                showActiveTheme(getPreferredTheme())

                document.querySelectorAll('[data-bs-theme-value]')
                    .forEach(toggle => {
                        toggle.addEventListener('click', () => {
                            const theme = toggle.getAttribute('data-bs-theme-value')
                            setStoredTheme(theme)
                            setTheme(theme)
                            showActiveTheme(theme, true)
                        })
                    })
            })
        })()
    </script>
    </body>
</html>