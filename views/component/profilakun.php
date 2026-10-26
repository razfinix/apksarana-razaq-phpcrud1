<?php
$idUserAktif = (int)($_SESSION['iduser'] ?? 0);
$stmtProfil = mysqli_prepare($koneksi, "SELECT * FROM user WHERE iduser = ? LIMIT 1");
mysqli_stmt_bind_param($stmtProfil, 'i', $idUserAktif);
mysqli_stmt_execute($stmtProfil);
$userProfil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtProfil)) ?: [];
mysqli_stmt_close($stmtProfil);
$isPeminjam = (($_SESSION['role'] ?? '') === 'peminjam');
$fotoProfil = fotoUser($userProfil['foto'] ?? ($_SESSION['foto'] ?? ''));
$namaTampil = $isPeminjam ? ($_SESSION['namapeminjam'] ?? $userProfil['namauser'] ?? 'Peminjam') : ($userProfil['namauser'] ?? 'Pengguna');
?>
<div class="container-fluid py-3 profile-settings-page">
    <div class="page-heading mb-3"><div><span class="eyebrow">PENGATURAN AKUN</span><h1>Profil Saya</h1><p>Tambahkan atau ganti foto profil. Foto akan tampil di navbar dan sidebar saat akun digunakan.</p></div></div>
    <?php if (!empty($_SESSION['success_foto'])): ?><div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($_SESSION['success_foto']); ?></div><?php unset($_SESSION['success_foto']); endif; ?>
    <?php if (!empty($_SESSION['error_foto'])): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($_SESSION['error_foto']); ?></div><?php unset($_SESSION['error_foto']); endif; ?>
    <div class="card profile-settings-card"><div class="card-body p-4">
        <div class="profile-settings-summary"><img src="<?= htmlspecialchars($fotoProfil); ?>" alt="Foto profil saat ini" class="profile-settings-avatar"><div><h3><?= htmlspecialchars($namaTampil); ?></h3><div class="text-muted">@<?= htmlspecialchars($userProfil['username'] ?? ($_SESSION['username'] ?? '')); ?></div><span class="badge badge-primary mt-2"><?= htmlspecialchars(ucfirst($_SESSION['role'] ?? 'user')); ?></span></div></div>
        <hr>
        <form action="proses/prosesfotouser.php" method="post" enctype="multipart/form-data">
            <div class="form-group"><label for="foto_profil">Pilih foto profil baru</label><input type="file" id="foto_profil" name="foto_profil" class="form-control-file" accept="image/jpeg,image/png,image/webp" required><small class="form-text text-muted">Format JPG, PNG, atau WEBP. Maksimal 3 MB.</small></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-camera mr-1"></i> Simpan Foto</button>
            <?php if ($isPeminjam): ?><a href="index.php?halaman=dashboardpeminjam" class="btn btn-light ml-1">Kembali</a><?php else: ?><a href="index.php?halaman=<?= (($_SESSION['role'] ?? '') === 'admin' ? 'dashboardadmin' : 'dashboardpetugas'); ?>" class="btn btn-light ml-1">Kembali</a><?php endif; ?>
        </form>
    </div></div>
</div>
