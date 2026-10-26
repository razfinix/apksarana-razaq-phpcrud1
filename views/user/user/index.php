<?php
$q = mysqli_query($koneksi, "SELECT iduser,namauser,username,nohp,role,foto FROM user ORDER BY iduser DESC");
$filter = $_GET['role'] ?? '';
if (in_array($filter, ['admin', 'petugas', 'peminjam'], true)) {
    $q = mysqli_query($koneksi, "SELECT iduser,namauser,username,nohp,role,foto FROM user WHERE role='" . mysqli_real_escape_string($koneksi, $filter) . "' ORDER BY iduser DESC");
}
$totalAll = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM user"))['c'] ?? 0);
$totalAdmin = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM user WHERE role='admin'"))['c'] ?? 0);
$totalPetugas = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM user WHERE role='petugas'"))['c'] ?? 0);
$totalPeminjam = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM user WHERE role='peminjam'"))['c'] ?? 0);
?>
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
  <div>
    <h2>Kelola User</h2>
    <p class="text-muted mb-0">Semua pengguna sistem: Admin, Petugas, dan Peminjam.</p>
  </div>
  <a class="btn btn-primary" href="index.php?halaman=createuser"><i class="fas fa-plus mr-1"></i>Tambah User</a>
</div>

<div class="mb-3 d-flex flex-wrap" style="gap:8px">
  <a class="btn btn-sm <?= $filter === '' ? 'btn-primary' : 'btn-outline-primary' ?>" href="index.php?halaman=user">Semua (<?= $totalAll ?>)</a>
  <a class="btn btn-sm <?= $filter === 'admin' ? 'btn-danger' : 'btn-outline-danger' ?>" href="index.php?halaman=user&role=admin">Admin (<?= $totalAdmin ?>)</a>
  <a class="btn btn-sm <?= $filter === 'petugas' ? 'btn-info' : 'btn-outline-info' ?>" href="index.php?halaman=user&role=petugas">Petugas (<?= $totalPetugas ?>)</a>
  <a class="btn btn-sm <?= $filter === 'peminjam' ? 'btn-success' : 'btn-outline-success' ?>" href="index.php?halaman=user&role=peminjam">Peminjam (<?= $totalPeminjam ?>)</a>
</div>

<div class="card modern-card">
  <div class="card-body table-responsive p-0">
    <table class="table modern-table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Foto</th>
          <th>ID</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $ada = false;
        while ($r = mysqli_fetch_assoc($q)):
          $ada = true;
          $role = $r['role'] ?? '';
          $badge = 'badge-secondary';
          if ($role === 'admin') $badge = 'badge-danger';
          elseif ($role === 'petugas') $badge = 'badge-info';
          elseif ($role === 'peminjam') $badge = 'badge-success';
        ?>
        <tr>
          <td>
            <img src="<?= htmlspecialchars(fotoUser($r['foto'] ?? '')) ?>"
                 alt="Foto <?= htmlspecialchars($r['namauser']) ?>"
                 style="width:42px;height:42px;object-fit:cover;border-radius:50%;border:2px solid #d8e7ff">
          </td>
          <td><?= (int)$r['iduser'] ?></td>
          <td><?= htmlspecialchars($r['namauser']) ?></td>
          <td><?= htmlspecialchars($r['username']) ?></td>
          <td><span class="badge <?= $badge ?>"><?= htmlspecialchars(ucfirst($role)) ?></span></td>
          <td>
            <a class="btn btn-sm btn-warning" href="index.php?halaman=edituser&id=<?= (int)$r['iduser'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger"
               onclick="return confirm('Hapus user ini?')"
               href="proses/prosesuser.php?aksi=hapus&id=<?= (int)$r['iduser'] ?>">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (!$ada): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data user.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
