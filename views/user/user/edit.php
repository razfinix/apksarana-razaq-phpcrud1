<?php
$id = (int)($_GET['id'] ?? 0);
$r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE iduser=$id"));
if (!$r) {
    echo '<div class="alert alert-danger">User tidak ditemukan.</div>';
    echo '<a class="btn btn-secondary" href="index.php?halaman=user">Kembali</a>';
    return;
}
?>
<div class="page-heading">
  <div>
    <span class="eyebrow">USER</span>
    <h1>Edit User</h1>
    <p>Ubah data akun: Admin, Petugas, atau Peminjam.</p>
  </div>
</div>
<form action="proses/prosesuser.php?aksi=edit" method="post" class="card modern-card">
  <div class="card-body p-4" style="max-width:560px">
    <input type="hidden" name="iduser" value="<?= (int)$r['iduser'] ?>">
    <div class="form-group"><label>Nama</label><input class="form-control" name="namauser" value="<?= htmlspecialchars($r['namauser']) ?>" required></div>
    <div class="form-group"><label>Username</label><input class="form-control" name="username" value="<?= htmlspecialchars($r['username']) ?>" required></div>
    <div class="form-group"><label>Password baru (kosongkan jika tidak diubah)</label><input class="form-control" type="password" name="password"></div>
    <div class="form-group">
      <label>Role</label>
      <select class="form-control" name="role" required>
        <?php foreach (['admin' => 'Admin', 'petugas' => 'Petugas', 'peminjam' => 'Peminjam'] as $val => $label): ?>
          <option value="<?= $val ?>" <?= ($r['role'] === $val) ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a class="btn btn-light" href="index.php?halaman=user">Batal</a>
  </div>
</form>
