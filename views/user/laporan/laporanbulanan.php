<?php
$judulLaporan = 'Laporan Bulanan Peminjaman Sarana';
$periodeLaporan = 'Bulanan';
$dariDefault = date('Y-m-01');
$sampaiDefault = date('Y-m-t');
$dari = trim((string)($_GET['dari'] ?? $dariDefault));
$sampai = trim((string)($_GET['sampai'] ?? $sampaiDefault));
$validDate = static function(string $date): bool {
    $dt = DateTime::createFromFormat('!Y-m-d', $date);
    return $dt && $dt->format('Y-m-d') === $date;
};
if (!$validDate($dari)) $dari = $dariDefault;
if (!$validDate($sampai)) $sampai = $sampaiDefault;
if ($dari > $sampai) { $tmp = $dari; $dari = $sampai; $sampai = $tmp; }
$sqlLaporan = "SELECT p.idpeminjaman, p.tanggalpinjam, p.status,
    pm.namapeminjam, pm.kelas,
    GROUP_CONCAT(CONCAT(COALESCE(a.namaalat, 'Sarana'), ' (', COALESCE(d.jumlahpinjam, 0), ')') ORDER BY a.namaalat SEPARATOR ', ') AS daftar_alat,
    COALESCE(SUM(d.jumlahpinjam), 0) AS total_unit
 FROM peminjaman p
 LEFT JOIN peminjam pm ON pm.idpeminjam = p.idpeminjam
 LEFT JOIN detilpeminjaman d ON d.idpeminjaman = p.idpeminjaman
 LEFT JOIN alat a ON a.idalat = d.idalat
 WHERE p.tanggalpinjam BETWEEN ? AND ?
 GROUP BY p.idpeminjaman, p.tanggalpinjam, p.status, pm.namapeminjam, pm.kelas
 ORDER BY p.tanggalpinjam DESC, p.idpeminjaman DESC";
