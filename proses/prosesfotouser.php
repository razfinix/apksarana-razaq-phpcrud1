<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/helper.php';
if (empty($_SESSION['iduser'])) redirect('../index.php?halaman=loginuser');
$role = (string)($_SESSION['role'] ?? '');
$halamanProfil = $role === 'peminjam' ? 'profil' : 'profilakun';
$redirectProfil = '../index.php?halaman=' . $halamanProfil;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect($redirectProfil);
if (!isset($_FILES['foto_profil']) || $_FILES['foto_profil']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error_foto'] = 'Pilih foto terlebih dahulu. Jika gagal, coba pilih file lain.';
    redirect($redirectProfil);
}
$file = $_FILES['foto_profil'];
if ((int)$file['size'] > 3 * 1024 * 1024) {
    $_SESSION['error_foto'] = 'Ukuran foto maksimal 3 MB.';
    redirect($redirectProfil);
}
$info = @getimagesize($file['tmp_name']);
$mime = $info['mime'] ?? '';
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if (!$info || !isset($allowed[$mime])) {
    $_SESSION['error_foto'] = 'Format foto harus JPG, PNG, atau WEBP.';
    redirect($redirectProfil);
}
$folder = __DIR__ . '/../assets/uploads/foto_user';
if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
    $_SESSION['error_foto'] = 'Folder penyimpanan foto tidak bisa dibuat. Periksa izin folder assets.';
    redirect($redirectProfil);
}
$idUser = (int)$_SESSION['iduser'];
$namaFile = 'user_' . $idUser . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
$tujuan = $folder . '/' . $namaFile;
if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
    $_SESSION['error_foto'] = 'Foto gagal disimpan. Periksa izin folder assets/uploads.';
    redirect($redirectProfil);
}
$stmt = mysqli_prepare($koneksi, 'UPDATE user SET foto = ? WHERE iduser = ?');
if (!$stmt) { @unlink($tujuan); $_SESSION['error_foto'] = 'Database tidak dapat menyimpan foto. Coba lagi.'; redirect($redirectProfil); }
mysqli_stmt_bind_param($stmt, 'si', $namaFile, $idUser);
$berhasil = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
if (!$berhasil) { @unlink($tujuan); $_SESSION['error_foto'] = 'Foto gagal diperbarui pada database.'; redirect($redirectProfil); }
$fotoLama = basename((string)($_SESSION['foto'] ?? ''));
$_SESSION['foto'] = $namaFile;
if ($fotoLama !== '' && $fotoLama !== 'default.png' && $fotoLama !== $namaFile && strpos($fotoLama, 'user_' . $idUser . '_') === 0) {
    $lama = $folder . '/' . $fotoLama;
    if (is_file($lama)) @unlink($lama);
}
$_SESSION['success_foto'] = 'Foto profil berhasil diperbarui.';
redirect($redirectProfil);
