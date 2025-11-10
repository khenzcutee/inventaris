<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3, 4])) {
    header("Location: ../../index.php");
    exit;
}

$status = null; // Untuk alert SweetAlert

// Handle proses pengembalian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pemakaian'])) {
    $id_pemakaian = (int)$_POST['id_pemakaian'];
    $tanggal_masuk = mysqli_real_escape_string($conn, $_POST['tanggal_masuk']);
    $kilometer_akhir = (int)$_POST['kilometer_akhir'];

    if (prosesPengembalian($id_pemakaian, $tanggal_masuk, $kilometer_akhir)) {
        $status = ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Pengembalian berhasil diproses!'];
    } else {
        $status = ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Pengembalian gagal dilakukan!'];
    }
}

// Logout (opsional)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout();
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengembalian Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<?php include "navbar.php"; ?>

<!-- Main Content -->
<div class="container-fluid p-4">
    <h2 class="mb-4 text-primary"><i class="bi bi-arrow-repeat"></i> Form Pengembalian Kendaraan</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <span>Pengembalian Kendaraan</span>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Peminjaman</label>
                    <select name="id_pemakaian" id="id_pemakaian" class="form-control" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        <?= getPemakaianSedangDipakaiOptions(); ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kilometer Awal</label>
                    <input type="number" id="kilometer_awal" name="kilometer_awal" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kilometer Akhir</label>
                    <input type="number" name="kilometer_akhir" class="form-control" required>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">✔ Proses Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script SweetAlert -->
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

<!-- Script JS untuk update KM Awal otomatis -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectPemakaian = document.getElementById("id_pemakaian");
    const kmAwalInput = document.getElementById("kilometer_awal");

    selectPemakaian.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const kmAwal = selectedOption.getAttribute("data-km");
        kmAwalInput.value = kmAwal ? kmAwal : '';
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
