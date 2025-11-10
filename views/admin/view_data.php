<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3,4])) {
    header("Location: ../../index.php");
    exit;
}

/* ------ Ambil & validasi parameter tipe halaman ------ */
$allowedPages = ['user','kendaraan','pemakaian','history','divisi','roles','status','lokasi'];
$page = isset($_GET['type']) ? $_GET['type'] : 'user';
if (!in_array($page, $allowedPages, true)) {
    $page = 'user';
}

/* ------ Handle delete (PRG + flash) lebih dulu ------ */
if (isset($_GET['delete'], $_GET['id'], $_GET['type'])) {
    $deleteType = $_GET['type'];
    $deleteId   = (int)$_GET['id'];

    $result = deleteData($deleteType, $deleteId);

    if ($result === true) {
        $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>'Data berhasil dihapus.'];
    } elseif ($result === 'not_allowed') {
        $_SESSION['flash'] = ['icon'=>'warning','title'=>'Tidak Bisa Dihapus','text'=>'Pemakaian yang sudah selesai tidak dapat dihapus.'];
    } elseif ($result === 'has_relation') {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal Menghapus','text'=>'Data ini memiliki relasi sehingga tidak dapat dihapus.'];
    } else {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal','text'=>'Terjadi kesalahan saat menghapus data.'];
    }

    header("Location: view_Data.php?type=" . urlencode($deleteType));
    exit;
}

/* ------ Search & Pagination ------ */
$search  = isset($_GET['search']) ? trim($_GET['search']) : '';
$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pageNum < 1) $pageNum = 1;

$limit  = 10;
$offset = ($pageNum - 1) * $limit;

/* ------ Ambil data paginated ------ */
$data       = getPaginatedData($page, $search, $limit, $offset);
$totalRows  = getTotalRows($page, $search);
$totalPages = max(1, (int)ceil($totalRows / $limit));

/* ------ Hitung kolom untuk colspan "Tidak ada data" ------ */
$colCount = 2; // No + Aksi
if ($page == 'user') {
    $colCount += 4;
} elseif ($page == 'kendaraan') {
    $colCount += 10;
} elseif ($page == 'pemakaian') {
    $colCount += 4;
} elseif ($page == 'history') {
    $colCount += 6;
} elseif ($page == 'divisi') {
    $colCount += 1;
} elseif ($page == 'roles') {
    $colCount += 1;
} elseif ($page == 'status') {
    $colCount += 1;
} elseif ($page == 'lokasi') {
    $colCount += 2;
}

