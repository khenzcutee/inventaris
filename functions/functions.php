<?php 
// DATABASE CONNECTION
$host = "localhost";
$username = "root";
$password = "";
$database = "inventaris";
$conn = mysqli_connect($host,$username,$password,$database);

// CHECK CONNECTION
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    };

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
        };
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
        }
        return $result;
}

function getKendaraanLengkap() {
    return query("
        SELECT k.id, k.plat_nomor, k.nomor_stnk, k.bahan_bakar, k.warna, 
               k.jenis_kendaraan, k.merek, k.kilometer, k.gambar,
               l.nama_lokasi, s.nama_status
        FROM kendaraan k
        INNER JOIN lokasi l ON k.id_lokasi = l.id
        INNER JOIN status s ON k.id_status = s.id
    ");
}

function getUserLengkap() {
    return query("
        SELECT u.id, u.nama, u.username, d.nama_divisi, r.nama_roles
        FROM user u
        INNER JOIN divisi d ON u.id_divisi = d.id
        INNER JOIN roles r ON u.id_roles = r.id
    ");
}

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

function getCount($table) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM `$table`");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'];
}

function getCountPemakaian($table) {
    global $conn;
    
    // Pastikan nama tabel aman (whitelist)
    $allowedTables = ['pemakaian', 'kendaraan', 'user'];
    if (!in_array($table, $allowedTables)) {
        return 0;
    }

    $sql = "SELECT COUNT(*) as total FROM $table WHERE id_status = 2";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
    
    return 0;
}

function getCountRequest($table) {
    global $conn;
    
    // Pastikan nama tabel aman (whitelist)
    $allowedTables = ['pemakaian', 'kendaraan', 'user'];
    if (!in_array($table, $allowedTables)) {
        return 0;
    }

    $sql = "SELECT COUNT(*) as total FROM $table WHERE id_status = 6";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
    
    return 0;
}

function getCountKendaraanTersedia($table) {
    global $conn;
    
    // Pastikan nama tabel aman (whitelist)
    $allowedTables = ['pemakaian', 'kendaraan', 'user'];
    if (!in_array($table, $allowedTables)) {
        return 0;
    }

    $sql = "SELECT COUNT(*) as total FROM $table WHERE id_status = 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
    
    return 0;
}

function uploadGambar($fileInputName = 'gambar') {
    // Lokasi folder penyimpanan gambar
    $targetDir = "../assets/images/kendaraan/";

    // Ambil informasi file
    $fileName = $_FILES[$fileInputName]['name'];
    $fileTmp  = $_FILES[$fileInputName]['tmp_name'];
    $fileSize = $_FILES[$fileInputName]['size'];
    $fileError = $_FILES[$fileInputName]['error'];

    // Cek apakah ada file yang diupload
    if ($fileError === 4) {
        // Tidak ada file yang diupload
        return 'default.png'; // Bisa diganti default image
    }

    // Ekstensi file
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png'];

    // Validasi ekstensi
    if (!in_array($fileExt, $allowedExt)) {
        echo "<script>alert('Hanya boleh upload file JPG, JPEG, atau PNG!');</script>";
        return false;
    }

    // Validasi ukuran (maksimal 2MB)
    if ($fileSize > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran file maksimal 2MB!');</script>";
        return false;
    }

    // Generate nama file baru agar unik
    $newFileName = uniqid('img_', true) . '.' . $fileExt;

    // Pindahkan file ke folder tujuan
    if (!move_uploaded_file($fileTmp, $targetDir . $newFileName)) {
        echo "<script>alert('Gagal mengupload gambar!');</script>";
        return false;
    }

    return $newFileName; // Return nama file baru
}

function getDivisiOptions($selectedId = null) {
    global $conn;

    // SESUAIKAN nama kolom PK di tabel roles:
    // Jika di skema kamu PK=roles.id, pakai ini:
    $sql = "SELECT id, nama_divisi FROM divisi ORDER BY nama_divisi";
    // Jika ternyata PK-mu bernama id_roles, ganti jadi:
    // $sql = "SELECT id_roles AS id, nama_roles FROM roles ORDER BY nama_roles";

    $res = mysqli_query($conn, $sql);
    if (!$res) return ''; // atau tangani error sesuai kebutuhan

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars($row['nama_divisi'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$name}</option>";
    }
    return $html;
}

function getRolesOptions($selectedId = null) {
    global $conn;

    // SESUAIKAN nama kolom PK di tabel roles:
    // Jika di skema kamu PK=roles.id, pakai ini:
    $sql = "SELECT id, nama_roles FROM roles ORDER BY nama_roles";
    // Jika ternyata PK-mu bernama id_roles, ganti jadi:
    // $sql = "SELECT id_roles AS id, nama_roles FROM roles ORDER BY nama_roles";

    $res = mysqli_query($conn, $sql);
    if (!$res) return ''; // atau tangani error sesuai kebutuhan

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars($row['nama_roles'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$name}</option>";
    }
    return $html;
}

function getLokasiOptions($selectedId = null) {
    global $conn;

    // SESUAIKAN nama kolom PK di tabel lokasi:
    // Jika di skema kamu PK=lokasi.id, pakai ini:
    $sql = "SELECT id, nama_lokasi FROM lokasi ORDER BY nama_lokasi";
    // Jika ternyata PK-mu bernama id_lokasi, ganti jadi:
    // $sql = "SELECT id_lokasi AS id, nama_lokasi FROM lokasi ORDER BY nama_lokasi";

    $res = mysqli_query($conn, $sql);
    if (!$res) return ''; // atau tangani error sesuai kebutuhan

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars($row['nama_lokasi'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$name}</option>";
    }
    return $html;
}

function getStatusOptions($selectedId = null) {
    global $conn;

    // SESUAIKAN nama kolom PK di tabel status:
    // Jika di skema kamu PK=status.id, pakai ini:
    $sql = "SELECT id, nama_status FROM status ORDER BY nama_status";
    // Jika ternyata PK-mu bernama id_status, ganti jadi:
    // $sql = "SELECT id_status AS id, nama_status FROM status ORDER BY nama_status";

    $res = mysqli_query($conn, $sql);
    if (!$res) return ''; // atau tangani error sesuai kebutuhan

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$name}</option>";
    }
    return $html;
}

