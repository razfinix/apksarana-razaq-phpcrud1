<?php
require_once 'koneksi.php'; require_once 'session.php'; require_once 'helper.php';
if (!in_array($_SESSION['role'] ?? '', ['admin','petugas'], true)) exit('403');
function simpanFotoKategori(): ?string {
 if (empty($_FILES['foto']) || ($_FILES['foto']['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return null;
 $f=$_FILES['foto']; if($f['error']!==UPLOAD_ERR_OK || $f['size']>3*1024*1024) throw new Exception('Gambar gagal diunggah atau melebihi 3 MB.');
 $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']); $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;
 if(!$ext || getimagesize($f['tmp_name'])===false) throw new Exception('Format gambar harus JPG, PNG, atau WEBP.');
 $name=bin2hex(random_bytes(16)).'.'.$ext; $dir=__DIR__.'/../assets/uploads/kategori/'; if(!is_dir($dir)) mkdir($dir,0755,true);
 if(!move_uploaded_file($f['tmp_name'],$dir.$name)) throw new Exception('Gagal menyimpan gambar kategori.'); return $name;
}
$a=$_GET['aksi']??'';
try {
 if($a==='tambah') { $nama=trim($_POST['namakategori']??''); if($nama==='') throw new Exception('Nama kategori wajib diisi.'); $foto=simpanFotoKategori(); $s=mysqli_prepare($koneksi,'INSERT INTO kategori(namakategori,foto) VALUES(?,?)'); mysqli_stmt_bind_param($s,'ss',$nama,$foto); mysqli_stmt_execute($s); mysqli_stmt_close($s); }
 elseif($a==='edit') { $id=(int)$_POST['idkategori']; $nama=trim($_POST['namakategori']??''); if($nama==='') throw new Exception('Nama kategori wajib diisi.'); $foto=simpanFotoKategori(); if($foto){$s=mysqli_prepare($koneksi,'UPDATE kategori SET namakategori=?,foto=? WHERE idkategori=?');mysqli_stmt_bind_param($s,'ssi',$nama,$foto,$id);}else{$s=mysqli_prepare($koneksi,'UPDATE kategori SET namakategori=? WHERE idkategori=?');mysqli_stmt_bind_param($s,'si',$nama,$id);} mysqli_stmt_execute($s);mysqli_stmt_close($s); }
 elseif($a==='hapus') mysqli_query($koneksi,'DELETE FROM kategori WHERE idkategori='.(int)$_GET['id']);
 redirect('../index.php?halaman=kategori');
} catch(Throwable $e) { die('Gagal menyimpan kategori: '.htmlspecialchars($e->getMessage(),ENT_QUOTES,'UTF-8').' <a href="../index.php?halaman=kategori">Kembali</a>'); }
