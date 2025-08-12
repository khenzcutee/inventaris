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
    
}

function getCount($table) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM `$table`");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'];
}

function login($data) {
    global $conn;
    
    $username = trim($data['username']);
    $password = trim($data['password']);
    
    // Gunakan prepared statement
    $stmt = $conn->prepare("SELECT id, password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Verifikasi password hash
        if (password_verify($password, $row['password'])) {
            return true; // Login sukses
        }
    }
    return false; // Login gagal
}

function logout() {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit;
}
?>