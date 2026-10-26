<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php?halaman=loginpeminjam');
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['error_login'] = 'Username dan password wajib diisi.';
    redirect('../index.php?halaman=loginpeminjam');
}

/*
 * Cari akun berdasarkan username saja terlebih dahulu.
 * Role dicek setelah data ditemukan supaya akun baru tidak gagal
 * hanya karena perbedaan huruf besar/kecil atau spasi pada role.
 */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT u.*, p.idpeminjam, p.namapeminjam
     FROM user u
     LEFT JOIN peminjam p ON p.iduser = u.iduser
     WHERE LOWER(TRIM(u.username)) = LOWER(TRIM(?))
     LIMIT 1"
);

if (!$stmt) {
    $_SESSION['error_login'] = 'Query login gagal: ' . mysqli_error($koneksi);
    redirect('../index.php?halaman=loginpeminjam');
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['error_login'] = 'Username tidak ditemukan.';
    redirect('../index.php?halaman=loginpeminjam');
}

$role = strtolower(trim((string)($data['role'] ?? '')));
if ($role !== 'peminjam') {
    $_SESSION['error_login'] = 'Akun ini bukan akun peminjam.';
    redirect('../index.php?halaman=loginpeminjam');
}

if (empty($data['idpeminjam'])) {
    $_SESSION['error_login'] = 'Akun sudah ada, tetapi data peminjam belum terhubung.';
    redirect('../index.php?halaman=loginpeminjam');
}

$storedPassword = (string)($data['password'] ?? '');
$ok = false;

/* Mendukung password lama plaintext dan password baru hasil password_hash(). */
if ($storedPassword !== '') {
    $ok = password_verify($password, $storedPassword);
    if (!$ok) {
        $ok = hash_equals($storedPassword, $password);
    }
}

if (!$ok) {
    $_SESSION['error_login'] = 'Password salah. Gunakan password yang sama seperti saat register.';
    redirect('../index.php?halaman=loginpeminjam');
}

/* Kalau password lama masih plaintext, ubah menjadi hash setelah login berhasil. */
if (!password_verify($password, $storedPassword)) {
    $hashBaru = password_hash($password, PASSWORD_DEFAULT);
    $update = mysqli_prepare($koneksi, "UPDATE user SET password=? WHERE iduser=?");
    if ($update) {
        $iduserUpdate = (int)$data['iduser'];
        mysqli_stmt_bind_param($update, 'si', $hashBaru, $iduserUpdate);
        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);
    }
}

session_regenerate_id(true);
$_SESSION['iduser'] = (int)$data['iduser'];
$_SESSION['idpeminjam'] = (int)$data['idpeminjam'];
$_SESSION['namauser'] = $data['namapeminjam'] ?: $data['namauser'];
$_SESSION['namapeminjam'] = $data['namapeminjam'] ?: $data['namauser'];
$_SESSION['username'] = $data['username'];
$_SESSION['role'] = 'peminjam';
$_SESSION['foto'] = $data['foto'] ?? 'default.png';
$_SESSION['login'] = true;
unset($_SESSION['error_login']);

redirect('../index.php?halaman=dashboardpeminjam');