function getStatusEditOptions($selectedId = null) {
    global $conn;

    // SESUAIKAN nama kolom PK di tabel status:
    // Jika di skema kamu PK=status.id, pakai ini:
    $sql = "SELECT id, nama_status FROM status WHERE id IN (2,3,5) ORDER BY nama_status";
    // Jika ternyata PK-mu bernama id_status, ganti jadi:
    // $sql = "SELECT id_status AS id, nama_status FROM status ORDER BY nama_status";

    $res = mysqli_query($conn, $sql);
    if (!$res) return ''; // atau tangani error sesuai kebutuhan

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$name}</option>";
    }
    return $html;
}

function getStatusTambahOptions($selectedId = null) {
    global $conn;

    // SESUAIKAN nama kolom PK di tabel status:
    // Jika di skema kamu PK=status.id, pakai ini:
    $sql = "SELECT id, nama_status FROM status WHERE id IN (2) ORDER BY nama_status";
    // Jika ternyata PK-mu bernama id_status, ganti jadi:
    // $sql = "SELECT id_status AS id, nama_status FROM status ORDER BY nama_status";

    $res = mysqli_query($conn, $sql);
    if (!$res) return ''; // atau tangani error sesuai kebutuhan

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars($row['nama_status'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$name}</option>";
    }
    return $html;
}

function getUserOptions($selectedId = null) {
    global $conn;

    // Ambil id dan nama user sesuai tabel user
    $sql = "SELECT id, nama FROM user ORDER BY nama ASC";
    $res = mysqli_query($conn, $sql);

    if (!$res) {
        return ''; // jika query gagal, return kosong
    }

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$name}</option>";
    }
    return $html;
}

function getKendaraanOptions($selectedId = null) {
    global $conn;

    // Ambil id dan plat nomor sesuai tabel kendaraan
    $sql = "SELECT id, plat_nomor FROM kendaraan WHERE id_status = 1 ORDER BY plat_nomor ASC";
    $res = mysqli_query($conn, $sql);

    if (!$res) {
        return '';
    }

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $plat = htmlspecialchars($row['plat_nomor'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$plat}</option>";
    }
    return $html;
}

function getKendaraanOptionsUpdate($selectedId = null) {
    global $conn;

    $sql = "SELECT id, plat_nomor FROM kendaraan WHERE id_status IN (1, 2, 5) ORDER BY plat_nomor ASC";
    $res = mysqli_query($conn, $sql);

    if (!$res) {
        return '';
    }

    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $id   = (int)$row['id'];
        $plat = htmlspecialchars($row['plat_nomor'], ENT_QUOTES, 'UTF-8');
        $sel  = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
        $html .= "<option value=\"{$id}\"{$sel}>{$plat}</option>";
    }
    return $html;
}

