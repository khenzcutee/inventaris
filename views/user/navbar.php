<!-- Bottom Navigation Bar -->
<nav class="bottom-nav">
    <a href="dashboard.php" class="nav-item-mobile">
        <i class="fas fa-home"></i><span>Home</span>
    </a>
    <a href="request.php" class="nav-item-mobile">
        <i class="fas fa-plus-circle"></i><span>Request</span>
    </a>
    <a href="view_data.php?view=aktif" class="nav-item-mobile">
        <i class="fas fa-table"></i><span>Pemakaian</span>
    </a>
    <a href="kendaraan.php" class="nav-item-mobile">
        <i class="fas fa-car-side"></i><span>Daftar Kendaraan</span>
    </a>
</nav>

<script>
document.getElementById('logoutBtn').addEventListener('click', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'Yakin ingin logout?',
        text: 'Sesi Anda akan berakhir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, logout!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fungsi toggleSidebar tidak diperlukan lagi karena sidebar di-hide di mobile
</script>