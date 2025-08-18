<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
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
                    <a class="nav-link" href="view_Data.php?type=roles">Roles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=status">Status</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_Data.php?type=user">User</a>
                </li>
            </ul>
            <form action="" method="get" class="d-flex">
                <button class="btn btn-light btn-sm" name="logout" value="1" type="submit">Logout</button>
            </form>
        </div>
    </div>
</nav>