function getKendaraanRequestPending() {
    global $conn;

    $sql = "SELECT r.id AS request_id, u.nama AS user_nama, k.plat_nomor, k.merek
            FROM request r
            JOIN user u ON r.id_user = u.id
            JOIN kendaraan k ON r.id_kendaraan = k.id
            WHERE r.id_status = 8  -- Pending
            ORDER BY r.id DESC";

    $res = mysqli_query($conn, $sql);
    $data = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
    }
    return $data;
}

function getKendaraanRequestList($selectedId = null) {
    global $conn;

    // Ambil kendaraan yang tersedia (status 1 = Tersedia)
    $sql = "SELECT id, plat_nomor, merek, jenis_kendaraan FROM kendaraan ORDER BY plat_nomor ASC";
    $res = mysqli_query($conn, $sql);

    $html = '';
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $id   = (int)$row['id'];
            $plat = htmlspecialchars($row['plat_nomor'], ENT_QUOTES, 'UTF-8');
            $merek = htmlspecialchars($row['merek'], ENT_QUOTES, 'UTF-8');
            $jenis = htmlspecialchars($row['jenis_kendaraan'], ENT_QUOTES, 'UTF-8');

            $sel = ($selectedId !== null && (int)$selectedId === $id) ? ' selected' : '';
            $html .= "<option value=\"$id\"$sel>$plat - $merek ($jenis)</option>";
        }
    }
    return $html;
}

function getAllKendaraanOptions($selectedId = null) {
    global $conn;
    $sql = "SELECT id, plat_nomor, merek, jenis_kendaraan FROM kendaraan ORDER BY plat_nomor ASC";
    $res = mysqli_query($conn, $sql);

    $html = '';
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $id = (int)$row['id'];
            $sel = ($selectedId !== null && $selectedId == $id) ? ' selected' : '';
            $html .= "<option value='{$id}'{$sel}>"
                  . htmlspecialchars($row['plat_nomor']) . " - "
                  . htmlspecialchars($row['merek']) . " ("
                  . htmlspecialchars($row['jenis_kendaraan']) . ")</option>";
        }
    }
    return $html;
}

function getPemakaianSedangDipakaiOptions() {
    global $conn;
    $sql = "SELECT p.id, k.plat_nomor, u.nama
            FROM pemakaian p
            INNER JOIN kendaraan k ON p.id_inventaris = k.id
            INNER JOIN `user` u ON p.id_user = u.id
            WHERE p.id_status = 2";
    $res = mysqli_query($conn, $sql);
    $options = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $options .= "<option value='{$row['id']}'>
                        {$row['plat_nomor']} - {$row['nama']}
                     </option>";
    }
    return $options;
}

function tambahuser($data) {
    global $conn;
    $nama = mysqli_escape_string($conn, $data['nama']);
    $username = mysqli_escape_string($conn, $data['username']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $id_divisi = mysqli_escape_string($conn, $data['id_divisi']);
    $id_roles = mysqli_escape_string($conn, $data['id_roles']);
    $user_id = $_SESSION['user_id'];
    
    $query = "INSERT INTO user (nama, username, password, id_divisi, id_roles, created_at, updated_at, created_by, updated_by)
              VALUES ('$nama', '$username', '$password', $id_divisi, $id_roles, NOW(), NOW(), $user_id, $user_id)";

    return mysqli_query($conn, $query);
}

function tambahKendaraan($data, $files) {
    global $conn;

    // Ambil data form dengan sanitasi
    $plat_nomor      = htmlspecialchars($data['plat_nomor']);
    $nomor_stnk      = htmlspecialchars($data['nomor_stnk']);
    $bahan_bakar     = htmlspecialchars($data['bahan_bakar']);
    $warna           = htmlspecialchars($data['warna']);
    $jenis_kendaraan = htmlspecialchars($data['jenis_kendaraan']);
    $merek           = htmlspecialchars($data['merek']);
    $kilometer       = (int)$data['kilometer'];
    $id_lokasi       = (int)$data['id_lokasi'];
    $id_status       = (int)$data['id_status'];
    $user_id         = $_SESSION['user_id'];

    $gambar = uploadGambar('gambar'); 
    if ($gambar === false) {
        return false; // Jika gagal upload, hentikan proses
    }

    // ✅ Query Insert
    $query = "INSERT INTO kendaraan (
                plat_nomor, nomor_stnk, bahan_bakar, warna, jenis_kendaraan, merek, kilometer, gambar, id_lokasi, id_status, created_at, updated_at, created_by, updated_by
              ) VALUES (
                '$plat_nomor', '$nomor_stnk', '$bahan_bakar', '$warna', '$jenis_kendaraan', '$merek', $kilometer, '$gambar', $id_lokasi, $id_status, NOW(), NOW(), $user_id, $user_id
              )";

    return mysqli_query($conn, $query);
}

