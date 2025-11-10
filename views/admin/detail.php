<?php
session_start();
require "../../functions/functions.php";

// Cek login
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3,4])) {
    header("Location: ../../index.php");
    exit;
}

// Ambil parameter
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($type) || $id <= 0) {
    die("Data tidak valid.");
}

// Ambil detail data
$data = getDetailData($type, $id);
if (!$data) {
    die("Data tidak ditemukan.");
}

// Tentukan folder gambar berdasarkan jenis data
$imagePath = '';
if ($type === 'kendaraan') {
    $imagePath = '../../assets/images/kendaraan/';
} elseif ($type === 'user') {
    $imagePath = '../../assets/images/users/';
} else {
    $imagePath = '../../assets/images/';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail <?= ucfirst($type) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); }
        th { width: 30%; background: #f1f3f5; }
        td img {
            border-radius: 8px;
            max-width: 240px;
            height: auto;
            object-fit: cover;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .table th, .table td { vertical-align: middle; }
        .btn-secondary { border-radius: 8px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<?php include "navbar.php"; ?>

<!-- Main Content -->
<div class="container-fluid p-4">
    <h2 class="mb-4 text-primary">📋 Detail <?= ucfirst($type) ?></h2>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <span>Detail <?= ucfirst($type) ?></span>
            <a href="view_Data.php?type=<?= $type ?>" class="btn btn-light btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <?php foreach ($data as $key => $value): ?>
                        <tr>
                            <th><?= ucfirst(str_replace("_", " ", $key)) ?></th>
                            <td>
                                <?php if ($key === 'gambar' && !empty($value)): ?>
                                    <a href="<?= $imagePath . htmlspecialchars($value) ?>" target="_blank">
                                        <img src="<?= $imagePath . htmlspecialchars($value) ?>" 
                                             alt="Gambar <?= htmlspecialchars($type) ?>" 
                                             onerror="this.src='<?= $imagePath ?>default.jpg'">
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
