<?php
session_start();
require "../functions/functions.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil parameter page
$page = isset($_GET['type']) ? $_GET['type'] : '';

// Ambil data sesuai page
$data = getDataView($page);

// Hitung jumlah kolom
$colCount = 2; // No + Aksi
if ($page == 'user') {
    $colCount += 4;
} elseif ($page == 'kendaraan') {
    $colCount += 9;
} elseif ($page == 'pemakaian') {
    $colCount += 4;
} elseif ($page == 'divisi') {
    $colCount += 1;
} elseif ($page == 'roles') {
    $colCount += 1;
} elseif ($page == 'status') {
    $colCount += 1;
} elseif ($page == 'lokasi') {
    $colCount += 2;
}

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['id']) && isset($_GET['type'])) {
    $deleteType = $_GET['type'];
    $deleteId = (int)$_GET['id'];

    if (deleteData($deleteType, $deleteId)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='view_Data.php?type=$deleteType';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!');</script>";
    }
}

// Logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout(); // Panggil function logout() yang sudah kamu punya
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Data - Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<?php include "navbar.php"; ?>
<?php include "sidebar.php"; ?>

<!-- Main Content -->
<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">📄 Data <?= ucfirst($page) ?></h2>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar <?= ucfirst($page) ?></span>
            <a href="tambah.php?type=<?= $page ?>" class="btn btn-primary btn-sm">+ Tambah <?= ucfirst($page) ?></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <?php if ($page == 'user'): ?>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Divisi</th>
                                <th>Roles</th>
                            <?php elseif ($page == 'kendaraan'): ?>
                                <th>Plat Nomor</th>
                                <th>Nomor STNK</th>
                                <th>Bahan Bakar</th>
                                <th>Warna</th>
                                <th>Jenis</th>
                                <th>Merek</th>
                                <th>Kilometer</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                            <?php elseif ($page == 'pemakaian'): ?>
                                <th>Tanggal</th>
                                <th>Pengguna</th>
                                <th>Plat Nomor</th>
                                <th>Status</th>
                            <?php elseif ($page == 'divisi'): ?>
                                <th>Nama Divisi</th>
                            <?php endif; ?>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data)): ?>
                            <?php $no = 1; foreach ($data as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <?php if ($page == 'user'): ?>
                                        <td><?= $row['nama'] ?></td>
                                        <td><?= $row['username'] ?></td>
                                        <td><?= $row['nama_divisi'] ?></td>
                                        <td><?= $row['nama_roles'] ?></td>
                                    <?php elseif ($page == 'kendaraan'): ?>
                                        <td><?= $row['plat_nomor'] ?></td>
                                        <td><?= $row['nomor_stnk'] ?></td>
                                        <td><?= $row['bahan_bakar'] ?></td>
                                        <td><?= $row['warna'] ?></td>
                                        <td><?= $row['jenis_kendaraan'] ?></td>
                                        <td><?= $row['merek'] ?></td>
                                        <td><?= number_format($row['kilometer']) ?></td>
                                        <td><?= $row['nama_lokasi'] ?></td>
                                        <td><?= $row['nama_status'] ?></td>
                                    <?php elseif ($page == 'pemakaian'): ?>
                                        <td><?= htmlspecialchars($row['tanggal_keluar'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_user'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['plat_nomor'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <?php elseif ($page == 'divisi'): ?>
                                        <td><?= $row['nama_divisi'] ?></td>
                                    <?php elseif ($page == 'roles'): ?>
                                        <td><?= $row['nama_roles'] ?></td>
                                    <?php elseif ($page == 'status'): ?>
                                        <td><?= $row['nama_status'] ?></td>
                                    <?php elseif ($page == 'lokasi'): ?>
                                        <td><?= $row['nama_lokasi'] ?></td>
                                        <td><?= $row['alamat'] ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <a href="detail.php?type=<?= $page ?>&id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                                        <a href="edit.php?type=<?= $page ?>&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="view_Data.php?type=<?= $page ?>&delete=1&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= $colCount ?>" class="text-center">Tidak ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
