<div class="login-page"><div class="card auth-card"><div class="card-body">
<div class="auth-icon"><i class="fas fa-user-shield"></i></div>
<h3 class="text-center">Login Admin / Petugas</h3>
<p class="auth-subtitle text-center">Masuk untuk mengelola aplikasi peminjaman sarana. Admin dan Petugas memakai halaman login yang sama.</p>
<?php if(isset($_SESSION['error_login'])): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($_SESSION['error_login']); unset($_SESSION['error_login']); ?></div><?php endif; ?>
<form action="proses/prosesloginuser.php" method="post">
<div class="form-group"><label>Username</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div><input class="form-control" name="username" autocomplete="username" required></div></div>
<div class="form-group"><label>Password</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div><input class="form-control" name="password" type="password" autocomplete="current-password" required></div></div>
<button class="btn btn-primary btn-block py-2" name="login"><i class="fas fa-sign-in-alt mr-1"></i> Login</button>
</form>
</div></div></div>
