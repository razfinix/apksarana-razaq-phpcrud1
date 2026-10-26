<?php
$idalat = (int)($_GET['id'] ?? 0);
$alatDipilih = null;
if ($idalat > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT a.*, k.namakategori FROM alat a LEFT JOIN kategori k ON k.idkategori=a.idkategori WHERE a.idalat=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $idalat);
    mysqli_stmt_execute($stmt);
    $alatDipilih = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$alat = mysqli_query($koneksi, "SELECT a.*, k.namakategori FROM alat a LEFT JOIN kategori k ON k.idkategori=a.idkategori WHERE a.jumlah>0 ORDER BY a.namaalat");
$idpeminjam = (int)($_SESSION['idpeminjam'] ?? 0);
$pm = null;
if ($idpeminjam > 0) {
    $pm = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM peminjam WHERE idpeminjam=$idpeminjam LIMIT 1"));
}
?>
<div class="page-heading">
    <div><span class="eyebrow">PEMINJAMAN</span>
        <h1>Ajukan Peminjaman</h1>
        <p>Pilih satu atau beberapa alat dalam satu pengajuan. Pengajuan menunggu persetujuan petugas.</p>
    </div>
</div>
<div class="card modern-card" style="max-width:900px">
    <div class="card-body p-4">
        <form action="proses/prosespeminjaman.php?aksi=ajukan" method="post" id="formAjukanPeminjaman">
            <div class="form-group"><label>Nama Peminjam</label><input type="text" class="form-control" value="<?= htmlspecialchars($pm['namapeminjam'] ?? $_SESSION['namapeminjam'] ?? '-') ?>" readonly></div>
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2"><label class="mb-0">Daftar Alat yang Ingin Dipinjam</label><button type="button" class="btn btn-sm btn-outline-primary" id="tambahBarisAlat"><i class="fas fa-plus mr-1"></i> Tambah Alat</button></div>
            <div id="daftarBarisAlat">
                <div class="row align-items-end item-peminjaman mb-2">
                    <div class="col-md-7 form-group"><label>Nama Alat</label><select name="items[0][idalat]" class="form-control" required>
                            <option value="">-- Pilih alat --</option><?php mysqli_data_seek($alat, 0);
                                                                        while ($a = mysqli_fetch_assoc($alat)): ?><option value="<?= $a['idalat'] ?>" <?= ($alatDipilih && (int)$alatDipilih['idalat'] === (int)$a['idalat']) ? 'selected' : '' ?>><?= htmlspecialchars($a['namaalat']) ?> — stok <?= $a['jumlah'] ?></option><?php endwhile; ?>
                        </select></div>
                    <div class="col-md-3 form-group"><label>Jumlah</label><input type="number" name="items[0][jumlahpinjam]" class="form-control" min="1" value="1" required></div>
                    <div class="col-md-2 form-group"><button type="button" class="btn btn-outline-danger hapus-baris" disabled>Hapus</button></div>
                </div>
            </div>
            <div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i>Status awal <strong>Menunggu</strong>. Stok tidak berkurang sampai pengajuan disetujui petugas.</div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Ajukan Peminjaman</button><a href="index.php?halaman=daftaralat" class="btn btn-light ml-1">Kembali</a>
        </form>
    </div>
</div>
<script>
    (function() {
        const list = document.getElementById('daftarBarisAlat'),
            add = document.getElementById('tambahBarisAlat');
        let next = 1;

        function update() {
            const rows = list.querySelectorAll('.item-peminjaman');
            rows.forEach(row => row.querySelector('.hapus-baris').disabled = rows.length === 1);
        }
        add.addEventListener('click', function() {
            const row = list.querySelector('.item-peminjaman').cloneNode(true);
            row.querySelectorAll('select,input').forEach(el => {
                el.name = el.name.replace(/items\[\d+\]/, 'items[' + next + ']');
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
                else el.value = '1';
            });
            list.appendChild(row);
            next++;
            update();
        });
        list.addEventListener('click', function(e) {
            if (e.target.classList.contains('hapus-baris')) {
                e.target.closest('.item-peminjaman').remove();
                update();
            }
        });
        update();
    })();
</script>