function tambahPemakaian($data) {
    global $conn;

    $id_user        = (int)$data['id_user'];
    $id_inventaris  = (int)$data['id_inventaris']; // ini harus sesuai dengan id kendaraan
    $tanggal_keluar = mysqli_real_escape_string($conn, $data['tanggal_keluar']);
    $id_status      = (int)$data['id_status']; // status baru untuk kendaraan
    $user_id        = (int)$_SESSION['user_id'];

    // Mulai transaksi supaya aman
    mysqli_begin_transaction($conn);

    try {
        // 1. Insert ke pemakaian
        $insertPemakaian = "INSERT INTO pemakaian (id_user, id_inventaris, tanggal_keluar, id_status, created_at, updated_at, created_by, updated_by)
                            VALUES ($id_user, $id_inventaris, '$tanggal_keluar', 2, NOW(), NOW(), $user_id, $user_id)";
        if (!mysqli_query($conn, $insertPemakaian)) {
            throw new Exception("Gagal menambahkan pemakaian: " . mysqli_error($conn));
        }

        // 2. Update status di tabel kendaraan
        $updateStatus = "UPDATE kendaraan SET id_status = 2, updated_at = NOW(), updated_by = $user_id WHERE id = $id_inventaris";
        if (!mysqli_query($conn, $updateStatus)) {
            throw new Exception("Gagal update status kendaraan: " . mysqli_error($conn));
        }

        // Commit transaksi
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        // Rollback kalau ada error
        mysqli_rollback($conn);
        error_log($e->getMessage());
        return false;
    }
}

// === FUNGSI REQUEST KENDARAAN ===
function requestKendaraan($user_id, $id_kendaraan) {
    global $conn;

    $user_id = (int)$user_id;
    $id_kendaraan = (int)$id_kendaraan;

    // Cek apakah sudah ada request Pending untuk kendaraan ini dari user yang sama
    $cek = mysqli_query($conn, "SELECT id FROM request WHERE id_user = $user_id AND id_kendaraan = $id_kendaraan AND id_status = 8");
    if ($cek && mysqli_num_rows($cek) > 0) {
        return 'duplicate';
    }

    $sql = "INSERT INTO request (id_user, id_kendaraan, id_status, tanggal_request, created_at, updated_at, created_by, updated_by)
            VALUES ($user_id, $id_kendaraan, 8, NOW(), NOW(), NOW(), $user_id, $user_id)";

    return mysqli_query($conn, $sql) ? true : false;
}

// === FUNGSI APPROVE REQUEST ===
function approveRequest($id_request) {
    global $conn;
    $id_request = (int)$id_request;

    // Ambil data request
    $q = mysqli_query($conn, "SELECT id_user, id_kendaraan FROM request WHERE id = $id_request AND id_status = 8");
    if (!$q || mysqli_num_rows($q) === 0) {
        return false; // request tidak ditemukan atau bukan Pending
    }
    $data = mysqli_fetch_assoc($q);
    $id_kendaraan = (int)$data['id_kendaraan'];
    $id_user = (int)$data['id_user'];

    // Cek status kendaraan
    $cek = mysqli_query($conn, "SELECT id_status FROM kendaraan WHERE id = $id_kendaraan");
    if (!$cek || mysqli_num_rows($cek) === 0) {
        return false;
    }
    $kendaraan = mysqli_fetch_assoc($cek);

    if ((int)$kendaraan['id_status'] !== 1) {
        return 'not_available'; // kendaraan sedang tidak tersedia
    }

    mysqli_begin_transaction($conn);

    // Update request ke Approved (9)
    $sql1 = "UPDATE request SET id_status = 9, updated_at = NOW() WHERE id = $id_request";
    if (!mysqli_query($conn, $sql1)) {
        mysqli_rollback($conn);
        return false;
    }

    // Tambahkan ke tabel pemakaian
    $sql2 = "INSERT INTO pemakaian (id_user, id_inventaris, tanggal_keluar, tanggal_masuk, id_status, created_at, updated_at, created_by, updated_by)
             VALUES ($id_user, $id_kendaraan, CURDATE(), '0000-00-00', 2, NOW(), NOW(), $id_user, $id_user)";
    if (!mysqli_query($conn, $sql2)) {
        mysqli_rollback($conn);
        return false;
    }

    // Update kendaraan jadi Sedang Dipakai (2)
    $sql3 = "UPDATE kendaraan SET id_status = 2 WHERE id = $id_kendaraan";
    if (!mysqli_query($conn, $sql3)) {
        mysqli_rollback($conn);
        return false;
    }

    mysqli_commit($conn);
    return true;
}

