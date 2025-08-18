<?php
session_start();
require "../functions/functions.php";

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

// Ambil data lama
$data = getDetailData($type, $id);
if (!$data) {
    die("Data tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (updateData($type, $id, $_POST)) {
        echo "<script>alert('Data berhasil diupdate!');window.location='view_Data.php?type=$type';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update data!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit <?= ucfirst($type) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include "navbar.php"; ?>
<?php include "sidebar.php"; ?>

<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">✏️ Edit <?= ucfirst($type) ?></h2>
    <a href="view_Data.php?type=<?= $type ?>" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Form Edit <?= ucfirst($type) ?></div>
        <div class="card-body">
            <form method="POST">
                <?php if ($type == 'user'): ?>
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Divisi</label>
                        <select name="id_divisi" class="form-control">
                            <?= getDivisiOptions($data['id_divisi']); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Roles</label>
                        <select name="id_roles" class="form-control">
                            <?= getRolesOptions($data['id_roles']); ?>
                        </select>
                    </div>

                <?php elseif ($type == 'kendaraan'): ?>
                    <div class="mb-3">
                        <label>Plat Nomor</label>
                        <input type="text" name="plat_nomor" class="form-control" value="<?= htmlspecialchars($data['plat_nomor']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Nomor STNK</label>
                        <input type="text" name="nomor_stnk" class="form-control" value="<?= htmlspecialchars($data['nomor_stnk']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Bahan Bakar</label>
                        <input type="text" name="bahan_bakar" class="form-control" value="<?= htmlspecialchars($data['bahan_bakar']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Warna</label>
                        <input type="text" name="warna" class="form-control" value="<?= htmlspecialchars($data['warna']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Jenis Kendaraan</label>
                        <input type="text" name="jenis_kendaraan" class="form-control" value="<?= htmlspecialchars($data['jenis_kendaraan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Merek</label>
                        <input type="text" name="merek" class="form-control" value="<?= htmlspecialchars($data['merek']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Kilometer</label>
                        <input type="number" name="kilometer" class="form-control" value="<?= htmlspecialchars($data['kilometer']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Lokasi</label>
                        <select name="id_lokasi" class="form-control">
                            <?= getLokasiOptions($data['id_lokasi']); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="id_status" class="form-control">
                            <?= getStatusOptions($data['id_status']); ?>
                        </select>
                    </div>

                <?php elseif ($type == 'pemakaian'): ?>
                    <div class="mb-3">
                        <label>User</label>
                        <select name="id_user" class="form-control">
                            <?= getUserOptions($data['id_user']); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Kendaraan</label>
                        <select name="id_inventaris" class="form-control">
                            <?= getKendaraanOptions($data['id_inventaris']); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluar" class="form-control" value="<?= htmlspecialchars($data['tanggal_keluar']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="<?= htmlspecialchars($data['tanggal_masuk']) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="id_status" class="form-control">
                            <?= getStatusOptions($data['id_status']); ?>
                        </select>
                    </div>

                <?php elseif ($type == 'divisi'): ?>
                    <div class="mb-3">
                        <label>Nama Divisi</label>
                        <input type="text" name="nama_divisi" class="form-control" value="<?= htmlspecialchars($data['nama_divisi']) ?>" required>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
