<?php 
session_start();
require "../inventaris/functions/functions.php";

$status = ""; // Variabel untuk status notifikasi

if (isset($_POST['submit'])) {
    if (login($_POST)) { 
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];
        $status = "success"; // Login berhasil
    } else {
        $status = "error"; // Login gagal
    }
}
?>
<!DOCTYPE html>
<html lang="en">
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

    <?php if ($status == "success"): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Login Berhasil',
            text: 'Selamat datang <?= htmlspecialchars($_SESSION['username']) ?>',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "../inventaris/views/dashboard.php";
        });
    </script>
    <?php elseif ($status == "error"): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: 'Username atau password salah!',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    <?php endif; ?>
</body>
</html>
