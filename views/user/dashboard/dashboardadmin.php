<?php
$jumlahStaff = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM user WHERE role IN ('admin','petugas')"))['c'] ?? 0);
$jumlahPeminjam = totalData($koneksi, 'peminjam');
$jumlahAlat = totalData($koneksi, 'alat');
$jumlahPeminjaman = totalData($koneksi, 'peminjaman');

$pendingRows = mysqli_query($koneksi, "SELECT p.idpeminjaman,p.tanggalpinjam,p.tanggalpengembalian,pm.namapeminjam,pm.kelas,COUNT(d.iddetilpeminjaman) AS jumlahitem FROM peminjaman p JOIN peminjam pm ON pm.idpeminjam=p.idpeminjam LEFT JOIN detilpeminjaman d ON d.idpeminjaman=p.idpeminjaman WHERE p.status='Menunggu' GROUP BY p.idpeminjaman ORDER BY p.idpeminjaman ASC");
$lateRows = mysqli_query($koneksi, "SELECT p.idpeminjaman,p.tanggalpinjam,p.tanggalpengembalian,pm.namapeminjam FROM peminjaman p JOIN peminjam pm ON pm.idpeminjam=p.idpeminjam WHERE p.status='Dipinjam' AND p.tanggalpengembalian < CURDATE() ORDER BY p.tanggalpengembalian ASC");
$pendingCount = $pendingRows ? mysqli_num_rows($pendingRows) : 0;
$lateCount = $lateRows ? mysqli_num_rows($lateRows) : 0;
$nama = htmlspecialchars($_SESSION['namauser'] ?? 'Admin');
?>

<div class="dash-banner">
  <div class="dash-banner-inner">
    <div>
      <span class="eyebrow" style="background:rgba(255,255,255,.15);color:#fff;margin-bottom:8px">DASHBOARD ADMIN</span>
      <h2>Halo, <?= $nama ?> 👋</h2>
      <p>Pantau stok sarana, setujui pengajuan, dan jaga operasional peminjaman tetap rapi.</p>
    </div>
    <div class="dash-banner-actions">
      <a class="btn-solid" href="index.php?halaman=peminjaman"><i class="fas fa-clipboard-list mr-1"></i> Kelola Peminjaman</a>
      <a class="btn-ghost" href="index.php?halaman=alat"><i class="fas fa-tools mr-1"></i> Data Sarana</a>
    </div>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-modern variant-blue">
    <span class="stat-icon blue"><i class="fas fa-user-shield"></i></span>
    <div><strong><?= $jumlahStaff ?></strong><span>Admin & Petugas</span></div>
  </div>
  <div class="stat-modern variant-teal">
    <span class="stat-icon teal"><i class="fas fa-user-graduate"></i></span>
    <div><strong><?= $jumlahPeminjam ?></strong><span>Peminjam</span></div>
  </div>
  <div class="stat-modern variant-orange">
    <span class="stat-icon orange"><i class="fas fa-tools"></i></span>
    <div><strong><?= $jumlahAlat ?></strong><span>Sarana</span></div>
  </div>
  <div class="stat-modern variant-purple">
    <span class="stat-icon purple"><i class="fas fa-hand-holding"></i></span>
    <div><strong><?= $jumlahPeminjaman ?></strong><span>Total Peminjaman</span></div>
  </div>
</div>

<div class="action-grid">
  <a class="action-tile" href="index.php?halaman=peminjaman">
    <span class="tile-icon blue"><i class="fas fa-check-double"></i></span>
    <strong>Setujui Pengajuan</strong>
    <span><?= $pendingCount ?> menunggu persetujuan</span>
  </a>
  <a class="action-tile" href="index.php?halaman=pengembalian">
    <span class="tile-icon teal"><i class="fas fa-undo"></i></span>
    <strong>Pengembalian</strong>
    <span>Proses alat yang dikembalikan</span>
  </a>
  <a class="action-tile" href="index.php?halaman=user">
    <span class="tile-icon purple"><i class="fas fa-users-cog"></i></span>
    <strong>Kelola Staff</strong>
    <span>Admin & petugas saja</span>
  </a>
  <a class="action-tile" href="index.php?halaman=laporanharian">
    <span class="tile-icon orange"><i class="fas fa-chart-line"></i></span>
    <strong>Laporan</strong>
    <span>Rekap harian & bulanan</span>
  </a>
