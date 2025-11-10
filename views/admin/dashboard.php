<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3,4])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout(); // Panggil function logout() yang sudah kamu punya
    exit;
}

// Hitung jumlah data di tiap tabel
$jumlahDivisi    = getCount('divisi');
$jumlahKendaraanTersedia = getCountKendaraanTersedia('kendaraan');
$jumlahRequest   = getCountRequest('pemakaian');
$jumlahUser      = getCount('user');
$jumlahPemakaian = getCountPemakaian('pemakaian');

// Data kendaraan
$kendaraan = getKendaraanLengkap();

// Data User
$user = getUserLengkap();

// Data Pemakaian
$pemakaian = getPemakaianLengkap();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <style>
        /* Efek hover hanya untuk card statistik */
        .stats-card {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: scale(1.05);
        }

        /* Card tabel tetap normal */
        .card-table {
            cursor: default;
            transition: none;
        }
        .card-table:hover {
            transform: none;
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<?php include "navbar.php"; ?>
<!-- Main Content -->
<div class="container-fluid p-4">
    <h2 class="mb-4 text-primary">📊 Dashboard Inventaris</h2>

    <!-- Cards Statistik -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center stats-card">
                <div class="card-header">Divisi</div>
                <div class="card-body">
                    <h3 class="text-primary"><?= $jumlahDivisi ?></h3>
                    <p class="text-muted">Total Divisi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stats-card">
                <a href="view_data.php?type=kendaraan">
                <div class="card-header">Kendaraan</div>
                <div class="card-body">
                    <h3 class="text-primary"><?= $jumlahKendaraanTersedia ?></h3>
                    <p class="text-muted">Kendaraan Yang Tersedia</p>
                </div>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stats-card">
                <a href="approved.php">
                <div class="card-header">Request</div>
                <div class="card-body">
                    <h3 class="text-primary"><?= $jumlahRequest?></h3>
                    <p class="text-muted">Total Request</p>
                </div>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stats-card">
                <a href="view_data.php?type=pemakaian">
                <div class="card-header">Pemakaian</div>
                <div class="card-body">
                    <h3 class="text-primary"><?= $jumlahPemakaian ?></h3>
                    <p class="text-muted">Pemakaian Aktif</p>
                </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Data 1 -->
<div class="card mb-4 card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Kendaraan</span>
        <div>
            <a href="view_data.php?type=kendaraan" class="btn btn-success btn-sm">👁 Lihat Semua</a>
            <a href="tambah.php?type=kendaraan" class="btn btn-primary btn-sm">+ Tambah Kendaraan</a>
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable1" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Plat Nomor</th>
                    <th>Nomor STNK</th>
                    <th>Bahan Bakar</th>
                    <th>Warna</th>
                    <th>Jenis</th>
                    <th>Merek</th>
                    <th>Kilometer</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kendaraan as $row) : ?>
                    <tr>
                        <td><?= $row['plat_nomor'] ?></td>
                        <td><?= $row['nomor_stnk'] ?></td>
                        <td><?= $row['bahan_bakar'] ?></td>
                        <td><?= $row['warna'] ?></td>
                        <td><?= $row['jenis_kendaraan'] ?></td>
                        <td><?= $row['merek'] ?></td>
                        <td><?= number_format($row['kilometer']) ?></td>
                        <td><?= $row['nama_lokasi'] ?></td>
                        <td><?= $row['nama_status'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tabel Data 2 -->
<div class="card mb-4 card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar User</span>
        <div>
            <a href="view_data.php?type=user" class="btn btn-success btn-sm">👁 Lihat Semua</a>
            <a href="tambah.php?type=user" class="btn btn-primary btn-sm">+ Tambah User</a>
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable2" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Divisi</th>
                    <th>Roles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user as $row) : ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['username']?></td>
                        <td><?= $row['nama_divisi']?></td>
                        <td><?= $row['nama_roles']?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tabel Data 3 -->
<div class="card mb-4 card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Pemakaian</span>
        <div>
            <a href="view_Data.php?type=pemakaian" class="btn btn-success btn-sm">👁 Lihat Semua</a>
            <a href="tambah.php?type=pemakaian" class="btn btn-primary btn-sm">+ Tambah Pemakaian</a>
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable3" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Kendaraan</th>
                    <th>Tanggal Keluar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($pemakaian)):
                    foreach ($pemakaian as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
                            <td><?= htmlspecialchars($row['plat_nomor']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_keluar']) ?></td>
                            <td><?= htmlspecialchars($row['nama_status']) ?></td>
                        </tr>
                <?php 
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data pemakaian</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#dataTable1').DataTable({
        responsive: false
    });
    $('#dataTable2').DataTable({
        responsive: false
    });
    $('#dataTable3').DataTable({
        responsive: false
    });
});
</script>
</body>
</html>