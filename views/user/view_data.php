<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['id_roles'] != 5) {
    header("Location: ../index.php");
    exit;
}

$userId   = (int)$_SESSION['id'];
$namaUser = $_SESSION['nama'] ?? $_SESSION['username'];

// Ambil parameter search dan pagination
$search  = isset($_GET['search']) ? trim($_GET['search']) : '';
$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pageNum < 1) $pageNum = 1;

$limit  = 10;
$offset = ($pageNum - 1) * $limit;

// Pemakaian Aktif
$dataAktif       = getUserPemakaianAktif($userId, $search, $limit, $offset);
$totalRowsAktif  = getUserPemakaianAktifCount($userId, $search);
$totalPagesAktif = max(1, ceil($totalRowsAktif / $limit));

// History
$dataHistory       = getUserPemakaianHistory($userId, $search, $limit, $offset);
$totalRowsHistory  = getUserPemakaianHistoryCount($userId, $search);
$totalPagesHistory = max(1, ceil($totalRowsHistory / $limit));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemakaian & History - <?= htmlspecialchars($namaUser) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<?php include "navbar.php"; ?>
<?php include "sidebar.php"; ?>

<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">📄 Data Pemakaian <?= htmlspecialchars($namaUser) ?></h2>

    <!-- Form search -->
    <form method="GET" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari kendaraan..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <!-- PEMAKAIAN AKTIF -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">Pemakaian Aktif</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Keluar</th>
                            <th>Plat Nomor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dataAktif)): ?>
                            <?php $no = $offset + 1; foreach ($dataAktif as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_keluar']) ?></td>
                                    <td><?= htmlspecialchars($row['plat_nomor']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">Tidak ada pemakaian aktif</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination aktif -->
            <?php if ($totalPagesAktif > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPagesAktif; $i++): ?>
                        <li class="page-item <?= ($i == $pageNum) ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- HISTORY -->
    <div class="card">
        <div class="card-header bg-secondary text-white">History Pemakaian</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Keluar</th>
                            <th>Tanggal Masuk</th>
                            <th>Plat Nomor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dataHistory)): ?>
                            <?php $no = $offset + 1; foreach ($dataHistory as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_keluar']) ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_masuk']) ?></td>
                                    <td><?= htmlspecialchars($row['plat_nomor']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Belum ada history</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination history -->
            <?php if ($totalPagesHistory > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPagesHistory; $i++): ?>
                        <li class="page-item <?= ($i == $pageNum) ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "script.php"; ?>
</body>
</html>
