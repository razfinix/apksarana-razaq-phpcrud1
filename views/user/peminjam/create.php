<div class="page-heading"><div><span class="eyebrow">MASTER DATA</span><h1>Tambah Peminjam</h1><p>Tambahkan akun peminjam baru ke dalam sistem.</p></div><a class="btn btn-light" href="index.php?halaman=peminjam"><i class="fas fa-arrow-left mr-1"></i>Kembali</a></div>
<?php $asal=mysqli_query($koneksi,"SELECT * FROM asal ORDER BY namaasal"); ?>
<div class="card modern-card"><div class="card-body p-4">
<form action="proses/prosespeminjam.php?aksi=tambah" method="post">
<div class="row">
<div class="col-lg-6 form-group"><label>Nama Peminjam</label><input class="form-control" name="namapeminjam" required></div>
<div class="col-lg-6 form-group"><label>Kelas / Jabatan</label><input class="form-control" name="kelas" placeholder="Contoh: XI RPL 2 atau Guru" required></div>
<div class="col-lg-6 form-group"><label>Username</label><input class="form-control" name="username" required></div>
<div class="col-lg-6 form-group"><label>Password</label><input class="form-control" type="password" name="password" required></div>
<?php $asalDefault=mysqli_fetch_assoc($asal); ?><input type="hidden" name="idasal" value="<?= (int)($asalDefault['idasal'] ?? 1) ?>">
<div class="col-lg-6 form-group"><label>No. HP</label><input class="form-control" name="nohp"></div>
<div class="col-lg-12 form-group"><label>Lokasi Peminjaman</label><input class="form-control" name="lokasi" placeholder="Contoh: XI RPL 2, Ruang Guru, atau lokasi lain" maxlength="100" required></div>
</div><button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan Peminjam</button> <a class="btn btn-light" href="index.php?halaman=peminjam">Batal</a>
</form></div></div>