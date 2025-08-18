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
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4 text-primary">Detail <?= ucfirst($type) ?></h2>
    <a href="view_Data.php?type=<?= $type ?>" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <?php foreach ($data as $key => $value): ?>
                    <tr>
                        <th><?= ucfirst(str_replace("_", " ", $key)) ?></th>
                        <td><?= htmlspecialchars($value) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
