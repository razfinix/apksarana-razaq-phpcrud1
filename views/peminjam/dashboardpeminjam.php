<?php
$id = (int) ($_SESSION['idpeminjam'] ?? 0);
$alat = totalData($koneksi, 'alat');
$riwayat = 0; $aktif = 0; $pending = 0; $terlambat = 0;

$q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE idpeminjam = $id");
if ($q) { $riwayat = (int)(mysqli_fetch_assoc($q)['total'] ?? 0); }

$aktifQ = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE idpeminjam = $id AND status = 'Dipinjam'");
if ($aktifQ) { $aktif = (int)(mysqli_fetch_assoc($aktifQ)['total'] ?? 0); }

$pendingQ = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE idpeminjam = $id AND status = 'Menunggu'");
if ($pendingQ) { $pending = (int)(mysqli_fetch_assoc($pendingQ)['total'] ?? 0); }

$lateQ = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE idpeminjam = $id AND status = 'Dipinjam' AND tanggalpengembalian IS NOT NULL AND tanggalpengembalian < CURDATE()");
if ($lateQ) { $terlambat = (int)(mysqli_fetch_assoc($lateQ)['total'] ?? 0); }

$nama = htmlspecialchars($_SESSION['namapeminjam'] ?? 'Peminjam', ENT_QUOTES, 'UTF-8');

$recent = mysqli_query($koneksi, "SELECT p.idpeminjaman,p.status,p.tanggalpinjam,p.tanggalpengembalian FROM peminjaman p WHERE p.idpeminjam=$id ORDER BY p.idpeminjaman DESC LIMIT 5");
?>

<div class="dash-banner purple">
  <div class="dash-banner-inner">
    <div>
      <span class="eyebrow" style="background:rgba(255,255,255,.15);color:#fff;margin-bottom:8px">RUANG PEMINJAM</span>
      <h2>Halo, <?= $nama ?> 👋</h2>
      <p>Cek sarana yang tersedia, ajukan peminjaman, dan pantau status pengajuanmu di satu tempat.</p>
    </div>
    <div class="dash-banner-actions">
      <a class="btn-solid" href="index.php?halaman=daftaralat"><i class="fas fa-search mr-1"></i> Cari Sarana</a>
      <a class="btn-ghost" href="index.php?halaman=riwayatpeminjaman"><i class="fas fa-history mr-1"></i> Riwayat</a>
    </div>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-modern variant-orange">
    <span class="stat-icon orange"><i class="fas fa-tools"></i></span>
    <div><strong><?= $alat ?></strong><span>Sarana Tersedia</span></div>
  </div>
  <div class="stat-modern variant-teal">
    <span class="stat-icon teal"><i class="fas fa-clipboard-list"></i></span>
    <div><strong><?= $riwayat ?></strong><span>Total Pengajuan</span></div>
  </div>
  <div class="stat-modern variant-purple">
    <span class="stat-icon purple"><i class="fas fa-clock"></i></span>
    <div><strong><?= $aktif ?></strong><span>Sedang Dipinjam</span></div>
  </div>
  <div class="stat-modern variant-blue">
    <span class="stat-icon blue"><i class="fas fa-hourglass-half"></i></span>
    <div><strong><?= $pending ?></strong><span>Menunggu Persetujuan</span></div>
  </div>
</div>

<?php if ($terlambat > 0): ?>
<div class="panel-card" style="border-color:#fecaca;margin-bottom:22px">
  <div class="panel-head" style="background:linear-gradient(90deg,#fff1f2,#fee2e2)">
    <div>
      <span class="eyebrow" style="color:#be123c">PERINGATAN</span>
      <h3 style="color:#9f1239">Ada <?= $terlambat ?> peminjaman melewati batas pengembalian</h3>
    </div>
    <a class="btn btn-sm btn-danger" href="index.php?halaman=riwayatpeminjaman">Segera kembalikan</a>
  </div>
</div>
<?php endif; ?>

