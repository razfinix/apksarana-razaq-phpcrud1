<?php
require_once 'koneksi.php';
require_once 'session.php';
require_once 'helper.php';
cekAdminPetugas();

$a = $_GET['aksi'] ?? '';

if ($a === 'tambah') {
    $hash = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $nama = trim($_POST['namapeminjam'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $nohp = trim($_POST['nohp'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $idasal = (int)($_POST['idasal'] ?? 0);

    mysqli_begin_transaction($koneksi);
    try {
        $foto = 'default.png';
        $role = 'peminjam';
        $alamatUser = '';
        $s = mysqli_prepare($koneksi, "INSERT INTO user(namauser,username,password,nohp,alamat,foto,role) VALUES(?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($s, 'sssssss', $nama, $username, $hash, $nohp, $alamatUser, $foto, $role);
        mysqli_stmt_execute($s);
        $iduser = mysqli_insert_id($koneksi);
        mysqli_stmt_close($s);

        $s = mysqli_prepare($koneksi, "INSERT INTO peminjam(iduser,idasal,namapeminjam,kelas,nohp,lokasi) VALUES(?,?,?,?,?,?)");
        mysqli_stmt_bind_param($s, 'iissss', $iduser, $idasal, $nama, $kelas, $nohp, $lokasi);
        mysqli_stmt_execute($s);
        mysqli_commit($koneksi);
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        die('Gagal menyimpan peminjam: ' . htmlspecialchars($e->getMessage()));
    }
} elseif ($a === 'edit') {
    $id = (int)($_POST['idpeminjam'] ?? 0);
    $s = mysqli_prepare($koneksi, "UPDATE peminjam SET idasal=?,namapeminjam=?,kelas=?,nohp=?,lokasi=? WHERE idpeminjam=?");
    mysqli_stmt_bind_param($s, 'issssi', $_POST['idasal'], $_POST['namapeminjam'], $_POST['kelas'], $_POST['nohp'], $_POST['lokasi'], $id);
    mysqli_stmt_execute($s);
    mysqli_stmt_close($s);
}

redirect('../index.php?halaman=peminjam');
