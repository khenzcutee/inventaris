<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3,4,5])) {
    header("Location: ../index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout(); // Panggil function logout() yang sudah kamu punya
    exit;
}
$user_id = $_SESSION['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kendaraan = (int)$_POST['id_kendaraan'];

    $result = requestKendaraan($user_id, $id_kendaraan);
    if ($result === true) {
        $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>'Request berhasil dikirim!'];
    } elseif ($result === 'duplicate') {
        $_SESSION['flash'] = ['icon'=>'warning','title'=>'Sudah Ada','text'=>'Anda sudah mengirim request untuk kendaraan ini!'];
    } else {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Error','text'=>'Terjadi kesalahan!'];
    }
    header("Location: request.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Request Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include "navbar.php"; ?>
<?php include "sidebar.php"; ?>
<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">🚗 Request Kendaraan</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

    <!-- Flash Message SweetAlert -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: '<?= $_SESSION['flash']['icon'] ?>',
                title: '<?= $_SESSION['flash']['title'] ?>',
                text: '<?= $_SESSION['flash']['text'] ?>',
                confirmButtonColor: '#3085d6'
            });
        });
        </script>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Form Request Kendaraan</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_kendaraan" class="form-label">Pilih Kendaraan</label>
                    <select name="id_kendaraan" id="id_kendaraan" class="form-select" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        <?= getAllKendaraanOptions(); ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Kirim Request</button>
            </form>
        </div>
    </div>
</div>

<?php include "script.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
