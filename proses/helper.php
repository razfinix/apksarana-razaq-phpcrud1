<?php
function rupiah($angka){ return 'Rp '.number_format((float)$angka,0,',','.'); }
function angka($angka){ return number_format((float)$angka,0,',','.'); }
function tanggalIndonesia($tanggal){ if(!$tanggal) return '-'; $b=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; $t=strtotime($tanggal); return $t?date('d',$t).' '.$b[(int)date('m',$t)-1].' '.date('Y',$t):'-'; }
function redirect($url){ header('Location: '.$url); exit; }
function cekLoginUser(){ if(!isset($_SESSION['iduser'])) redirect('index.php?halaman=loginuser'); }
function cekLoginPeminjam(){ if(!isset($_SESSION['iduser']) || ($_SESSION['role']??'')!=='peminjam') redirect('index.php?halaman=loginpeminjam'); }
function cekAdmin(){ if(($_SESSION['role']??'')!=='admin'){ http_response_code(403); include __DIR__.'/../views/errors/403.php'; exit; } }
function cekPetugas(){ if(($_SESSION['role']??'')!=='petugas'){ http_response_code(403); include __DIR__.'/../views/errors/403.php'; exit; } }
function cekAdminPetugas(){ if(!in_array($_SESSION['role']??'', ['admin','petugas'], true)){ http_response_code(403); include __DIR__.'/../views/errors/403.php'; exit; } }
function bersih($data){ global $koneksi; return mysqli_real_escape_string($koneksi, trim((string)$data)); }
function totalData($koneksi,$tabel){ $allowed=['user','peminjam','asal','kategori','alat','peminjaman','detilpeminjaman']; if(!in_array($tabel,$allowed,true)) return 0; $q=mysqli_query($koneksi,"SELECT COUNT(*) total FROM `$tabel`"); return (int)(mysqli_fetch_assoc($q)['total']??0); }
function menuAktif($menu){ return (($_GET['halaman']??'')===$menu)?'active':''; }
function fotoUser($foto=''){
    $default = 'assets/dist/img/avatar.png';
    $foto = basename((string)$foto);
    if ($foto === '' || $foto === '.' || $foto === '..') return $default;
    $path = __DIR__ . '/../assets/uploads/foto_user/' . $foto;
    return is_file($path) ? 'assets/uploads/foto_user/' . rawurlencode($foto) : $default;
}