<div class="action-grid">
  <a class="action-tile" href="index.php?halaman=daftaralat">
    <span class="tile-icon blue"><i class="fas fa-search"></i></span>
    <strong>Jelajahi Sarana</strong>
    <span>Lihat alat yang bisa dipinjam</span>
  </a>
  <a class="action-tile" href="index.php?halaman=ajukanpeminjaman">
    <span class="tile-icon teal"><i class="fas fa-plus-circle"></i></span>
    <strong>Ajukan Peminjaman</strong>
    <span>Buat pengajuan baru</span>
  </a>
  <a class="action-tile" href="index.php?halaman=riwayatpeminjaman">
    <span class="tile-icon purple"><i class="fas fa-history"></i></span>
    <strong>Riwayat & Status</strong>
    <span>Pantau persetujuan & pengembalian</span>
  </a>
  <a class="action-tile" href="index.php?halaman=profil">
    <span class="tile-icon orange"><i class="fas fa-user"></i></span>
    <strong>Profil Saya</strong>
    <span>Perbarui data akun</span>
  </a>
</div>

<div class="dash-split">
  <div class="panel-card mb-0">
    <div class="panel-head">
      <div>
        <span class="eyebrow">AKTIVITAS</span>
        <h3>Pengajuan Terbaru</h3>
      </div>
      <a class="btn btn-sm btn-outline-primary" href="index.php?halaman=riwayatpeminjaman">Semua</a>
    </div>
    <div class="panel-body p-0">
      <div class="table-responsive">
        <table class="table modern-table mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Status</th>
              <th>Pinjam</th>
              <th>Batas</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$recent || mysqli_num_rows($recent) === 0): ?>
              <tr><td colspan="4"><div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada pengajuan. Mulai dari cari sarana.</p></div></td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($recent)):
              $st = $r['status'] ?? '';
              $pill = 'pill-info';
              if ($st === 'Menunggu') $pill = 'pill-wait';
              elseif ($st === 'Dipinjam') $pill = 'pill-ok';
              elseif ($st === 'Ditolak' || $st === 'Dikembalikan') $pill = ($st === 'Ditolak' ? 'pill-danger' : 'pill-info');
            ?>
              <tr>
                <td><a href="index.php?halaman=riwayatpeminjaman"><strong>#<?= (int)$r['idpeminjaman'] ?></strong></a></td>
                <td><span class="pill <?= $pill ?>"><?= htmlspecialchars($st) ?></span></td>
                <td><?= htmlspecialchars($r['tanggalpinjam'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['tanggalpengembalian'] ?? '-') ?></td>
              </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="panel-card mb-0">
    <div class="panel-head">
      <div>
        <span class="eyebrow">TIPS</span>
        <h3>Cara pinjam yang rapi</h3>
      </div>
    </div>
    <div class="panel-body">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div style="display:flex;gap:12px;align-items:flex-start">
          <span class="tile-icon blue" style="width:36px;height:36px;font-size:14px;flex-shrink:0"><i class="fas fa-1"></i></span>
          <div><strong style="font-size:14px">Cek ketersediaan</strong><div style="font-size:13px;color:#64748b">Pastikan alat masih tersedia sebelum mengajukan.</div></div>
        </div>
        <div style="display:flex;gap:12px;align-items:flex-start">
          <span class="tile-icon teal" style="width:36px;height:36px;font-size:14px;flex-shrink:0"><i class="fas fa-2"></i></span>
          <div><strong style="font-size:14px">Ajukan dengan jelas</strong><div style="font-size:13px;color:#64748b">Isi tanggal pinjam & rencana kembali dengan benar.</div></div>
        </div>
        <div style="display:flex;gap:12px;align-items:flex-start">
          <span class="tile-icon purple" style="width:36px;height:36px;font-size:14px;flex-shrink:0"><i class="fas fa-3"></i></span>
          <div><strong style="font-size:14px">Kembalikan tepat waktu</strong><div style="font-size:13px;color:#64748b">Hindari keterlambatan agar alat siap dipakai orang lain.</div></div>
        </div>
      </div>
      <a class="btn btn-primary btn-block mt-4" href="index.php?halaman=daftaralat">
        <i class="fas fa-arrow-right mr-1"></i> Mulai pinjam sekarang
      </a>
    </div>
  </div>
</div>
