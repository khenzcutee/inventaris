<?php
session_start();
require "../functions/functions.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['logout'])) {
    logout();
}

// Hitung jumlah data di tiap tabel
$jumlahDivisi    = getCount('divisi');
$jumlahKendaraan = getCount('kendaraan');
$jumlahLokasi    = getCount('lokasi');
$jumlahUser      = getCount('user');

// Data kendaraan
$kendaraan = getKendaraanLengkap();

// Data User
$user = getUserLengkap();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
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
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<?php include "navbar.php"; ?>
<?php include "sidebar.php"; ?>

<!-- Main Content -->
<div class="col-md-10 p-4">
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
                <div class="card-header">Kendaraan</div>
                <div class="card-body">
                    <h3 class="text-primary"><?= $jumlahKendaraan ?></h3>
                    <p class="text-muted">Total Kendaraan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stats-card">
                <div class="card-header">Lokasi</div>
                <div class="card-body">
                    <h3 class="text-primary"><?= $jumlahLokasi ?></h3>
                    <p class="text-muted">Total Lokasi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stats-card">
                <div class="card-header">User</div>
                <div class="card-body">
                    <h3 class="text-primary"><?= $jumlahUser ?></h3>
                    <p class="text-muted">Total User</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data 1 -->
    <div class="card mb-4 card-table">
        <div class="card-header">
            Daftar Kendaraan
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

    <!-- Tabel Data 2 (Copy) -->
    <div class="card mb-4 card-table">
        <div class="card-header">
            Daftar User
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
    <!-- Tabel Data 3 (Copy) -->
    <div class="card mb-4 card-table">
        <div class="card-header">
            Daftar Kendaraan (Copy)
        </div>
        <div class="card-body">
            <table id="dataTable3" class="table table-striped table-bordered">
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
</div>
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
