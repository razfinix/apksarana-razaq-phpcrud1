<nav class="main-header navbar navbar-expand navbar-white navbar-light" role="navigation" aria-label="Navbar Admin dan Petugas">
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <button type="button" class="nav-link admin-mobile-menu-toggle" id="adminMenuToggle" aria-label="Buka menu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </li>
        <li class="nav-item d-none d-md-block ml-2">
            <span class="font-weight-bold text-dark">Peminjaman Sarana</span>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <img src="<?=fotoUser($_SESSION['foto']??'')?>" class="user-image img-circle" style="width:28px;height:28px;max-width:28px;max-height:28px;object-fit:cover;flex-shrink:0;position:static;" alt="Foto pengguna">
                <span class="ml-2"><?=htmlspecialchars($_SESSION['namauser']??'User')?> <small class="text-muted">(<?=ucfirst($_SESSION['role']??'')?>)</small></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                <a class="dropdown-item" href="index.php?halaman=profilakun"><i class="fas fa-user-circle mr-2 text-primary"></i>Profil Saya</a>
                <a class="dropdown-item" href="index.php?halaman=logout"><i class="fas fa-sign-out-alt mr-2 text-danger"></i>Logout</a>
            </div>
        </li>
    </ul>
</nav>
