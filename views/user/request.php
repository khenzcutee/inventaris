<?php
session_start();
require "../../functions/functions.php";

if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['id_roles'], [3,4,5])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout();
    exit;
}

$user_id = $_SESSION['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_kendaraan'])) {
    $id_kendaraan_list = $_POST['id_kendaraan'] ?? [];

    $success = 0;
    $duplicate = 0;
    $error = 0;

    foreach ($id_kendaraan_list as $id_kendaraan) {
        $id_kendaraan = (int)$id_kendaraan;
        if ($id_kendaraan > 0) {
            $result = requestKendaraan($user_id, $id_kendaraan);
            if ($result === true) $success++;
            elseif ($result === 'duplicate') $duplicate++;
            else $error++;
        }
    }

    if ($success > 0) {
        $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>"$success request berhasil dikirim!"];
    } elseif ($duplicate > 0) {
        $_SESSION['flash'] = ['icon'=>'warning','title'=>'Sudah Ada','text'=>"$duplicate kendaraan sudah pernah direquest!"];
    } else {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal','text'=>'Terjadi kesalahan saat mengirim request!'];
    }

    header("Location: request.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Request Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="../../assets/css/request_user.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="app-header d-flex justify-content-between align-items-center px-3">
    <div class="d-flex align-items-center gap-2">
        <img src="../../assets/images/logo-maxi.jpg" alt="Logo Maxi" class="app-logo">
        <h1 class="h5 m-0 text-white fw-semibold">Dashboard User</h1>
    </div>
    <form id="logoutForm" method="POST" class="d-inline m-0">
        <input type="hidden" name="logout" value="1">
        <button type="button" id="logoutBtn" class="btn btn-outline-light btn-sm border-0" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>
</header>

<div class="app-content">
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary w-100 mb-3">← Kembali ke Dashboard</a>
    </div>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['flash'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $_SESSION['flash']['icon'] ?>',
                title: '<?= $_SESSION['flash']['title'] ?>',
                text: '<?= $_SESSION['flash']['text'] ?>',
                confirmButtonColor: '#3085d6'
            });
        });
    </script>
    <?php unset($_SESSION['flash']); endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white fw-bold">Form Request Kendaraan</div>
        <div class="card-body">
            <form method="POST" id="requestForm">
                <div id="request-container">
                    <div class="request-item mb-3 border p-3 rounded bg-light">
                        <label class="form-label fw-bold">Pilih Lokasi</label>
                        <select class="form-select mb-2 lokasi" required>
                            <option value="">-- Pilih Lokasi --</option>
                            <?= getAllLokasiOptions(); ?>
                        </select>

                        <label class="form-label fw-bold">Pilih Jenis Kendaraan</label>
                        <select class="form-select mb-2 jenis" required disabled>
                            <option value="">-- Pilih Jenis Kendaraan --</option>
                        </select>

                        <label class="form-label fw-bold">Pilih Kendaraan</label>
                        <select name="id_kendaraan[]" class="form-select kendaraan" required disabled>
                            <option value="">-- Pilih Kendaraan --</option>
                        </select>

                        <button type="button" class="btn btn-danger btn-sm mt-2 removeRequest">Hapus</button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary w-100 mb-3" id="addRequest">
                    <i class="fas fa-plus-circle"></i> Tambah Kendaraan
                </button>
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-paper-plane"></i> Kirim Request
                </button>
            </form>
        </div>
    </div>
</div>

<?php include "navbar.php"?>
<script>
// ============ TAMBAH REQUEST BARU ============
document.getElementById('addRequest').addEventListener('click', function() {
    const container = document.getElementById('request-container');
    const firstItem = container.querySelector('.request-item');

    // Clone form pertama
    const clone = firstItem.cloneNode(true);

    // Reset semua input/select di dalam clone
    clone.querySelectorAll('select').forEach(sel => {
        sel.value = '';
        sel.disabled = true;
    });

    // Aktifkan kembali dropdown lokasi (biar bisa dipilih)
    const lokasiSelect = clone.querySelector('.lokasi');
    if (lokasiSelect) lokasiSelect.disabled = false;

    // Tambahkan tombol hapus (kalau belum ada)
    if (!clone.querySelector('.removeRequest')) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm mt-2 removeRequest';
        removeBtn.textContent = 'Hapus';
        clone.appendChild(removeBtn);
    }

    container.appendChild(clone);

    // Jalankan fungsi listener untuk dropdown baru
    initDropdownListeners(clone);
});

// ============ HAPUS REQUEST ============
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRequest')) {
        const item = e.target.closest('.request-item');
        if (item && document.querySelectorAll('.request-item').length > 1) {
            item.remove();
        }
    }
});

// ============ INISIALISASI DROPDOWN AJAX ============
function initDropdownListeners(scope) {
    const lokasi = scope.querySelector('.lokasi');
    const jenis = scope.querySelector('.jenis');
    const kendaraan = scope.querySelector('.kendaraan');

    if (!lokasi || !jenis || !kendaraan) return;

    // RESET dropdown setiap kali ganti lokasi
    lokasi.addEventListener('change', function() {
        const id = this.value;
        jenis.innerHTML = '<option>Loading...</option>';
        kendaraan.innerHTML = '<option>-- Pilih Kendaraan --</option>';
        kendaraan.disabled = true;

        if (id) {
            fetch(`../../functions/ajax_kendaraan.php?lokasi_id=${id}`)
                .then(res => res.text())
                .then(data => {
                    jenis.innerHTML = data || '<option value="">-- Tidak Ada Jenis --</option>';
                    jenis.disabled = false;
                })
                .catch(() => {
                    jenis.innerHTML = '<option>Error memuat data</option>';
                });
        } else {
            jenis.innerHTML = '<option>-- Pilih Lokasi Dulu --</option>';
            jenis.disabled = true;
        }
    });

    // RESET kendaraan setiap kali ganti jenis kendaraan
    jenis.addEventListener('change', function() {
        const id_lokasi = lokasi.value;
        const jenisVal = this.value;

        kendaraan.innerHTML = '<option>Loading...</option>';
        kendaraan.disabled = true;

        if (id_lokasi && jenisVal) {
            fetch(`../../functions/ajax_kendaraan.php?lokasi_id=${id_lokasi}&jenis_kendaraan=${encodeURIComponent(jenisVal)}`)
                .then(res => res.text())
                .then(data => {
                    kendaraan.innerHTML = data || '<option value="">-- Tidak Ada Kendaraan --</option>';
                    kendaraan.disabled = false;
                })
                .catch(() => {
                    kendaraan.innerHTML = '<option>Error memuat data</option>';
                });
        } else {
            kendaraan.innerHTML = '<option>-- Pilih Jenis Dulu --</option>';
        }
    });
}

// Jalankan inisialisasi pertama kali
document.querySelectorAll('.request-item').forEach(initDropdownListeners);
</script>
</body>
</html>