$stmtLaporan = mysqli_prepare($koneksi, $sqlLaporan);
mysqli_stmt_bind_param($stmtLaporan, 'ss', $dari, $sampai);
mysqli_stmt_execute($stmtLaporan);
$qLaporan = mysqli_stmt_get_result($stmtLaporan);
if (!$qLaporan) { echo '<div class="alert alert-danger">Laporan gagal dimuat. Periksa struktur tabel peminjaman.</div>'; return; }
$jumlahTransaksi = mysqli_num_rows($qLaporan);
$periodeCetak = date('d-m-Y', strtotime($dari)) . ' s.d. ' . date('d-m-Y', strtotime($sampai));
?>
<style>
.report-shell{background:#fff;border:1px solid #dce6f5;border-radius:18px;padding:28px;box-shadow:0 12px 35px rgba(16,55,115,.08);color:#172b4d}
.report-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px}
.report-head{text-align:center;border-bottom:3px double #183d78;padding:0 0 16px;margin-bottom:20px}
.report-head h2{font-size:21px;font-weight:800;margin:4px 0;color:#15366b;text-transform:uppercase}
.report-head p{margin:2px 0;color:#344765}
.report-mark{height:5px;background:linear-gradient(90deg,#123b7a,#2e72d2,#6cbcff);border-radius:99px;margin-bottom:14px}
.report-table{width:100%;border-collapse:collapse;font-size:12px}
.report-table th{background:#eaf2ff;color:#173d79;border:1px solid #b8cbe8;padding:9px 7px;text-align:center}
.report-table td{border:1px solid #cbd6e5;padding:8px 7px;vertical-align:top}
.report-table tbody tr:nth-child(even){background:#f7faff}
.report-summary{display:flex;gap:12px;margin:14px 0 20px;flex-wrap:wrap}
.report-summary div{border:1px solid #d8e5f7;background:#f4f8ff;border-radius:10px;padding:10px 14px;min-width:160px}
.report-summary strong{display:block;color:#17468d;font-size:20px}
.report-signatures{display:grid;grid-template-columns:1fr 1fr;gap:45px;margin:34px 0 0;text-align:center;break-inside:avoid}
.report-signatures .sign-space{height:70px}
.report-signatures strong{display:block;text-decoration:underline}
.report-note{font-size:11px;color:#5d6d84;margin-top:12px}
@media(max-width:700px){.report-shell{padding:14px}.report-table{font-size:10px}.report-table th,.report-table td{padding:5px 4px}.report-signatures{gap:20px}}
@media print{.no-print{display:none!important}@page{size:A4 landscape;margin:15mm}body{background:#fff!important}.main-header,.main-sidebar,.main-footer,.report-toolbar,.content-header,.mobile-sidebar-overlay{display:none!important}.content-wrapper,.wrapper,.content{margin:0!important;padding:0!important;background:#fff!important;min-height:0!important}.report-shell{border:0;box-shadow:none;border-radius:0;padding:0}.report-head h2{font-size:18px}.report-table{font-size:10px}.report-table tr{break-inside:avoid}.report-signatures{break-inside:avoid}}
</style>
<div class="report-toolbar no-print" style="align-items:flex-start;flex-wrap:wrap">
 <form method="get" action="index.php" class="d-flex flex-wrap align-items-end" style="gap:10px">
  <input type="hidden" name="halaman" value="laporanbulanan">
  <div><label for="tanggalDari" class="mb-1">Dari tanggal</label><input id="tanggalDari" class="form-control" type="date" name="dari" value="<?= htmlspecialchars($dari) ?>" required></div>
  <div><label for="tanggalSampai" class="mb-1">Sampai tanggal</label><input id="tanggalSampai" class="form-control" type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>" required></div>
  <button class="btn btn-primary" type="submit"><i class="fas fa-filter mr-1"></i> Filter</button>
  <a class="btn btn-outline-secondary" href="index.php?halaman=laporanbulanan">Reset</a>
 </form>
 <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print mr-1"></i> Cetak / Simpan PDF</button>
</div>
<section class="report-shell">
 <div class="report-mark"></div>
 <header class="report-head">
  <p style="font-weight:700;letter-spacing:.7px">PEMERINTAH PROVINSI ACEH</p>
  <h2>SMK NEGERI 1 KARANG BARU</h2>
  <p>Administrasi Sarana dan Prasarana Sekolah</p>
  <p style="font-size:11px">Dokumen laporan sistem peminjaman sarana</p>
 </header>
 <h3 style="text-align:center;font-size:17px;font-weight:800;margin-bottom:4px">LAPORAN BULANAN PEMINJAMAN SARANA</h3>
 <p style="text-align:center;margin-bottom:18px">Periode: <?= htmlspecialchars($periodeCetak) ?></p>
 <div class="report-summary">
  <div><span>Total transaksi</span><strong><?= $jumlahTransaksi ?></strong></div>
  <div><span>Jenis laporan</span><strong style="font-size:16px"><?= htmlspecialchars($periodeLaporan) ?></strong></div>
  <div><span>Tanggal cetak</span><strong style="font-size:16px"><?= date('d-m-Y') ?></strong></div>
 </div>
 <div class="table-responsive">
  <table class="report-table">
   <thead><tr><th style="width:35px">No.</th><th style="width:65px">ID Transaksi</th><th>Nama Peminjam</th><th style="width:90px">Kelas</th><th>Nama Sarana (Jumlah)</th><th style="width:95px">Tanggal Pinjam</th><th style="width:90px">Status</th></tr></thead>
   <tbody>
   <?php if ($jumlahTransaksi === 0): ?><tr><td colspan="7" style="text-align:center;padding:20px">Belum ada data peminjaman pada periode ini.</td></tr>
   <?php else: $no=1; while($row=mysqli_fetch_assoc($qLaporan)): ?>
    <tr><td style="text-align:center"><?= $no++ ?></td><td style="text-align:center">#<?= (int)$row['idpeminjaman'] ?></td><td><?= htmlspecialchars($row['namapeminjam'] ?? '-') ?></td><td><?= htmlspecialchars($row['kelas'] ?? '-') ?></td><td><?= htmlspecialchars($row['daftar_alat'] ?: '-') ?></td><td style="text-align:center"><?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggalpinjam']))) ?></td><td style="text-align:center"><?= htmlspecialchars($row['status']) ?></td></tr>
   <?php endwhile; endif; ?>
   </tbody>
  </table>
 </div>
 <p class="report-note">Catatan: angka jumlah pada kolom sarana menunjukkan jumlah unit yang tercatat pada transaksi. Laporan dibuat berdasarkan data transaksi di aplikasi.</p>
 <div class="report-signatures">
  <div><p>Mengetahui,</p><p><strong>Kepala Sekolah</strong></p><div class="sign-space"></div><strong>( ........................................ )</strong><p>NIP. ....................................</p></div>
  <div><p><?= htmlspecialchars(date('d F Y')) ?></p><p><strong>Kepala Sarana dan Prasarana</strong></p><div class="sign-space"></div><strong>( ........................................ )</strong><p>NIP. ....................................</p></div>
 </div>
</section>
