<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3,4])) {
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
    $result = updateData($type, $id, $_POST);

    if ($result === true) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data berhasil diupdate.'
        ];
        header("Location: edit.php?type=$type&id=$id");
        exit;
    } elseif ($result === 'not_allowed') {
        $_SESSION['flash'] = [
            'icon'  => 'warning',
            'title' => 'Tidak Bisa Diubah',
            'text'  => 'Pemakaian Yang Sudah Selesai Tidak Dapat di Edit Kembali'
        ];
        header("Location: edit.php?type=$type&id=$id");
        exit;
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Gagal mengubah data.'
        ];
        header("Location: edit.php?type=$type&id=$id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit <?= ucfirst($type) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
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
                    
                <?php elseif ($type == 'divisi'): ?>
                    <div class="mb-3">
                        <label>Nama Divisi</label>
                        <input type="text" name="nama_divisi" class="form-control" value="<?= htmlspecialchars($data['nama_divisi']) ?>" required>
                    </div>

                <?php elseif ($type == 'status'): ?>
                    <div class="mb-3">
                        <label>Nama Status</label>
                        <input type="text" name="nama_status" class="form-control" value="<?= htmlspecialchars($data['nama_status']) ?>" required>
                    </div>

                <?php elseif ($type == 'roles'): ?>
                    <div class="mb-3">
                        <label>Nama Roles</label>
                        <input type="text" name="nama_roles" class="form-control" value="<?= htmlspecialchars($data['nama_roles']) ?>" required>
                    </div>

                <?php elseif ($type == 'lokasi'): ?>
                    <div class="mb-3">
                        <label>Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control" value="<?= htmlspecialchars($data['nama_lokasi']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($data['alamat']) ?>" required>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
<?php include "script.php"; ?>
</body>
</html>
