<?php
session_start();
require "../../functions/functions.php";

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

// Parameter GET
$view    = $_GET['view'] ?? 'aktif';
$search  = trim($_GET['search'] ?? '');
$pageNum = max(1, (int)($_GET['page'] ?? 1));

$limit  = 5;
$offset = ($pageNum - 1) * $limit;

// Data dari fungsi
$dataAktif       = getUserPemakaianAktif($userId, $search, $limit, $offset);
$totalRowsAktif  = getUserPemakaianAktifCount($userId, $search);
$totalPagesAktif = max(1, ceil($totalRowsAktif / $limit));

$dataHistory       = getUserPemakaianHistory($userId, $search, $limit, $offset);
$totalRowsHistory  = getUserPemakaianHistoryCount($userId, $search);
$totalPagesHistory = max(1, ceil($totalRowsHistory / $limit));

$baseUrl = "/inventaris/assets/images/kendaraan/";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $view === 'aktif' ? 'Pemakaian Aktif' : 'History Pemakaian' ?> - <?= htmlspecialchars($namaUser) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="../../assets/css/kendaraan.css" rel="stylesheet">

<style>
/* ===== Header ===== */
.app-header {
    background-color: #0d6efd;
    color: #fff;
    padding: 0.8rem 1rem;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    border-bottom-left-radius: 14px;
    border-bottom-right-radius: 14px;
}
.app-logo {
    height: 40px;
    width: auto;
    border-radius: 8px;
    object-fit: contain;
    background-color: white;
    padding: 2px;
}

/* ===== View switch buttons ===== */
.view-switch {
    text-align: center;
    margin: 15px 0;
}
.view-switch .btn {
    width: 48%;
    font-weight: 600;
}

/* ===== Card style (sama seperti Pending) ===== */
.card-item {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    margin-bottom: 15px;
    overflow: hidden;
    cursor: pointer;
    transition: 0.3s ease;
}
.card-item:hover {
    background-color: #f8faff;
}
.card-top {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
}
.card-top img {
    width: 90px;
    height: 70px;
    border-radius: 8px;
    object-fit: cover;
    background-color: #f0f0f0;
    flex-shrink: 0;
}
.card-top h6 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 2px;
}
.card-top small {
    color: #555;
}
.card-details {
    display: none;
    padding: 10px 15px;
    border-top: 1px solid #eee;
    background-color: #fafafa;
    font-size: 0.9rem;
    color: #444;
}
.card-details.active {
    display: block;
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ===== Responsif ===== */
@media (max-width: 768px) {
    .card-top img { width: 85px; height: 65px; }
    .card-top h6 { font-size: 0.95rem; }
    .card-top small { font-size: 0.85rem; }
    .card-details { font-size: 0.85rem; }
}
</style>
</head>
<body class="d-flex flex-column min-vh-100">

<header class="app-header d-flex justify-content-between align-items-center px-3">
    <div class="d-flex align-items-center gap-2">
        <img src="../../assets/images/logo-maxi.jpg" alt="Logo Maxi" class="app-logo">
        <h1 class="h6 m-0 fw-semibold">Dashboard User</h1>
    </div>
    <form id="logoutForm" method="POST" class="m-0">
        <input type="hidden" name="logout" value="1">
        <button type="button" id="logoutBtn" class="btn btn-outline-light btn-sm border-0">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>
</header>

<div class="app-content mt-3 px-2">
    <!-- Tombol switch -->
    <div class="view-switch">
        <a href="?view=aktif" class="btn <?= $view === 'aktif' ? 'btn-warning' : 'btn-outline-warning' ?>">Aktif</a>
        <a href="?view=history" class="btn <?= $view === 'history' ? 'btn-secondary' : 'btn-outline-secondary' ?>">History</a>
    </div>

    <!-- Form pencarian -->
    <form method="GET" class="filter-bar d-flex mb-3">
        <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari kendaraan..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
    </form>

    <!-- Konten utama -->
    <?php if ($view === 'aktif'): ?>
        <?php if (!empty($dataAktif)): ?>
            <?php foreach ($dataAktif as $row): ?>
                <div class="card-item" onclick="toggleDetail(this)">
                    <div class="card-top">
                        <img src="<?= $baseUrl . htmlspecialchars($row['gambar'] ?? 'default.jpg') ?>" 
                             onerror="this.src='<?= $baseUrl ?>default.png'" 
                             alt="Kendaraan">
                        <div>
                            <h6><?= htmlspecialchars($row['plat_nomor']) ?></h6>
                            <small><?= htmlspecialchars($row['merek'] ?? '-') ?></small><br>
                            <small><i class="fas fa-calendar-alt text-primary"></i> <?= htmlspecialchars($row['tanggal_keluar']) ?></small><br>
                            <span class="badge bg-warning text-dark mt-1"><?= htmlspecialchars($row['nama_status']) ?></span>
                        </div>
                    </div>
                    <div class="card-details">
                        <p><b>Tanggal Keluar:</b> <?= htmlspecialchars($row['tanggal_keluar']) ?></p>
                        <p><b>Warna:</b> <?= htmlspecialchars($row['warna'] ?? '-') ?></p>
                        <p><b>Jenis Kendaraan:</b> <?= htmlspecialchars($row['jenis_kendaraan'] ?? '-') ?></p>
                        <p><b>Status:</b> <?= htmlspecialchars($row['nama_status']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">Tidak ada pemakaian aktif.</div>
        <?php endif; ?>

    <?php elseif ($view === 'history'): ?>
        <?php if (!empty($dataHistory)): ?>
            <?php foreach ($dataHistory as $row): ?>
                <div class="card-item" onclick="toggleDetail(this)">
                    <div class="card-top">
                        <img src="<?= $baseUrl . htmlspecialchars($row['gambar'] ?? 'default.jpg') ?>" 
                             onerror="this.src='<?= $baseUrl ?>default.png'" 
                             alt="Kendaraan">
                        <div>
                            <h6><?= htmlspecialchars($row['plat_nomor']) ?></h6>
                            <small><?= htmlspecialchars($row['merek'] ?? '-') ?></small><br>
                            <small><i class="fas fa-calendar-alt text-secondary"></i> <?= htmlspecialchars($row['tanggal_keluar']) ?> → <?= htmlspecialchars($row['tanggal_masuk']) ?></small><br>
                            <span class="badge bg-secondary mt-1"><?= htmlspecialchars($row['nama_status']) ?></span>
                        </div>
                    </div>
                    <div class="card-details">
                        <p><b>Tanggal Keluar:</b> <?= htmlspecialchars($row['tanggal_keluar']) ?></p>
                        <p><b>Tanggal Masuk:</b> <?= htmlspecialchars($row['tanggal_masuk']) ?></p>
                        <p><b>Warna:</b> <?= htmlspecialchars($row['warna'] ?? '-') ?></p>
                        <p><b>Jenis Kendaraan:</b> <?= htmlspecialchars($row['jenis_kendaraan'] ?? '-') ?></p>
                        <p><b>Status:</b> <?= htmlspecialchars($row['nama_status']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">Belum ada history pemakaian.</div>
        <?php endif; ?>
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
