<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Aplikasi Penggajian CV.Gemilang Sukses Mandiri">
    <meta name="author" content="Your Name">

    <title>Login - CV.GEMILANG SUKSES MANDIRI</title>

    <link href="<?php echo base_url() ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo base_url() ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url() ?>/assets/vendor/login.css" rel="stylesheet" type="text/css">

    <style>
        
    </style>
</head>

<body>
    <div class="login-container">
        <img src="https://via.placeholder.com/120x40/00FFFF/0F2027?text=LOGO+ANDA" alt="" class="logo">
        <h1>APLIKASI PENGGAJIAN <br>CV.GEMILANG SUKSES MANDIRI</h1>
        
        <?php if(!empty(session()->getFlashdata('pesan'))) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('pesan')?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif ?>

        

        <form method="POST" action="<?php echo base_url('login') ?>" class="user">
            <div class="form-group">
                <i class="fas fa-user fa-icon"></i>
                <input type="text" class="<?= ($validation->hasError('username')) ? 'is-invalid' : '' ?> form-control form-control-user"
                    id="username" placeholder="Username" name="username" value="<?= old('username') ?>">
                <div class="invalid-feedback">
                    <?= $validation->getError('username') ?>
                </div>
            </div>
            <div class="form-group">
                <i class="fas fa-lock fa-icon"></i>
                <input type="password" class="<?= ($validation->hasError('password')) ? 'is-invalid' : '' ?> form-control form-control-user"
                    id="password" placeholder="Password" name="password">
                <div class="invalid-feedback">
                    <?= $validation->getError('password') ?>
                </div>
            </div>
            <button type="submit" class="btn btn-block btn-login">
                Login
            </button>
            <div>
            </div>
            <button type="button" id="themeToggle" class="toggle-theme">
            🌙 Dark Mode
            </button>

        </form>

    </div>

    <script src="<?php echo base_url() ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="<?php echo base_url() ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="<?php echo base_url() ?>/assets/js/sb-admin-2.min.js"></script>
    <script>
        const toggle = document.getElementById('themeToggle');

        // Cek jika user pernah aktifkan dark mode
        if(localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
            toggle.innerHTML = "☀ Light Mode";
        }
        toggle.innerHTML = document.body.classList.contains("dark-mode") 
        ? "☀ Light Mode" : "🌙 Dark Mode";

        if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
        document.body.classList.add("dark-mode");
        }

        toggle.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");

            // Simpan preferensi user
            if(document.body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                toggle.innerHTML = "☀ Light Mode";
            } else {
                localStorage.setItem("theme", "light");
                toggle.innerHTML = "🌙 Dark Mode";
            }
        });
    </script>

</body>

</html>