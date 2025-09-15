<!-- Load SweetAlert & Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- FLASH MESSAGE -->
<?php if (!empty($_SESSION['flash'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: '<?= htmlspecialchars($_SESSION['flash']['icon'], ENT_QUOTES, "UTF-8") ?>',
        title: '<?= htmlspecialchars($_SESSION['flash']['title'], ENT_QUOTES, "UTF-8") ?>',
        text: '<?= htmlspecialchars($_SESSION['flash']['text'], ENT_QUOTES, "UTF-8") ?>',
        confirmButtonColor: '#3085d6'
    });
});
</script>
<?php unset($_SESSION['flash']); endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Konfirmasi hapus
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // Alert untuk edit disabled
    document.querySelectorAll('.btn-edit-disabled').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const title = this.getAttribute('data-title') || 'Tidak Bisa Edit!';
            const text  = this.getAttribute('data-text') || 'Data ini tidak dapat diedit.';
            Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonColor: '#3085d6'
            });
        });
    });
});
</script>
