<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php?halaman=registerpeminjam');
}

$nama = trim($_POST['namapeminjam'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$kelas = trim($_POST['kelas'] ?? '');
$nohp = trim($_POST['nohp'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');

if ($nama === '' || $username === '' || $password === '' || $kelas === '' || $lokasi === '') {
    $_SESSION['error_register'] = 'Data wajib diisi lengkap.';
    redirect('../index.php?halaman=registerpeminjam');
}

$asalDefault = mysqli_query($koneksi, "SELECT idasal FROM asal ORDER BY idasal ASC LIMIT 1");
$rowAsal = $asalDefault ? mysqli_fetch_assoc($asalDefault) : null;
$idasal = (int)($rowAsal['idasal'] ?? 0);

if ($idasal < 1) {
    $_SESSION['error_register'] = 'Data lokasi peminjaman belum tersedia. Minta admin menambahkan lokasi terlebih dahulu.';
    redirect('../index.php?halaman=registerpeminjam');
}

$cek = mysqli_prepare($koneksi, "SELECT iduser FROM user WHERE username=? LIMIT 1");
mysqli_stmt_bind_param($cek, 's', $username);
mysqli_stmt_execute($cek);
if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
    mysqli_stmt_close($cek);
    $_SESSION['error_register'] = 'Username sudah digunakan.';
    redirect('../index.php?halaman=registerpeminjam');
}
mysqli_stmt_close($cek);

mysqli_begin_transaction($koneksi);
try {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'peminjam';
    $foto = 'default.png';
    $alamatUser = ''; // kolom user.alamat tetap ada demi kompatibilitas database.

    $stmt = mysqli_prepare($koneksi, "INSERT INTO user(namauser,username,password,nohp,alamat,foto,role) VALUES(?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'sssssss', $nama, $username, $hash, $nohp, $alamatUser, $foto, $role);
    mysqli_stmt_execute($stmt);
    $iduser = mysqli_insert_id($koneksi);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($koneksi, "INSERT INTO peminjam(iduser,idasal,namapeminjam,kelas,nohp,lokasi) VALUES(?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iissss', $iduser, $idasal, $nama, $kelas, $nohp, $lokasi);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($koneksi);
    redirect('../index.php?halaman=loginpeminjam&daftar=sukses');
} catch (Throwable $e) {
    mysqli_rollback($koneksi);
    $_SESSION['error_register'] = 'Registrasi gagal: ' . $e->getMessage();
    redirect('../index.php?halaman=registerpeminjam');
}
