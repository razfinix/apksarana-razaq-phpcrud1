<?php
require_once 'koneksi.php'; require_once 'session.php'; require_once 'helper.php';
if (!in_array($_SESSION['role'] ?? '', ['admin','petugas'], true)) exit('403');
function simpanFotoSarana(string $field, string $folder): ?string {
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 3*1024*1024) throw new Exception('Foto gagal diunggah atau melebihi 3 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
    if (!$ext || getimagesize($f['tmp_name']) === false) throw new Exception('Format foto harus JPG, PNG, atau WEBP.');
    $name = bin2hex(random_bytes(16)).'.'.$ext;
    $dir = __DIR__.'/../assets/uploads/'.$folder.'/';
    if (!is_dir($dir)) mkdir($dir,0755,true);
    if (!move_uploaded_file($f['tmp_name'], $dir.$name)) throw new Exception('Gagal menyimpan foto.');
    return $name;
}
$a=$_GET['aksi']??'';
try {
 if ($a==='tambah') {
  $foto=simpanFotoSarana('foto','sarana') ?? 'default.png';
  $s=mysqli_prepare($koneksi,'INSERT INTO alat(idkategori,namaalat,jumlah,kondisi,lokasi,foto) VALUES(?,?,?,?,?,?)');
  mysqli_stmt_bind_param($s,'isisss',$_POST['idkategori'],$_POST['namaalat'],$_POST['jumlah'],$_POST['kondisi'],$_POST['lokasi'],$foto); mysqli_stmt_execute($s); mysqli_stmt_close($s);
 } elseif ($a==='edit') {
  $id=(int)$_POST['idalat']; $foto=simpanFotoSarana('foto','sarana');
  if ($foto) { $s=mysqli_prepare($koneksi,'UPDATE alat SET idkategori=?,namaalat=?,jumlah=?,kondisi=?,lokasi=?,foto=? WHERE idalat=?'); mysqli_stmt_bind_param($s,'isisssi',$_POST['idkategori'],$_POST['namaalat'],$_POST['jumlah'],$_POST['kondisi'],$_POST['lokasi'],$foto,$id); }
  else { $s=mysqli_prepare($koneksi,'UPDATE alat SET idkategori=?,namaalat=?,jumlah=?,kondisi=?,lokasi=? WHERE idalat=?'); mysqli_stmt_bind_param($s,'isissi',$_POST['idkategori'],$_POST['namaalat'],$_POST['jumlah'],$_POST['kondisi'],$_POST['lokasi'],$id); }
  mysqli_stmt_execute($s); mysqli_stmt_close($s);
 } elseif ($a==='hapus') mysqli_query($koneksi,'DELETE FROM alat WHERE idalat='.(int)$_GET['id']);
 redirect('../index.php?halaman=alat');
} catch(Throwable $e) { die('Gagal menyimpan data sarana: '.htmlspecialchars($e->getMessage(),ENT_QUOTES,'UTF-8').' <a href="../index.php?halaman=alat">Kembali</a>'); }
