<?php
session_start();
require "../functions/functions.php";

// Cek login
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../index.php");
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail <?= ucfirst($type) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<?php include "navbar.php"; ?>
<!-- Sidebar -->
<?php include "sidebar.php"; ?>

<!-- Main Content -->
<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">📋 Detail <?= ucfirst($type) ?></h2>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Detail <?= ucfirst($type) ?></span>
            <a href="view_Data.php?type=<?= $type ?>" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php foreach ($data as $key => $value): ?>
                        <tr>
                            <th style="width:30%;"><?= ucfirst(str_replace("_", " ", $key)) ?></th>
                            <td><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></td>
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