// === FUNGSI TOLAK REQUEST ===
function rejectRequest($id_request) {
    global $conn;
    $id_request = (int)$id_request;

    mysqli_begin_transaction($conn);

    // Ambil data request
    $q = mysqli_query($conn, "SELECT id_user, id_kendaraan FROM request WHERE id = $id_request AND id_status = 8");
    if (!$q || mysqli_num_rows($q) === 0) {
        return false; // request tidak ditemukan atau bukan Pending
    }
    $data = mysqli_fetch_assoc($q);
    $id_kendaraan = (int)$data['id_kendaraan'];
    $id_user = (int)$data['id_user'];

    // Update request menjadi Ditolak (id_status = 7)
    $sql1 = "UPDATE request SET id_status = 7, updated_at = NOW() WHERE id = $id_request"; // 7 = Ditolak
    if (!mysqli_query($conn, $sql1)) {
        mysqli_rollback($conn);
        return false;
    }

    // Tambahkan ke tabel pemakaian
    $sql2 = "INSERT INTO pemakaian (id_user, id_inventaris, tanggal_keluar, tanggal_masuk, id_status, created_at, updated_at, created_by, updated_by)
             VALUES ($id_user, $id_kendaraan, CURDATE(), '0000-00-00', 7, NOW(), NOW(), $id_user, $id_user)";
    if (!mysqli_query($conn, $sql2)) {
        mysqli_rollback($conn);
        return false;
    }

    // Tidak perlu ubah status kendaraan karena belum diubah saat request
    mysqli_commit($conn);
    return true;
}

function prosesPengembalian($id_pemakaian, $tanggal_masuk) {
    global $conn;
    mysqli_begin_transaction($conn);

    // Ambil id kendaraan
    $q = mysqli_query($conn, "SELECT id_inventaris FROM pemakaian WHERE id=$id_pemakaian");
    if (!$q || mysqli_num_rows($q) === 0) {
        mysqli_rollback($conn);
        return false;
    }
    $row = mysqli_fetch_assoc($q);
    $id_kendaraan = (int)$row['id_inventaris'];

    // Update pemakaian (tanggal_masuk + status selesai = 5)
    $sql1 = "UPDATE pemakaian SET tanggal_masuk='$tanggal_masuk', id_status=5 WHERE id=$id_pemakaian";
    if (!mysqli_query($conn, $sql1)) {
        mysqli_rollback($conn);
        return false;
    }

    // Update kendaraan jadi Tersedia (status = 1)
    $sql2 = "UPDATE kendaraan SET id_status=1 WHERE id=$id_kendaraan";
    if (!mysqli_query($conn, $sql2)) {
        mysqli_rollback($conn);
        return false;
    }

    mysqli_commit($conn);
    return true;
}

