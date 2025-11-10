<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['id_roles'] != 5) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout();
    exit;
}

// FILTER
$filterLokasi = isset($_GET['lokasi']) ? (int)$_GET['lokasi'] : 0;
$filterJenis  = isset($_GET['jenis']) ? trim($_GET['jenis']) : '';

$lokasiList = getAllLokasi();
$jenisList  = getAllJenisKendaraan();

$query = "SELECT k.id, k.plat_nomor, k.nomor_stnk, k.bahan_bakar,
                       k.warna, k.jenis_kendaraan, k.merek, k.kilometer,
                       k.gambar, l.nama_lokasi, s.nama_status
          FROM kendaraan k
          INNER JOIN lokasi l ON k.id_lokasi = l.id
          INNER JOIN status s ON k.id_status = s.id 
          WHERE 1=1";

if ($filterLokasi > 0) {
    $query .= " AND k.id_lokasi = " . $filterLokasi;
}
if (!empty($filterJenis)) {
    $query .= " AND k.jenis_kendaraan = '" . mysqli_real_escape_string($conn, $filterJenis) . "'";
}
$query .= " ORDER BY k.id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar Kendaraan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="../../assets/css/kendaraan.css" rel="stylesheet">
</head>
<body>

<header class="app-header d-flex justify-content-between align-items-center px-3">
    <div class="d-flex align-items-center gap-2">
        <img src="../../assets/images/logo-maxi.jpg" alt="Logo Maxi" class="app-logo">
        <h1 class="h6 m-0 fw-semibold">Daftar Kendaraan</h1>
    </div>
    <form id="logoutForm" method="POST" class="d-inline m-0">
        <input type="hidden" name="logout" value="1">
        <button type="button" id="logoutBtn" class="btn btn-outline-light btn-sm border-0">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>
</header>

<div class="app-content">
    <!-- Filter -->
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-6">
                    <select name="lokasi" class="form-select">
                        <option value="">Semua Lokasi</option>
                        <?php foreach ($lokasiList as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= ($filterLokasi == $l['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($l['nama_lokasi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6">
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        <?php foreach ($jenisList as $j): ?>
                            <option value="<?= htmlspecialchars($j['jenis_kendaraan']) ?>" <?= ($filterJenis == $j['jenis_kendaraan']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['jenis_kendaraan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Kendaraan -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card-item" onclick="toggleDetail(this)">
                <div class="card-top">
                    <img src="../../assets/images/kendaraan/<?= htmlspecialchars($row['gambar']) ?>" alt="Kendaraan">
                    <div>
                        <h6><?= htmlspecialchars($row['plat_nomor']) ?></h6>
                        <small><?= htmlspecialchars($row['merek']) ?> | <?= htmlspecialchars($row['jenis_kendaraan']) ?></small><br>
                        <small><i class="fas fa-map-marker-alt text-danger"></i> <?= htmlspecialchars($row['nama_lokasi']) ?></small>
                    </div>
                </div>
                <div class="card-details">
                    <p><strong>Nomor STNK:</strong> <?= htmlspecialchars($row['nomor_stnk']) ?></p>
                    <p><strong>Bahan Bakar:</strong> <?= htmlspecialchars($row['bahan_bakar']) ?></p>
                    <p><strong>Warna:</strong> <?= htmlspecialchars($row['warna']) ?></p>
                    <p><strong>Kilometer:</strong> <?= number_format($row['kilometer']) ?> km</p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($row['nama_status'] ?? 'Tidak diketahui') ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">Tidak ada kendaraan ditemukan.</div>
    <?php endif; ?>
</div>

<?php include "navbar.php"?>
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
