<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../index.php?halaman=loginuser');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['error_login'] = 'Username dan password wajib diisi.';
    redirect('../index.php?halaman=loginuser');
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM user WHERE username=? LIMIT 1");
if (!$stmt) {
    $_SESSION['error_login'] = 'Database belum sesuai. Jalankan tools/perbaikan_database.sql terlebih dahulu.';
    redirect('../index.php?halaman=loginuser');
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['error_login'] = 'Username tidak ditemukan.';
    redirect('../index.php?halaman=loginuser');
}

if (!isset($data['role'])) {
    $_SESSION['error_login'] = 'Kolom role pada tabel user belum ada. Jalankan tools/perbaikan_database.sql.';
    redirect('../index.php?halaman=loginuser');
}

if (!in_array($data['role'], ['admin','petugas'], true)) {
    $_SESSION['error_login'] = 'Akun ini bukan akun Admin/Petugas.';
    redirect('../index.php?halaman=loginuser');
}

$ok = password_verify($password, (string)$data['password']) || hash_equals((string)$data['password'], (string)$password);
if (!$ok) {
    $_SESSION['error_login'] = 'Password salah.';
    redirect('../index.php?halaman=loginuser');
}

session_regenerate_id(true);
$_SESSION['iduser'] = (int)$data['iduser'];
$_SESSION['namauser'] = $data['namauser'];
$_SESSION['username'] = $data['username'];
$_SESSION['role'] = $data['role'];
$_SESSION['foto'] = $data['foto'] ?? 'default.png';
$_SESSION['login'] = true;
unset($_SESSION['error_login']);

redirect('../index.php?halaman=' . ($data['role'] === 'admin' ? 'dashboardadmin' : 'dashboardpetugas'));
