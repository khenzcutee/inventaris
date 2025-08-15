<?php
session_start();
require "../functions/functions.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit;
}

$type = $_GET['type'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($type === 'user') {
        if (tambahUser($_POST)) {
            echo "User berhasil ditambahkan!";
        } else {
            echo "Gagal menambahkan user!";
        }
    } elseif ($type === 'kendaraan') {
        if (tambahKendaraan($_POST, $_FILES)) {
            echo "Kendaraan berhasil ditambahkan!";
        } else {
            echo "Gagal menambahkan kendaraan!";
        }
    } elseif ($type === 'pemakaian') {
        if (tambahPemakaian($_POST)) {
            echo "Pemakaian berhasil ditambahkan!";
        } else {
            echo "Gagal menambahkan pemakaian!";
        }
    } else {
        echo "Form tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data</title>
</head>
<body>
<h2>Form Tambah <?= ucfirst($type) ?></h2>
<form action="" method="post" enctype="multipart/form-data">
<?php
if ($type === 'user') {
?>
    <input type="text" name="nama" placeholder="Nama" required><br>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <select name="id_roles"><?= getRolesOptions(); ?></select><br>
    <select name="id_divisi"><?= getDivisiOptions(); ?></select><br>
    <button type="submit">Simpan</button>
<?php
} elseif ($type === 'kendaraan') {
?>
    <input type="text" name="plat_nomor" placeholder="Plat Nomor" required><br>
    <input type="text" name="nomor_stnk" placeholder="Nomor STNK" required><br>
    <input type="text" name="bahan_bakar" placeholder="Bahan Bakar"><br>
    <input type="text" name="warna" placeholder="Warna"><br>
    <input type="text" name="jenis_kendaraan" placeholder="Jenis Kendaraan"><br>
    <input type="text" name="merek" placeholder="Merek"><br>
    <input type="number" name="kilometer" placeholder="Kilometer"><br>
    <input type="file" name="gambar"><br>
    <select name="id_lokasi"><?= getLokasiOptions(); ?></select><br>
    <select name="id_status"><?= getStatusOptions(); ?></select><br>
    <button type="submit">Simpan</button>
<?php
} elseif ($type === 'pemakaian') {
?>
    <select name="id_user"><?= getUserOptions(); ?></select><br>
    <select name="id_inventaris"><?= getKendaraanOptions(); ?></select><br>
    <input type="date" name="tanggal_keluar" required><br>
    <input type="date" name="tanggal_masuk"><br>
    <select name="id_status"><?= getStatusOptions(); ?></select><br>
    <button type="submit">Simpan</button>
<?php
} else {
    echo "Form tidak tersedia.";
}
?>
</form>
</body>
</html>
