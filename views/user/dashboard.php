<?php
session_start();
require "../../functions/functions.php";

// Pastikan user login dan memiliki role ID 5 (User)
if (!isset($_SESSION['logged_in']) || $_SESSION['id_roles'] != 5) {
    header("Location: ../../index.php");
    exit;
}

// Log out handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout(); // Panggil function logout()
    exit;
}

$userId = $_SESSION['id']; // Ambil ID user yang login

// Ambil data untuk card (Asumsi functions.php sudah mendefinisikan koneksi $conn)
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
    LIMIT 1
");


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard User | Mobile App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="../../assets/css/dashboard_user.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex flex-column min-vh-100">

<header class="app-header d-flex justify-content-between align-items-center px-3">
    <div class="d-flex align-items-center gap-2">
        <img src="../../assets/images/logo-maxi.jpg" alt="Logo Maxi" class="app-logo">
        <h1 class="h5 m-0 text-white fw-semibold">Dashboard User</h1>
    </div>
    <form id="logoutForm" method="POST" class="d-inline m-0">
        <input type="hidden" name="logout" value="1">
        <button type="button" id="logoutBtn" class="btn btn-outline-light btn-sm border-0" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>
</header>

<div class="app-content">
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <a href="request.php" class="btn btn-success">+ Request Kendaraan</a>
            </div>

            <div class="row row-cols-1 g-3 mb-4">
                <div class="col">
                    <a href="pending.php" class="text-decoration-none">
                        <div class="card stats-card card-request text-center">
                            <div class="card-body">
                                <i class="fas fa-file-alt"></i>
                                <h3><?= htmlspecialchars($totalRequest) ?></h3>
                                <p>Total Request</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="view_data.php?view=aktif" class="text-decoration-none">
                        <div class="card stats-card card-pemakaian text-center">
                            <div class="card-body">
                                <i class="fas fa-car-side"></i>
                                <h3><?= htmlspecialchars($totalPemakaian) ?></h3>
                                <p>Pemakaian Aktif</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="view_data.php?view=history" class="text-decoration-none">
                        <div class="card stats-card card-history text-center">
                            <div class="card-body">
                                <i class="fas fa-clock-rotate-left"></i>
                                <h3><?= htmlspecialchars($totalSelesai) ?></h3>
                                <p>History Pemakaian</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="card history-card mt-4 mb-5"> <div class="card-header">History Pemakaian Terbaru</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless m-0">
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
                                        <?php while ($row = mysqli_fetch_assoc($history)): 
                                            // Tentukan kelas status badge
                                            // ... (Logika status di sini) ...
                                        ?>
                                            <tr>
                                                <td data-label="Tgl. Keluar:"><span><?= htmlspecialchars($row['tanggal_keluar']) ?></span></td>
                                                <td data-label="Tgl. Masuk:"><span><?= htmlspecialchars($row['tanggal_masuk']) ?></span></td>
                                                <td data-label="Plat Nomor:"><span><?= htmlspecialchars($row['plat_nomor']) ?></span></td>
                                                <td data-label="Status:">
                                                    <span class="status-badge <?= $statusClass ?>">
                                                        <?= htmlspecialchars($row['nama_status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr><td colspan="4" class="text-center p-4">Belum ada history pemakaian.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "navbar.php"?>
</body>
</html>