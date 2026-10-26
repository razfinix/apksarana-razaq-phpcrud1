<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$namaPeminjam = $_SESSION['namapeminjam'] ?? 'Peminjam';
require_once __DIR__ . '/../../../proses/helper.php';
?>

<nav class="top-navbar" role="navigation" aria-label="Navbar Peminjam">
    <div class="navbar-left">
        <button type="button" class="menu-toggle" id="peminjamMenuToggle" aria-label="Buka menu" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>

        <a class="navbar-title" href="index.php?halaman=dashboardpeminjam">
            Sistem Peminjaman Sarana
        </a>
    </div>

    <div class="navbar-right">
        <span class="navbar-user" title="<?= htmlspecialchars($namaPeminjam); ?>">
            <img src="<?= htmlspecialchars(fotoUser($_SESSION['foto'] ?? '')); ?>" alt="Foto profil" class="peminjam-navbar-avatar"> <?= htmlspecialchars($namaPeminjam); ?>
        </span>

        <a href="index.php?halaman=logout" class="logout-link">
            <i class="fas fa-sign-out-alt mr-1"></i>Logout
        </a>
    </div>
</nav>
