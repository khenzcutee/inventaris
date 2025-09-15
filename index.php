<?php
session_start();
require "../inventaris/functions/functions.php";

$redirectPage = "";

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (login($_POST)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];

        // Tentukan halaman redirect
        if (in_array($_SESSION['id_roles'], [3, 4])) {
            $redirectPage = "../inventaris/views/admin/dashboard.php";
        } elseif ($_SESSION['id_roles'] == 5) {
            $redirectPage = "../inventaris/views/user/dashboard.php";
        } else {
            $redirectPage = "../inventaris/index.php";
        }

        // Simpan status ke session
        $_SESSION['flash_login'] = [
            'status' => 'success',
            'username' => $_SESSION['username'],
            'redirect' => $redirectPage
        ];

        // Redirect agar SweetAlert muncul di reload
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['flash_login'] = [
            'status' => 'error'
        ];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Ambil flash login jika ada
$flash = $_SESSION['flash_login'] ?? null;
unset($_SESSION['flash_login']); // Hapus setelah ditampilkan

// Logout success alert
$logout_success = isset($_SESSION['logout_success']) && $_SESSION['logout_success'] === true;
unset($_SESSION['logout_success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Inventaris</title>
    <link rel="stylesheet" href="../inventaris/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">
        <div class="title-text">
            <div class="logo-login"><img src="../inventaris/assets/images/logo-maxi.jpg" alt=""></div>
            <div class="title">Login Inventaris Dashboard</div>
        </div>
        <div class="form-container">
            <form action="" method="post" class="login">
                <div class="field">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="field">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="pass-link"><a href="#">Forgot password?</a></div>
                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" name="submit" value="Login">
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert -->
    <script>
    <?php if ($flash): ?>
        <?php if ($flash['status'] === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil',
                text: 'Selamat datang <?= htmlspecialchars($flash['username']) ?>',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "<?= htmlspecialchars($flash['redirect']) ?>";
            });
        <?php elseif ($flash['status'] === 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: 'Username atau password salah!',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($logout_success): ?>
        Swal.fire({
            title: 'Logout Berhasil!',
            text: 'Anda telah keluar dari sistem.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
    </script>
</body>
</html>
