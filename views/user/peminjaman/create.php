<?php
$pm = mysqli_query($koneksi, "SELECT idpeminjam, namapeminjam, kelas FROM peminjam ORDER BY namapeminjam");
$alat = mysqli_query($koneksi, "SELECT idalat, namaalat, jumlah FROM alat WHERE jumlah > 0 ORDER BY namaalat");
$opsiAlat = '';
while ($a = mysqli_fetch_assoc($alat)) {
    $opsiAlat .= '<option value="'.(int)$a['idalat'].'">'.htmlspecialchars($a['namaalat'], ENT_QUOTES, 'UTF-8').' — stok '.(int)$a['jumlah'].'</option>';
}
?>
<div class="page-heading"><div><span class="eyebrow">TRANSAKSI</span><h1>Tambah Peminjaman</h1><p>Satu transaksi dapat berisi beberapa jenis alat. Setiap alat disimpan sebagai baris detail peminjaman.</p></div></div>
<div class="card modern-card"><div class="card-body p-4">
<form action="proses/prosespeminjaman.php?aksi=tambah" method="post" id="formPeminjamanAdmin">
  <div class="form-group"><label>Nama Peminjam</label><select class="form-control" name="idpeminjam" required><option value="">-- Pilih peminjam --</option><?php while($p=mysqli_fetch_assoc($pm)): ?><option value="<?= (int)$p['idpeminjam'] ?>"><?= htmlspecialchars($p['namapeminjam']) ?> — <?= htmlspecialchars($p['kelas']) ?></option><?php endwhile; ?></select></div>
  <div class="form-group"><label>Tanggal Rencana Pengembalian <small class="text-muted">(opsional)</small></label><input type="date" name="tanggalrencana" class="form-control" min="<?=date('Y-m-d')?>"><small class="text-muted">Boleh dikosongkan jika tidak dipakai.</small></div>
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-2"><label class="mb-0">Daftar Alat yang Dipinjam</label><button type="button" class="btn btn-sm btn-outline-primary" id="tambahBarisAlat"><i class="fas fa-plus mr-1"></i> Tambah Alat</button></div>
  <div id="daftarBarisAlat"><div class="row align-items-end item-peminjaman mb-2"><div class="col-md-7 form-group"><label>Nama Alat</label><select class="form-control" name="items[0][idalat]" required><option value="">-- Pilih alat --</option><?= $opsiAlat ?></select></div><div class="col-md-3 form-group"><label>Jumlah</label><input type="number" class="form-control" name="items[0][jumlahpinjam]" min="1" value="1" required></div><div class="col-md-2 form-group"><button type="button" class="btn btn-outline-danger hapus-baris" disabled>Hapus</button></div></div></div>
  <div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i>Stok akan dicek dan dikurangi setelah transaksi berhasil disimpan.</div>
  <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan Peminjaman</button><a class="btn btn-light" href="index.php?halaman=peminjaman">Batal</a>
</form></div></div>
<script>
(function(){const list=document.getElementById('daftarBarisAlat'),add=document.getElementById('tambahBarisAlat');let next=1;
function update(){const rows=list.querySelectorAll('.item-peminjaman');rows.forEach((row)=>{row.querySelector('.hapus-baris').disabled=rows.length===1;});}
add.addEventListener('click',function(){const row=list.querySelector('.item-peminjaman').cloneNode(true);row.querySelectorAll('select,input').forEach(el=>{el.name=el.name.replace(/items\[\d+\]/,'items['+next+']');if(el.tagName==='SELECT')el.selectedIndex=0;else el.value='1';});list.appendChild(row);next++;update();});
list.addEventListener('click',function(e){if(e.target.classList.contains('hapus-baris')){e.target.closest('.item-peminjaman').remove();update();}});update();})();
</script>
