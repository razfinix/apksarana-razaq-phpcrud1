<div class="auth-login-page"><div class="card auth-card"><div class="card-body">
<div class="auth-icon" style="background:linear-gradient(135deg,#198754,#4dd4ac)"><i class="fas fa-user"></i></div>
<h3 class="text-center">Login Peminjam</h3><p class="auth-subtitle text-center">Masuk untuk melihat sarana dan melakukan peminjaman.</p>
<?php if(isset($_GET['daftar'])): ?><div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i>Registrasi berhasil. Silakan login.</div><?php endif; ?>
<?php if(isset($_SESSION['error_login'])): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($_SESSION['error_login']); unset($_SESSION['error_login']); ?></div><?php endif; ?>
<form action="proses/prosesloginpeminjam.php" method="post">
<div class="form-group"><label>Username</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div><input class="form-control" name="username" autocomplete="username" required></div></div>
<div class="form-group"><label>Password</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div><input class="form-control" type="password" name="password" autocomplete="current-password" required></div></div>
<button class="btn btn-success btn-block py-2"><i class="fas fa-sign-in-alt mr-1"></i> Login Peminjam</button>
</form><div class="text-center mt-3"><a href="index.php?halaman=registerpeminjam">Belum punya akun? Register</a></div>
</div></div></div>
