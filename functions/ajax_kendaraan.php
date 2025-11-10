<?php
require "functions.php";

// Jika hanya dikirim lokasi (untuk ambil jenis kendaraan)
if (isset($_GET['lokasi_id']) && !isset($_GET['jenis_kendaraan'])) {
    $id_lokasi = (int)$_GET['lokasi_id'];
    echo getJenisKendaraanByLokasi($id_lokasi);
    exit;
}

// Jika dikirim lokasi + jenis kendaraan (untuk ambil kendaraan)
if (isset($_GET['lokasi_id']) && isset($_GET['jenis_kendaraan'])) {
    $id_lokasi = (int)$_GET['lokasi_id'];
    $jenis = $_GET['jenis_kendaraan'];
    echo getKendaraanByLokasiJenis($id_lokasi, $jenis);
    exit;
}
?>
