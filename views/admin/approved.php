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

// Approve Request
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $result = approveRequest($id);
    if ($result === true) {
        $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>'Request disetujui!'];
    } elseif ($result === 'not_available') {
        $_SESSION['flash'] = ['icon'=>'warning','title'=>'Gagal','text'=>'Kendaraan Sedang Digunakan!'];
    } else {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal','text'=>'Tidak dapat approve request!'];
    }
    header("Location: approved.php");
    exit;
}

// Reject Request
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    if (rejectRequest($id)) {
        $_SESSION['flash'] = ['icon'=>'info','title'=>'Ditolak','text'=>'Request berhasil ditolak!'];
    } else {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal','text'=>'Tidak dapat tolak request!'];
    }
    header("Location: approved.php");
    exit;
}

$requests = getKendaraanRequestPending();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Approve Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include "navbar.php"; ?>

<div class="container-fluid p-4">
    <h2 class="mb-4 text-primary">✅ Daftar Request Kendaraan</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Request Kendaraan Menunggu Persetujuan</div>
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Kendaraan</th>
                        <th width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['user_nama']) ?></td>
                                <td><?= htmlspecialchars($row['plat_nomor'].' - '.$row['merek']) ?></td>
                                <td>
                                    <a href="?approve=<?= $row['request_id'] ?>" class="btn btn-success btn-sm btn-approve me-2">
                                        ✅ Approve
                                    </a>
                                    <a href="?reject=<?= $row['request_id'] ?>" class="btn btn-danger btn-sm btn-reject">
                                        ❌ Tolak
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada request kendaraan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "script.php"; ?>

<script>
// SweetAlert untuk Approve / Reject
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-approve, .btn-reject').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const action = this.classList.contains('btn-approve') ? 'Setujui' : 'Tolak';
            Swal.fire({
                title: `Yakin ${action} request ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Ya, ${action}`,
                cancelButtonText: 'Batal',
                confirmButtonColor: action === 'Setujui' ? '#28a745' : '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});
</script>
</body>
</html>
