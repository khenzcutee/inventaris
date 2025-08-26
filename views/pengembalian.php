<?php
session_start();
require "../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3,4])) {
    header("Location: ../index.php");
    exit;
}

$status = null; // Untuk menentukan status alert

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemakaian = (int)$_POST['id_pemakaian'];
    $tanggal_masuk = mysqli_real_escape_string($conn, $_POST['tanggal_masuk']);

    if (prosesPengembalian($id_pemakaian, $tanggal_masuk)) {
        $status = ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Pengembalian berhasil diproses!'];
    } else {
        $status = ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Pengembalian gagal dilakukan!'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengembalian Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<?php include "navbar.php"; ?>
<!-- Sidebar -->
<?php include "sidebar.php"; ?>

<!-- Main Content -->
<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">🔄 Form Pengembalian Kendaraan</h2>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Pengembalian Kendaraan</span>
            <a href="view_Data.php?type=pemakaian" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Peminjaman</label>
                    <select name="id_pemakaian" class="form-control" required>
                        <?= getPemakaianSedangDipakaiOptions(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" required>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">✔ Proses Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
<?php if ($status): ?>
Swal.fire({
    icon: '<?= $status['icon'] ?>',
    title: '<?= $status['title'] ?>',
    text: '<?= $status['text'] ?>'
}).then(() => {
    window.location.href = 'pengembalian.php';
});
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
