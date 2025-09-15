<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['id_roles'] != 5) {
    header("Location: ../index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout(); // Panggil function logout() yang sudah kamu punya
    exit;
}

$userId = $_SESSION['id']; // Ambil ID user yang login

// Ambil data untuk card
$totalRequest = getCountRequestUser('request');
$totalPemakaian = getCountPemakaianUser("pemakaian");
$totalSelesai = getCountHistoryUser("pemakaian");

// Ambil 5 history terakhir
$history = mysqli_query($conn, "
    SELECT p.tanggal_keluar, p.tanggal_masuk, k.plat_nomor, s.nama_status
    FROM pemakaian p
    JOIN kendaraan k ON p.id_inventaris = k.id
    JOIN status s ON p.id_status = s.id
    WHERE p.id_user = $userId
    ORDER BY p.id DESC
    LIMIT 5
");


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include "navbar.php"; ?>
<?php include "sidebar.php"; ?>

<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">📊 Dashboard User</h2>

    <!-- Tombol Request Kendaraan -->
    <div class="mb-3">
        <a href="request.php" class="btn btn-success">+ Request Kendaraan</a>
    </div>

    <!-- Card Statistik -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Request</h5>
                    <p class="card-text fs-3"><?= $totalRequest ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pemakaian Aktif</h5>
                    <p class="card-text fs-3"><?= $totalPemakaian ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Pemakaian</h5>
                    <p class="card-text fs-3"><?= $totalSelesai ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel History -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">History Pemakaian Terbaru</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal Keluar</th>
                            <th>Tanggal Masuk</th>
                            <th>Plat Nomor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($history) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($history)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['tanggal_keluar']) ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_masuk']) ?></td>
                                    <td><?= htmlspecialchars($row['plat_nomor']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_status']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">Belum ada history</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "script.php"; ?>
</body>
</html>
