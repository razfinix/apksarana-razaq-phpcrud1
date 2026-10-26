<?php
$jumlahAlat = totalData($koneksi, 'alat');
$jumlahPeminjam = totalData($koneksi, 'peminjam');
$jumlahPeminjaman = totalData($koneksi, 'peminjaman');
$aktifPinjam = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE status='Dipinjam'"))['c'] ?? 0);

$pendingRows = mysqli_query($koneksi, "SELECT p.idpeminjaman,p.tanggalpinjam,p.tanggalpengembalian,pm.namapeminjam,pm.kelas,COUNT(d.iddetilpeminjaman) AS jumlahitem FROM peminjaman p JOIN peminjam pm ON pm.idpeminjam=p.idpeminjam LEFT JOIN detilpeminjaman d ON d.idpeminjaman=p.idpeminjaman WHERE p.status='Menunggu' GROUP BY p.idpeminjaman ORDER BY p.idpeminjaman ASC");
$lateRows = mysqli_query($koneksi, "SELECT p.idpeminjaman,p.tanggalpinjam,p.tanggalpengembalian,pm.namapeminjam FROM peminjaman p JOIN peminjam pm ON pm.idpeminjam=p.idpeminjam WHERE p.status='Dipinjam' AND p.tanggalpengembalian < CURDATE() ORDER BY p.tanggalpengembalian ASC");
$pendingCount = $pendingRows ? mysqli_num_rows($pendingRows) : 0;
$lateCount = $lateRows ? mysqli_num_rows($lateRows) : 0;
$nama = htmlspecialchars($_SESSION['namauser'] ?? 'Petugas');
?>

<div class="dash-banner teal">
  <div class="dash-banner-inner">
    <div>
      <span class="eyebrow" style="background:rgba(255,255,255,.15);color:#fff;margin-bottom:8px">DASHBOARD PETUGAS</span>
      <h2>Siap melayani, <?= $nama ?></h2>
      <p>Fokus operasional: setujui pengajuan, pantau alat dipinjam, dan proses pengembalian.</p>
    </div>
    <div class="dash-banner-actions">
      <a class="btn-solid" href="index.php?halaman=peminjaman"><i class="fas fa-clipboard-check mr-1"></i> Transaksi</a>
      <a class="btn-ghost" href="index.php?halaman=pengembalian"><i class="fas fa-undo mr-1"></i> Pengembalian</a>
    </div>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-modern variant-orange">
    <span class="stat-icon orange"><i class="fas fa-tools"></i></span>
    <div><strong><?= $jumlahAlat ?></strong><span>Data Sarana</span></div>
  </div>
  <div class="stat-modern variant-teal">
    <span class="stat-icon teal"><i class="fas fa-user"></i></span>
    <div><strong><?= $jumlahPeminjam ?></strong><span>Peminjam</span></div>
  </div>
  <div class="stat-modern variant-purple">
    <span class="stat-icon purple"><i class="fas fa-hand-holding"></i></span>
    <div><strong><?= $jumlahPeminjaman ?></strong><span>Total Transaksi</span></div>
  </div>
  <div class="stat-modern variant-blue">
    <span class="stat-icon blue"><i class="fas fa-clock"></i></span>
    <div><strong><?= $aktifPinjam ?></strong><span>Sedang Dipinjam</span></div>
  </div>
</div>

<div class="action-grid">
  <a class="action-tile" href="index.php?halaman=peminjaman">
    <span class="tile-icon blue"><i class="fas fa-tasks"></i></span>
    <strong>Antrian Pengajuan</strong>
    <span><?= $pendingCount ?> perlu diproses</span>
  </a>
  <a class="action-tile" href="index.php?halaman=pengembalian">
    <span class="tile-icon teal"><i class="fas fa-box-open"></i></span>
    <strong>Terima Kembali</strong>
    <span>Catat pengembalian alat</span>
  </a>
  <a class="action-tile" href="index.php?halaman=alat">
    <span class="tile-icon orange"><i class="fas fa-wrench"></i></span>
    <strong>Stok Sarana</strong>
    <span>Cek ketersediaan alat</span>
  </a>
  <a class="action-tile" href="index.php?halaman=laporanharian">
    <span class="tile-icon purple"><i class="fas fa-file-alt"></i></span>
    <strong>Laporan</strong>
    <span>Ringkasan aktivitas</span>
  </a>
</div>

<div class="panel-card">
  <div class="panel-head">
    <div>
      <span class="eyebrow">ANTRIAN</span>
      <h3>Pengajuan Menunggu Persetujuan</h3>
    </div>
    <div>
      <span class="badge badge-warning"><?= $pendingCount ?></span>
      <a class="btn btn-sm btn-primary ml-2" href="index.php?halaman=peminjaman">Buka semua</a>
    </div>
  </div>
  <div class="panel-body p-0">
    <div class="table-responsive">
      <table class="table modern-table mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Peminjam</th>
            <th>Kelas/Jabatan</th>
            <th>Tanggal Pengajuan</th>
            <th>Rencana Kembali</th>
            <th>Item</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$pendingRows || $pendingCount === 0): ?>
            <tr><td colspan="7"><div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada pengajuan baru hari ini.</p></div></td></tr>
          <?php else: while ($r = mysqli_fetch_assoc($pendingRows)): ?>
            <tr>
              <td><strong>#<?= (int)$r['idpeminjaman'] ?></strong></td>
              <td><?= htmlspecialchars($r['namapeminjam']) ?></td>
              <td><?= htmlspecialchars($r['kelas'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['tanggalpinjam'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['tanggalpengembalian'] ?? '-') ?></td>
              <td><span class="pill pill-info"><?= (int)$r['jumlahitem'] ?></span></td>
              <td class="text-nowrap">
                <a class="btn btn-sm btn-outline-primary" href="index.php?halaman=showpeminjaman&id=<?= (int)$r['idpeminjaman'] ?>">Detail</a>
                <form action="proses/prosespeminjaman.php?aksi=status" method="post" class="d-inline">
                  <input type="hidden" name="idpeminjaman" value="<?= (int)$r['idpeminjaman'] ?>">
                  <input type="hidden" name="status" value="Dipinjam">
                  <button class="btn btn-sm btn-success" onclick="return confirm('Setujui peminjaman ini?')"><i class="fas fa-check"></i></button>
                </form>
                <form action="proses/prosespeminjaman.php?aksi=status" method="post" class="d-inline">
                  <input type="hidden" name="idpeminjaman" value="<?= (int)$r['idpeminjaman'] ?>">
                  <input type="hidden" name="status" value="Ditolak">
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak pengajuan ini?')"><i class="fas fa-times"></i></button>
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
      <span class="eyebrow">TERLAMBAT</span>
      <h3>Alat Melewati Batas Pengembalian</h3>
    </div>
    <span class="badge badge-danger"><?= $lateCount ?></span>
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
            <tr><td colspan="5"><div class="empty-state"><i class="fas fa-smile"></i><p>Tidak ada keterlambatan. Operasional aman.</p></div></td></tr>
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
