<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3])) {
    header("Location: ../../index.php");
    exit;
}

$type = $_GET['type'] ?? null;

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($type === 'user') {
        $result = tambahUser($_POST);
    } elseif ($type === 'kendaraan') {
        $result = tambahKendaraan($_POST, $_FILES);
    } elseif ($type === 'pemakaian') {
        $result = tambahPemakaian($_POST);
    } elseif ($type === 'divisi') {
        $result = tambahDivisi($_POST);
    } elseif ($type === 'lokasi') {
        $result = tambahLokasi($_POST);
    } elseif ($type === 'roles') {
        $result = tambahRoles($_POST);
    } elseif ($type === 'status') {
        $result = tambahStatus($_POST);
    } else {
        $result = false;
    }

    $status = $result
        ? ['icon' => 'success', 'title' => 'Berhasil!', 'text' => 'Data berhasil ditambahkan!']
        : ['icon' => 'error', 'title' => 'Gagal!', 'text' => 'Data gagal ditambahkan!'];
}
?>

<?php if (isset($status)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: '<?= $status['icon'] ?>',
        title: '<?= $status['title'] ?>',
        text: '<?= $status['text'] ?>'
    }).then(() => {
        <?php if ($status['icon'] === 'success'): ?>
            window.location.href = 'view_data.php?type=<?= $type ?>';
        <?php endif; ?>
    });
});
</script>
<?php endif; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah <?= ucfirst($type) ?> - Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include "navbar.php"; ?>
<div class="d-flex">

    <!-- Main Content -->
    <div class="container-fluid p-4">
        <h2 class="mb-4 text-primary">➕ Tambah <?= ucfirst($type) ?></h2>
        <a href="view_data.php?type=<?= $type ?>" class="btn btn-secondary mb-3">← Kembali</a>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Form Tambah <?= ucfirst($type) ?>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="row g-3">

                    <?php if ($type === 'user'): ?>
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Roles</label>
                            <select name="id_roles" class="form-select"><?= getRolesOptions(); ?></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Divisi</label>
                            <select name="id_divisi" class="form-select"><?= getDivisiOptions(); ?></select>
                        </div>

                    <?php elseif ($type === 'kendaraan'): ?>
                        <div class="col-md-6">
                            <label class="form-label">Plat Nomor</label>
                            <input type="text" name="plat_nomor" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor STNK</label>
                            <input type="text" name="nomor_stnk" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bahan Bakar</label>
                            <input type="text" name="bahan_bakar" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Warna</label>
                            <input type="text" name="warna" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kendaraan</label>
                            <input type="text" name="jenis_kendaraan" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Merek</label>
                            <input type="text" name="merek" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kilometer</label>
                            <input type="number" name="kilometer" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gambar</label>
                            <input type="file" name="gambar" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <select name="id_lokasi" class="form-select"><?= getLokasiOptions(); ?></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="id_status" class="form-select"><?= getStatusOptions(); ?></select>
                        </div>

                    <?php elseif ($type === 'pemakaian'): ?>
                        <div class="col-md-6">
                            <label class="form-label">User</label>
                            <select name="id_user" class="form-select"><?= getUserOptions(); ?></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kendaraan</label>
                            <select name="id_inventaris" class="form-select"><?= getKendaraanOptions(); ?></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Keluar</label>
                            <input type="date" name="tanggal_keluar" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="id_status" class="form-select"><?= getStatusTambahOptions(); ?></select>
                        </div>

                    <?php elseif ($type === 'divisi'): ?>
                        <div class="col-md-6">
                            <label class="form-label">Nama Divisi</label>
                            <input type="text" name="nama_divisi" class="form-control">
                        </div>
                    
                    <?php elseif ($type === 'lokasi') : ?>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" name="nama_lokasi" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alamat Lengkap</label>
                            <input type="text" name="alamat" class="form-control">
                        </div>
                
                    <?php elseif ($type === 'roles') : ?>
                        <div class="col-md-6">
                            <label class="form-label">Nama Roles</label>
                            <input type="text" name="nama_roles" class="form-control">
                        </div>

                    <?php elseif ($type === 'status') : ?>
                        <div class="col-md-6">
                            <label class="form-label">Nama Status</label>
                            <input type="text" name="nama_status" class="form-control">
                        </div>
                    <?php else: ?>
                        <div class="col-12 text-danger">
                            Form tidak tersedia untuk tipe ini.
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <button type="submit" class="btn btn-success">✅ Simpan</button>
                        <a href="view_Data.php?type=<?= $type ?>" class="btn btn-secondary">❌ Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "script.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
