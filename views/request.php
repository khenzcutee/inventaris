<?php
session_start();
require "../functions/functions.php";

if (!isset($_SESSION['logged_in'])|| !in_array($_SESSION['id_roles'], [3,5])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kendaraan = $_POST['id_kendaraan'];

    $result = requestKendaraan($user_id, $id_kendaraan);
    if ($result === true) {
        $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>'Request berhasil dikirim!'];
    } elseif ($result === 'not_available') {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal','text'=>'Kendaraan tidak tersedia!'];
    } else {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Error','text'=>'Terjadi kesalahan!'];
    }
    header("Location: request.php");
    exit;
}

$kendaraan = mysqli_query($conn, "SELECT * FROM kendaraan WHERE id_status IN (1,3)");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Request Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include "navbar.php"; ?>
<?php include "sidebar.php"; ?>

<div class="col-md-10 p-4">
    <h2 class="mb-4 text-primary">🚗 Request Kendaraan</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Form Request Kendaraan</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_kendaraan" class="form-label">Pilih Kendaraan</label>
                    <select name="id_kendaraan" id="id_kendaraan" class="form-select" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        <?php while($row = mysqli_fetch_assoc($kendaraan)): ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['plat_nomor']) ?> - 
                                <?= htmlspecialchars($row['merek']) ?> 
                                (<?= htmlspecialchars($row['jenis_kendaraan']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Kirim Request</button>
            </form>
        </div>
    </div>
</div>

<?php include "script.php"; ?>
</body>
</html>
