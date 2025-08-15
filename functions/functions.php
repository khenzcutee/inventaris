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
    $sql = "SELECT id, plat_nomor FROM kendaraan ORDER BY plat_nomor ASC";
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
    $plat_nomor = htmlspecialchars($data['plat_nomor']);
    $nomor_stnk = htmlspecialchars($data['nomor_stnk']);
    $bahan_bakar = htmlspecialchars($data['bahan_bakar']);
    $warna = htmlspecialchars($data['warna']);
    $jenis_kendaraan = htmlspecialchars($data['jenis_kendaraan']);
    $merek = htmlspecialchars($data['merek']);
    $kilometer = (int)$data['kilometer'];
    $id_lokasi = (int)$data['id_lokasi'];
    $id_status = (int)$data['id_status'];
    $user_id = $_SESSION['user_id'];

    // Upload gambar
    $gambar = uploadGambar($files['gambar']);

    $query = "INSERT INTO kendaraan (plat_nomor, nomor_stnk, bahan_bakar, warna, jenis_kendaraan, merek, kilometer, gambar, id_lokasi, id_status, created_at, updated_at, created_by, updated_by)
              VALUES ('$plat_nomor', '$nomor_stnk', '$bahan_bakar', '$warna', '$jenis_kendaraan', '$merek', $kilometer, '$gambar', $id_lokasi, $id_status, NOW(), NOW(), $user_id, $user_id)";
    return mysqli_query($conn, $query);
}

function tambahPemakaian($data) {
    global $conn;

    $id_user        = (int)$data['id_user'];
    $id_inventaris  = (int)$data['id_inventaris']; // ini harus sesuai dengan id kendaraan
    $tanggal_keluar = mysqli_real_escape_string($conn, $data['tanggal_keluar']);
    $tanggal_masuk  = mysqli_real_escape_string($conn, $data['tanggal_masuk']);
    $id_status      = (int)$data['id_status']; // status baru untuk kendaraan
    $user_id        = (int)$_SESSION['user_id'];

    // Mulai transaksi supaya aman
    mysqli_begin_transaction($conn);

    try {
        // 1. Insert ke pemakaian
        $insertPemakaian = "INSERT INTO pemakaian (id_user, id_inventaris, tanggal_keluar, tanggal_masuk, id_status, created_at, updated_at, created_by, updated_by)
                            VALUES ($id_user, $id_inventaris, '$tanggal_keluar', '$tanggal_masuk', $id_status, NOW(), NOW(), $user_id, $user_id)";
        if (!mysqli_query($conn, $insertPemakaian)) {
            throw new Exception("Gagal menambahkan pemakaian: " . mysqli_error($conn));
        }

        // 2. Update status di tabel kendaraan
        $updateStatus = "UPDATE kendaraan SET id_status = $id_status, updated_at = NOW(), updated_by = $user_id WHERE id = $id_inventaris";
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
            INNER JOIN status s ON p.id_status = s.id";
    }
    else if ($page == 'divisi') {
        $sql = "SELECT * FROM divisi";
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


function login($data) {
    global $conn;

    $username = trim($data['username']);
    $password = trim($data['password']);

    $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE username = ?");
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