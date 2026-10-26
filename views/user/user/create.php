<div class="page-heading">
  <div>
    <span class="eyebrow">USER</span>
    <h1>Tambah User</h1>
    <p>Buat akun Admin, Petugas, atau Peminjam.</p>
  </div>
</div>
<form action="proses/prosesuser.php?aksi=tambah" method="post" class="card modern-card">
  <div class="card-body p-4" style="max-width:560px">
    <div class="form-group"><label>Nama</label><input class="form-control" name="namauser" required></div>
    <div class="form-group"><label>Username</label><input class="form-control" name="username" required></div>
    <div class="form-group"><label>Password</label><input class="form-control" type="password" name="password" required></div>
    <div class="form-group">
      <label>Role</label>
      <select class="form-control" name="role" required>
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
        <option value="peminjam">Peminjam</option>
      </select>
      <small class="text-muted">Admin &amp; Petugas login di halaman staff. Peminjam login di halaman peminjam.</small>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a class="btn btn-light" href="index.php?halaman=user">Batal</a>
  </div>
</form>
