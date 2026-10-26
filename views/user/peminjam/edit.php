<?php $id=(int)($_GET['id']??0); $r=mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM peminjam WHERE idpeminjam=$id")); $asal=mysqli_query($koneksi,"SELECT * FROM asal ORDER BY namaasal"); ?>
<div class="page-heading"><div><span class="eyebrow">MASTER DATA</span><h1>Edit Peminjam</h1><p>Perbarui data peminjam tanpa mengubah struktur akun.</p></div><a class="btn btn-light" href="index.php?halaman=peminjam"><i class="fas fa-arrow-left mr-1"></i>Kembali</a></div>
<div class="card modern-card"><div class="card-body p-4">
<form action="proses/prosespeminjam.php?aksi=edit" method="post"><input type="hidden" name="idpeminjam" value="<?=$r['idpeminjam']?>">
<div class="row"><div class="col-lg-6 form-group"><label>Nama Peminjam</label><input class="form-control" name="namapeminjam" value="<?=htmlspecialchars($r['namapeminjam'])?>" required></div>
<div class="col-lg-6 form-group"><label>Kelas / Jabatan</label><input class="form-control" name="kelas" value="<?=htmlspecialchars($r['kelas'])?>" placeholder="Contoh: XI RPL 2 atau Guru" required></div>
<input type="hidden" name="idasal" value="<?= (int)$r['idasal'] ?>">
<div class="col-lg-6 form-group"><label>No. HP</label><input class="form-control" name="nohp" value="<?=htmlspecialchars($r['nohp'])?>"></div>
<div class="col-lg-12 form-group"><label>Lokasi Peminjaman</label><input class="form-control" name="lokasi" value="<?=htmlspecialchars($r['lokasi'])?>" placeholder="Contoh: XI RPL 2 atau Ruang Guru" maxlength="100" required></div></div>
<button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button> <a class="btn btn-light" href="index.php?halaman=peminjam">Batal</a></form></div></div>