function tambahDivisi($data) {
    global $conn;
    $nama_divisi = htmlspecialchars($data['nama_divisi']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO divisi (nama_divisi, created_at, updated_at, created_by, updated_by)
              VALUES ('$nama_divisi', NOW(), NOW(), $user_id, $user_id)";
    
    return mysqli_query($conn, $query);
}

function tambahLokasi($data) {
    global $conn;
    $nama_lokasi = htmlspecialchars($data['nama_lokasi']);
    $alamat = htmlspecialchars($data['alamat']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO lokasi (nama_lokasi, alamat, created_At, updated_at, created_by, updated_by)
              VALUES ('$nama_lokasi', '$alamat', NOW(), NOW(), $user_id, $user_id)";
    
    return mysqli_query($conn, $query);

}

function tambahRoles($data) {
    global $conn;
    $nama_roles = htmlspecialchars($data['nama_roles']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO roles (nama_roles, created_at, updated_at, created_by, updated_by)
              VALUES ('$nama_roles', NOW(), NOW(), $user_id, $user_id)";
    
    return mysqli_query($conn, $query);
}

function tambahStatus($data) {
    global $conn;
    $nama_status = htmlspecialchars($data['nama_status']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO status (nama_status, created_at, updated_at, created_by, updated_by)
              VALUES ('$nama_status', NOW(), NOW(), $user_id, $user_id)";

    return mysqli_query($conn , $query);
}

function getDataView($page) {
    global $conn;
    $sql = "";

    if ($page == 'user') {
        $sql = "SELECT u.id, u.nama, u.username, d.nama_divisi, r.nama_roles 
                FROM `user` u
                INNER JOIN divisi d ON u.id_divisi = d.id
                INNER JOIN roles r ON u.id_roles = r.id";
    } 
    else if ($page == 'kendaraan') {
        $sql = "SELECT k.id, k.plat_nomor, k.nomor_stnk, k.bahan_bakar,
                       k.warna, k.jenis_kendaraan, k.merek, k.kilometer,
                       l.nama_lokasi, s.nama_status
                FROM kendaraan k
                INNER JOIN lokasi l ON k.id_lokasi = l.id
                INNER JOIN status s ON k.id_status = s.id";
    } 
    else if ($page == 'pemakaian') {
    $sql = "SELECT p.id, u.nama AS nama_user, k.plat_nomor, p.tanggal_keluar, s.nama_status
            FROM pemakaian p
            INNER JOIN `user` u ON p.id_user = u.id
            INNER JOIN kendaraan k ON p.id_inventaris = k.id
            INNER JOIN status s ON p.id_status = s.id
            WHERE p.id_status NOT IN (5,7,6)
            ORDER BY tanggal_keluar DESC";
    }
    else if ($page == 'pemakaianSelesai') {
    $sql = "SELECT p.id, u.nama AS nama_user, k.plat_nomor, p.tanggal_keluar, p.tanggal_masuk, s.nama_status
            FROM pemakaian p
            INNER JOIN `user` u ON p.id_user = u.id
            INNER JOIN kendaraan k ON p.id_inventaris = k.id
            INNER JOIN status s ON p.id_status = s.id
            WHERE p.id_status IN (5,7)
            ORDER BY tanggal_keluar DESC";
    }
    else if ($page == 'divisi') {
        $sql = "SELECT * FROM divisi";
    }
    else if ($page == 'roles') {
        $sql = "SELECT * FROM roles";
    }
    else if ($page == 'status') {
        $sql = "SELECT * FROM status";
    }
    else if ($page == 'lokasi') {
        $sql = "SELECT * FROM lokasi";
    } 
    else {
        return [];
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        error_log("Query Error: " . mysqli_error($conn)); // Untuk debug di log
        return []; // Hindari fatal error
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function getDetailData($type, $id) {
    global $conn;
    $id = (int)$id;
    $sql = "";

    if ($type == 'user') {
        $sql = "SELECT u.id, u.nama, u.username, d.nama_divisi, r.nama_roles, u.created_at, u.updated_at
                FROM `user` u
                INNER JOIN divisi d ON u.id_divisi = d.id
                INNER JOIN roles r ON u.id_roles = r.id
                WHERE u.id = $id";
    } elseif ($type == 'kendaraan') {
        $sql = "SELECT k.id, k.plat_nomor, k.nomor_stnk, k.bahan_bakar, k.warna,
                       k.jenis_kendaraan, k.merek, k.kilometer, l.nama_lokasi, s.nama_status,
                       k.created_at, k.updated_at
                FROM kendaraan k
                INNER JOIN lokasi l ON k.id_lokasi = l.id
                INNER JOIN status s ON k.id_status = s.id
                WHERE k.id = $id";
    } elseif ($type == 'pemakaian') {
        $sql = "SELECT p.id, u.nama AS nama_user, k.plat_nomor, p.tanggal_keluar, p.tanggal_masuk, 
                       s.nama_status, p.created_at, p.created_at, p.updated_at
                FROM pemakaian p
                INNER JOIN `user` u ON p.id_user = u.id
                INNER JOIN kendaraan k ON p.id_inventaris = k.id
                INNER JOIN status s ON p.id_status = s.id
                WHERE p.id = $id";
    } elseif ($type == 'pemakaianSelesai') {
        $sql = "SELECT p.id, u.nama AS nama_user, k.plat_nomor, p.tanggal_keluar, p.tanggal_masuk, 
                       s.nama_status, p.created_at, p.updated_at
                FROM pemakaian p
                INNER JOIN `user` u ON p.id_user = u.id
                INNER JOIN kendaraan k ON p.id_inventaris = k.id
                INNER JOIN status s ON p.id_status = s.id
                WHERE p.id = $id";
    } elseif ($type == 'divisi') {
        $sql = "SELECT id, nama_divisi FROM divisi WHERE id = $id";
    } elseif ($type == 'roles') {
        $sql = "SELECT id, nama_roles FROM roles WHERE id = $id";
    } elseif ($type == 'status') {
        $sql = "SELECT id, nama_status FROM status WHERE id = $id";
    } elseif ($type == 'lokasi') {
        $sql = "SELECT id, nama_lokasi, alamat FROM lokasi WHERE id = $id";
    } else {
        return null; // Jika type tidak dikenali
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }
    return mysqli_fetch_assoc($result);
}

function updateData($type, $id, $data) {
    global $conn;
    $id = (int)$id;

    // --- USER ---
    if ($type === 'user') {
        $nama      = mysqli_real_escape_string($conn, $data['nama']);
        $username  = mysqli_real_escape_string($conn, $data['username']);
        $id_divisi = (int)$data['id_divisi'];
        $id_roles  = (int)$data['id_roles'];

        $sql = "UPDATE `user`
                SET nama='$nama', username='$username', id_divisi=$id_divisi, id_roles=$id_roles
                WHERE id=$id";
        return mysqli_query($conn, $sql);
    }

    // --- KENDARAAN ---
    if ($type === 'kendaraan') {
        $plat_nomor      = mysqli_real_escape_string($conn, $data['plat_nomor']);
        $nomor_stnk      = mysqli_real_escape_string($conn, $data['nomor_stnk']);
        $bahan_bakar     = mysqli_real_escape_string($conn, $data['bahan_bakar']);
        $warna           = mysqli_real_escape_string($conn, $data['warna']);
        $jenis_kendaraan = mysqli_real_escape_string($conn, $data['jenis_kendaraan']);
        $merek           = mysqli_real_escape_string($conn, $data['merek']);
        $kilometer       = (int)$data['kilometer'];
        $id_lokasi       = (int)$data['id_lokasi'];
        $id_status       = (int)$data['id_status']; // pastikan 1..3

        $sql = "UPDATE kendaraan
                SET plat_nomor='$plat_nomor', nomor_stnk='$nomor_stnk', bahan_bakar='$bahan_bakar', warna='$warna',
                    jenis_kendaraan='$jenis_kendaraan', merek='$merek', kilometer=$kilometer,
                    id_lokasi=$id_lokasi, id_status=$id_status
                WHERE id=$id";
        return mysqli_query($conn, $sql);
    }

    // --- PEMAKAIAN ---
    elseif ($type === 'pemakaian') {
    // Ambil data lama untuk validasi
    $q = mysqli_query($conn, "SELECT id_inventaris, tanggal_keluar, tanggal_masuk, id_status
                               FROM pemakaian WHERE id=$id");
    if (!$q || mysqli_num_rows($q) === 0) {
        return false; // Data tidak ditemukan
    }
    $old = mysqli_fetch_assoc($q);
    $id_inventaris = (int)$old['id_inventaris'];
    $statusLama    = (int)$old['id_status'];

    // Jika status lama = 5 → tidak boleh edit
    if ($statusLama === 5) {
        return 'not_allowed'; // Kembalikan flag khusus
    }

    // Lanjutkan update
    $new_status = isset($data['id_status']) ? (int)$data['id_status'] : 2;

    if ($new_status === 5) $new_status = 1;

    if (!in_array($new_status, [1,2,3], true)) {
        return false;
    }

    mysqli_begin_transaction($conn);

    // Tentukan tanggal_masuk
    if (!empty($data['tanggal_masuk'])) {
        $tanggal_masuk = mysqli_real_escape_string($conn, $data['tanggal_masuk']);
    } else {
        $tanggal_masuk = ($new_status === 2)
            ? mysqli_real_escape_string($conn, $old['tanggal_masuk'])
            : date('Y-m-d');
    }

    // Update pemakaian
    $sql1 = "UPDATE pemakaian
             SET id_status=$new_status, tanggal_masuk='$tanggal_masuk'
             WHERE id=$id";
    if (!mysqli_query($conn, $sql1)) {
        mysqli_rollback($conn);
        return false;
    }

    // Update status kendaraan
    $kend_status = ($new_status === 2) ? 2 : 1;
    $sql2 = "UPDATE kendaraan SET id_status=$kend_status WHERE id=$id_inventaris";
    if (!mysqli_query($conn, $sql2)) {
        mysqli_rollback($conn);
        return false;
    }

    mysqli_commit($conn);
    return true;
}

    // --- DIVISI ---
    if ($type === 'divisi') {
        $nama_divisi = mysqli_real_escape_string($conn, $data['nama_divisi']);
        $sql = "UPDATE divisi SET nama_divisi='$nama_divisi' WHERE id=$id";
        return mysqli_query($conn, $sql);
    }

    // --- STATUS ---
    if ($type === 'status') {
        $nama_status = mysqli_real_escape_string($conn, $data['nama_status']);
        $sql = "UPDATE status SET nama_status='$nama_status' WHERE id=$id";
        return mysqli_query($conn, $sql);
    }

    // --- ROLES ---
    if ($type === 'roles') {
        $nama_roles = mysqli_real_escape_string($conn, $data['nama_roles']);
        $sql = "UPDATE roles SET nama_roles='$nama_roles' WHERE id=$id";
        return mysqli_query($conn, $sql);
    }

    // --- LOKASI ---
    if ($type === 'lokasi') {
        $nama_lokasi = mysqli_real_escape_string($conn, $data['nama_lokasi']);
        $alamat      = mysqli_real_escape_string($conn, $data['alamat']);
        $sql = "UPDATE lokasi SET nama_lokasi='$nama_lokasi', alamat='$alamat' WHERE id=$id";
        return mysqli_query($conn, $sql);
    }

    return false;
}

function deleteData($type, $id) {
    global $conn;
    $id = (int)$id;

    if ($type === 'user') {
        $sql = "DELETE FROM user WHERE id = $id";
        $ok  = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0 ? true : false;

    } else if ($type === 'kendaraan') {
        $sql = "DELETE FROM kendaraan WHERE id = $id";
        $ok  = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0 ? true : false;

    } else if ($type === 'pemakaian') {
    // Ambil id_status & id_inventaris sebelum hapus
    $cek = mysqli_query($conn, "SELECT id_status, id_inventaris FROM pemakaian WHERE id = $id");
    if (!$cek || mysqli_num_rows($cek) === 0) {
        return false; // Data tidak ada
    }
    $row = mysqli_fetch_assoc($cek);
    $id_status = (int)$row['id_status'];
    $id_inventaris = (int)$row['id_inventaris'];

    // Jika status selesai (5), tidak boleh hapus
    if ($id_status === 5) {
        return 'not_allowed';
    }

    // Hapus pemakaian
    $sql = "DELETE FROM pemakaian WHERE id = $id";
    if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
        // Setelah berhasil hapus, update status kendaraan menjadi 1 (Tersedia)
        $updateKendaraan = "UPDATE kendaraan SET id_status = 1 WHERE id = $id_inventaris";
        mysqli_query($conn, $updateKendaraan);
        return true;
    }

    return false;

    } else if ($type === 'pemakaianSelesai') {
        // Cek status dulu
        $cek = mysqli_query($conn, "SELECT id_status FROM pemakaian WHERE id = $id");
        if (!$cek || mysqli_num_rows($cek) === 0) {
            return false; // data tidak ada
        }
        $row = mysqli_fetch_assoc($cek);
        if ((int)$row['id_status'] === 5 || 7) {
            return 'not_allowed'; // status Selesai → tidak boleh hapus
        }

        $sql = "DELETE FROM pemakaian WHERE id = $id";
        $ok  = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0 ? true : false;

    } else if ($type === 'divisi') {
        $sql = "DELETE FROM divisi WHERE id = $id";
        $ok  = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0 ? true : false;

    } else if ($type === 'lokasi') {
        $sql = "DELETE FROM lokasi WHERE id = $id";
        $ok  = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0 ? true : false;

    } else if ($type === 'roles') {
        $sql = "DELETE FROM roles WHERE id = $id";
        $ok  = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0 ? true : false;

    } else if ($type === 'status') {
        $sql = "DELETE FROM status WHERE id = $id";
        $ok  = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0 ? true : false;

    } else {
        return false;
    }
}

function login($data) {
    global $conn;

    $username = trim($data['username']);
    $password = trim($data['password']);

    $stmt = $conn->prepare("SELECT id, username, password, id_roles FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Simpan session
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['id_roles'] = $row['id_roles'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_start();
    session_unset();
    session_destroy();
    session_start(); // mulai sesi baru untuk flash message
    $_SESSION['logout_success'] = true;
    header("Location: ../index.php");
    exit;
}
?>