/* ------ Logout (jika dipakai) ------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout();
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Data - Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link href="../../assets/css/table.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<?php include "navbar.php"; ?>

<div class="container-fluid p-4">
    <h2 class="mb-4 text-primary">📄 Data <?= htmlspecialchars(ucfirst($page), ENT_QUOTES, 'UTF-8') ?></h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar <?= htmlspecialchars(ucfirst($page), ENT_QUOTES, 'UTF-8') ?></span>
            <div class="btn-group"> 
                <?php
                    $isSelesai = (int)($row['id_status'] ?? 0) === 5;
                    if ($page === 'pemakaian'): ?>
                    <a href="view_Data.php?type=history" class="btn btn-secondary btn-sm">History</a>
                    <a href="pengembalian.php" class="btn btn-primary btn-sm">Pengembalian</a>
                    <a href="tambah.php?type=<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-success btn-sm">
                    + Tambah <?= htmlspecialchars(ucfirst($page), ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?php elseif ($page === 'history') :?>
                    <?php else: ?>
                    <a href="tambah.php?type=<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-success btn-sm">
                    + Tambah <?= htmlspecialchars(ucfirst($page), ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">
            <!-- Form search -->
            <form method="GET" class="mb-3 d-flex">
                <input type="hidden" name="type" value="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari data..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>

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
                                <th>Gambar</th>
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
                                <th>Peminjam</th>
                                <th>Plat Nomor</th>
                                <th>Status</th>
                            <?php elseif ($page == 'history'): ?>
                                <th>Tanggal Keluar</th>
                                <th>Tanggal Masuk</th>
                                <th>Peminjam</th>
                                <th>Plat Nomor</th>
                                <th>Kilometer Terakhir</th>
                                <th>Status</th>
                            <?php elseif ($page == 'divisi'): ?>
                                <th>Nama Divisi</th>
                            <?php elseif ($page == 'roles'): ?>
                                <th>Nama Roles</th>
                            <?php elseif ($page == 'status'): ?>
                                <th>Nama Status</th>
                            <?php elseif ($page == 'lokasi'): ?>
                                <th>Nama Lokasi</th>
                                <th>Alamat</th>
                            <?php endif; ?>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data)): ?>
                            <?php $no = $offset + 1; foreach ($data as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>

                                    <?php if ($page == 'user'): ?>
                                        <td><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_divisi'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_roles'], ENT_QUOTES, 'UTF-8') ?></td>

                                    <?php elseif ($page == 'kendaraan'): ?>
                                        <td>
                                            <a href="../../assets/images/kendaraan/<?= htmlspecialchars($row['gambar']) ?>" target="_blank">
                                                <img src="../../assets/images/kendaraan/<?= htmlspecialchars($row['gambar']) ?>" 
                                                    alt="Gambar Kendaraan" 
                                                    class="img-thumbnail shadow-sm">
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($row['plat_nomor'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nomor_stnk'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['bahan_bakar'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['warna'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['jenis_kendaraan'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['merek'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= number_format((int)$row['kilometer']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_lokasi'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8') ?></td>

                                    <?php elseif ($page == 'pemakaian'): ?>
                                        <td><?= htmlspecialchars($row['tanggal_keluar'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_user'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['plat_nomor'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8') ?></td>

                                    <?php elseif ($page == 'history'): ?>
                                        <td><?= htmlspecialchars($row['tanggal_keluar'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['tanggal_masuk'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_user'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['plat_nomor'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['kilometer'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8') ?></td>

                                    <?php elseif ($page == 'divisi'): ?>
                                        <td><?= htmlspecialchars($row['nama_divisi'], ENT_QUOTES, 'UTF-8') ?></td>

                                    <?php elseif ($page == 'roles'): ?>
                                        <td><?= htmlspecialchars($row['nama_roles'], ENT_QUOTES, 'UTF-8') ?></td>

                                    <?php elseif ($page == 'status'): ?>
                                        <td><?= htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8') ?></td>

                                    <?php elseif ($page == 'lokasi'): ?>
                                        <td><?= htmlspecialchars($row['nama_lokasi'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['alamat'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <?php endif; ?>

                                    <td>
                                        <!-- View selalu aktif -->
                                        <a href="detail.php?type=<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>&id=<?= (int)$row['id'] ?>"
                                           class="btn btn-info btn-sm">View</a>

                                        <?php
                                        $isSelesai = (int)($row['id_status'] ?? 0) === 5;
                                        if ($page === 'history' || ($page === 'pemakaian')): ?>
                                            <a href="#"
                                               class="btn btn-warning btn-sm btn-edit-disabled"
                                               data-title="Tidak Bisa Edit!"
                                               data-text="Data Pemakaian Tidak Dapat di Edit.">
                                               Edit
                                            </a>
                                        <?php else: ?>
                                            <a href="edit.php?type=<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>&id=<?= (int)$row['id'] ?>"
                                               class="btn btn-warning btn-sm">Edit</a>
                                        <?php endif; ?>

                                        <a href="view_Data.php?type=<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>&delete=1&id=<?= (int)$row['id'] ?>"
                                           class="btn btn-danger btn-sm btn-delete">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= $colCount ?>" class="text-center">Tidak ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $pageNum) ? 'active' : '' ?>">
                            <a class="page-link" href="?type=<?= urlencode($page) ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
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
