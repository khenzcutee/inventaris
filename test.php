<?php 
function getPemakaianLengkap() {
    return query("
        SELECT p.id, u.nama AS nama_user, k.plat_nomor, p.tanggal_keluar, s.nama_status
        FROM pemakaian p
        INNER JOIN user u ON p.id_user = u.id
        INNER JOIN kendaraan k ON p.id_inventaris = k.id
        INNER JOIN status s ON p.id_status = s.id
        ORDER BY p.tanggal_keluar DESC
    ");
}
?>