<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <img src="../assets/images/logo-maxi.jpg" alt="Logo">
            Inventaris
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" aria-controls="navbarNav" 
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=divisi">Divisi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=kendaraan">Kendaraan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=lokasi">Lokasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=pemakaian">Pemakaian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=pemakaianSelesai">History Pemakaian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="request.php">Request</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="approved.php">Approval</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=roles">Roles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=status">Status</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=user">User</a>
                </li>
            </ul>
            <form id="logoutForm" action="" method="post" class="d-flex">
                <button id="btnLogout" class="btn btn-light btn-sm" type="button">Logout</button>
                <input type="hidden" name="logout" value="1">
            </form>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('btnLogout').addEventListener('click', function() {
    Swal.fire({
        title: 'Yakin ingin logout?',
        text: "Anda akan keluar dari sistem!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
});
</script>