</div>

<div class="panel-card">
  <div class="panel-head">
    <div>
      <span class="eyebrow">PERLU TINDAKAN</span>
      <h3>Pengajuan Menunggu Persetujuan</h3>
    </div>
    <div>
      <span class="badge badge-warning"><?= $pendingCount ?> pending</span>
      <a class="btn btn-sm btn-primary ml-2" href="index.php?halaman=peminjaman">Lihat semua</a>
    </div>
  </div>
  <div class="panel-body p-0">
    <div class="table-responsive">
      <table class="table modern-table mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Peminjam</th>
            <th>Kelas</th>
            <th>Pengajuan</th>
            <th>Rencana Kembali</th>
            <th>Item</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$pendingRows || $pendingCount === 0): ?>
            <tr><td colspan="7"><div class="empty-state"><i class="fas fa-inbox"></i><p>Tidak ada pengajuan menunggu. Semua sudah diproses.</p></div></td></tr>
          <?php else: while ($r = mysqli_fetch_assoc($pendingRows)): ?>
            <tr>
              <td><strong>#<?= (int)$r['idpeminjaman'] ?></strong></td>
              <td><?= htmlspecialchars($r['namapeminjam']) ?></td>
              <td><?= htmlspecialchars($r['kelas'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['tanggalpinjam'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['tanggalpengembalian'] ?? '-') ?></td>
              <td><span class="pill pill-info"><?= (int)$r['jumlahitem'] ?> item</span></td>
              <td class="text-nowrap">
                <a class="btn btn-sm btn-outline-primary" href="index.php?halaman=showpeminjaman&id=<?= (int)$r['idpeminjaman'] ?>">Detail</a>
                <form action="proses/prosespeminjaman.php?aksi=status" method="post" class="d-inline">
                  <input type="hidden" name="idpeminjaman" value="<?= (int)$r['idpeminjaman'] ?>">
                  <input type="hidden" name="status" value="Dipinjam">
                  <button class="btn btn-sm btn-success" onclick="return confirm('Setujui peminjaman ini?')" title="Setujui"><i class="fas fa-check"></i></button>
                </form>
                <form action="proses/prosespeminjaman.php?aksi=status" method="post" class="d-inline">
                  <input type="hidden" name="idpeminjaman" value="<?= (int)$r['idpeminjaman'] ?>">
                  <input type="hidden" name="status" value="Ditolak">
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak pengajuan ini?')" title="Tolak"><i class="fas fa-times"></i></button>
                </form>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="panel-card">
  <div class="panel-head">
    <div>
      <span class="eyebrow">PENGINGAT</span>
      <h3>Melewati Batas Pengembalian</h3>
    </div>
    <span class="badge badge-danger"><?= $lateCount ?> terlambat</span>
  </div>
  <div class="panel-body p-0">
    <div class="table-responsive">
      <table class="table modern-table mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Peminjam</th>
            <th>Tanggal Pinjam</th>
            <th>Batas Kembali</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$lateRows || $lateCount === 0): ?>
            <tr><td colspan="5"><div class="empty-state"><i class="fas fa-check-circle"></i><p>Tidak ada peminjaman yang terlambat. Bagus!</p></div></td></tr>
          <?php else: while ($r = mysqli_fetch_assoc($lateRows)): ?>
            <tr>
              <td><strong>#<?= (int)$r['idpeminjaman'] ?></strong></td>
              <td><?= htmlspecialchars($r['namapeminjam']) ?></td>
              <td><?= htmlspecialchars($r['tanggalpinjam'] ?? '-') ?></td>
              <td><span class="pill pill-danger"><?= htmlspecialchars($r['tanggalpengembalian'] ?? '-') ?></span></td>
              <td><a class="btn btn-sm btn-outline-primary" href="index.php?halaman=showpeminjaman&id=<?= (int)$r['idpeminjaman'] ?>">Lihat</a></td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
