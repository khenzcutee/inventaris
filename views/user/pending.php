<?php
session_start();
require "../../functions/functions.php";
$basePath = dirname(__DIR__, 2); // naik 2 folder dari lokasi file
$baseUrl  = "/inventaris/assets/images/kendaraan/";

if (!isset($_SESSION['logged_in']) || $_SESSION['id_roles'] != 5) {
    header("Location: ../../index.php");
    exit;
}

// Logout handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout();
    exit;
}

$userId   = (int)$_SESSION['id'];
$namaUser = $_SESSION['nama'] ?? $_SESSION['username'];

$search  = trim($_GET['search'] ?? '');
$pageNum = max(1, (int)($_GET['page'] ?? 1));
$limit   = 10;
$offset  = ($pageNum - 1) * $limit;

// Ambil data request pending user
$dataRequest = getKendaraanRequestPendingUser($userId, $search, $limit, $offset);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Request Pending</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="../../assets/css/kendaraan.css" rel="stylesheet">
<link href="../../assets/css/pending.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="app-header d-flex justify-content-between align-items-center px-3">
    <div class="d-flex align-items-center gap-2">
        <img src="../../assets/images/logo-maxi.jpg" alt="Logo Maxi" class="app-logo">
        <h1 class="h6 m-0 fw-semibold">Request Pending</h1>
    </div>
    <form id="logoutForm" method="POST" class="d-inline m-0">
        <input type="hidden" name="logout" value="1">
        <button type="button" id="logoutBtn" class="btn btn-outline-light btn-sm border-0">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>
</header>

<div class="app-content mt-3 px-2">
    <a href="dashboard.php" class="btn btn-secondary w-100 mb-3">← Kembali ke Dashboard</a>

    <form method="GET" class="filter-bar d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari kendaraan..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
    </form>

    <?php if (!empty($dataRequest)): ?>
        <?php foreach ($dataRequest as $req): ?>
            <div class="card-item" onclick="toggleDetail(this)">
                <div class="card-top">
                    <img src="<?= $baseUrl . htmlspecialchars($req['gambar'] ?? 'default.jpg') ?>" 
                        alt="Kendaraan"
                        onerror="this.src='<?= $baseUrl ?>default.jpg'">
                    <div>
                        <h6><?= htmlspecialchars($req['plat_nomor']) ?></h6>
                        <small><?= htmlspecialchars($req['merek']) ?> | <?= htmlspecialchars($req['jenis_kendaraan']) ?></small><br>
                        <small><i class="fas fa-map-marker-alt text-danger"></i> <?= htmlspecialchars($req['nama_lokasi']) ?></small><br>
                        <span class="badge bg-warning text-dark mt-1"><?= htmlspecialchars($req['nama_status']) ?></span>
                    </div>
                </div>
                <div class="card-details">
                    <p><strong>Warna:</strong> <?= htmlspecialchars($req['warna']) ?></p>
                    <p><strong>Tanggal Request:</strong> <?= htmlspecialchars($req['tanggal_request']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">Belum ada request pending.</div>
    <?php endif; ?>
</div>

<?php include "navbar.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleDetail(card) {
    const details = card.querySelector('.card-details');
    details.classList.toggle('active');
}
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Yakin ingin logout?',
        text: 'Sesi Anda akan berakhir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, logout!',
        cancelButtonText: 'Batal'
    }).then((r) => { if (r.isConfirmed) document.getElementById('logoutForm').submit(); });
});
</script>
</body>
</html>
