<?php
// Riwayat transaksi yang sudah selesai dikembalikan.
$kolomBukti = null;
$cekKolom = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
if ($cekKolom) {
    while ($kol = mysqli_fetch_assoc($cekKolom)) {
        if (in_array($kol['Field'], ['bukti_pengembalian', 'buktipengembalian'], true)) {
            $kolomBukti = $kol['Field'];
            break;
        }
    }
}
$sql = "SELECT p.*, pm.namapeminjam, pm.kelas,
        COUNT(d.iddetilpeminjaman) AS jumlahitem,
        COALESCE(SUM(d.jumlahpinjam),0) AS totalunit
        FROM peminjaman p
        LEFT JOIN peminjam pm ON pm.idpeminjam=p.idpeminjam
        LEFT JOIN detilpeminjaman d ON d.idpeminjaman=p.idpeminjaman
        WHERE p.status='Selesai'
        GROUP BY p.idpeminjaman
        ORDER BY p.idpeminjaman DESC";
$q = mysqli_query($koneksi, $sql);
?>
<div class="page-heading"><div><span class="eyebrow">RIWAYAT TRANSAKSI</span><h1>Barang Sudah Dikembalikan</h1><p>Daftar peminjaman yang statusnya sudah selesai dikembalikan.</p></div><a class="btn btn-outline-primary" href="index.php?halaman=pengembalian"><i class="fas fa-undo mr-1"></i> Barang Belum Kembali</a></div>
<div class="card modern-card"><div class="card-body p-0"><div class="table-responsive"><table class="table modern-table mb-0"><thead><tr><th>ID</th><th>Nama Peminjam</th><th>Kelas</th><th>Tanggal Pinjam</th><th>Tanggal Dikembalikan</th><th>Jenis Alat</th><th>Total Unit</th><th>Bukti Foto</th><th>Status</th></tr></thead><tbody>
<?php if (!$q || mysqli_num_rows($q)===0): ?><tr><td colspan="9" class="text-center text-muted p-4">Belum ada transaksi yang selesai dikembalikan.</td></tr><?php else: while($r=mysqli_fetch_assoc($q)): $bukti=$kolomBukti && !empty($r[$kolomBukti]) ? basename((string)$r[$kolomBukti]) : ''; $lokasi='assets/uploads/pengembalian/'.$bukti; if ($bukti!=='' && !is_file($lokasi)) { $alternatif='assets/uploads/bukti_pengembalian/'.$bukti; if (is_file($alternatif)) $lokasi=$alternatif; } ?>
<tr><td>#<?= (int)$r['idpeminjaman'] ?></td><td><strong><?= htmlspecialchars($r['namapeminjam']??'-') ?></strong></td><td><?= htmlspecialchars($r['kelas']??'-') ?></td><td><?= htmlspecialchars($r['tanggalpinjam']??'-') ?></td><td><?= htmlspecialchars($r['tanggalpengembalian']??'-') ?></td><td><?= (int)$r['jumlahitem'] ?> jenis</td><td><?= (int)$r['totalunit'] ?> unit</td><td><?php if($bukti!=='' && is_file($lokasi)): ?><a href="<?= htmlspecialchars($lokasi) ?>" target="_blank" rel="noopener"><img src="<?= htmlspecialchars($lokasi) ?>" alt="Bukti pengembalian" style="width:70px;height:55px;object-fit:cover;border-radius:8px"></a><?php else: ?><span class="text-muted">Tidak ada foto</span><?php endif; ?></td><td><span class="status-badge status-success">Selesai</span></td></tr>
<?php endwhile; endif; ?>
</tbody></table></div></div></div>
