<?php
require_once 'koneksi.php';
require_once 'session.php';
require_once 'helper.php';

cekAdmin();

$a = $_GET['aksi'] ?? '';
$allowedRoles = ['admin', 'petugas', 'peminjam'];

if ($a === 'tambah') {
    $role = $_POST['role'] ?? '';
    if (!in_array($role, $allowedRoles, true)) {
        redirect('../index.php?halaman=createuser');
    }
    $_POST['nohp'] = $_POST['nohp'] ?? '';
    $_POST['alamat'] = $_POST['alamat'] ?? '';
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $foto = 'default.png';
    $s = mysqli_prepare($koneksi, "INSERT INTO user(namauser,username,password,nohp,alamat,foto,role) VALUES(?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param(
        $s,
        'sssssss',
        $_POST['namauser'],
        $_POST['username'],
        $hash,
        $_POST['nohp'],
        $_POST['alamat'],
        $foto,
        $role
    );
    mysqli_stmt_execute($s);

    // Jika role peminjam, buat baris di tabel peminjam (minimal)
    if ($role === 'peminjam') {
        $iduser = (int)mysqli_insert_id($koneksi);
        $asal = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT idasal FROM asal ORDER BY idasal ASC LIMIT 1"));
        $idasal = (int)($asal['idasal'] ?? 0);
        if ($iduser > 0 && $idasal > 0) {
            $nama = $_POST['namauser'];
            $nohp = $_POST['nohp'] ?? '';
            $stmt = mysqli_prepare($koneksi, "INSERT INTO peminjam(iduser,idasal,namapeminjam,kelas,nohp,lokasi) VALUES(?,?,?,?,?,?)");
            $kelas = '-';
            $lokasi = '-';
            mysqli_stmt_bind_param($stmt, 'iissss', $iduser, $idasal, $nama, $kelas, $nohp, $lokasi);
            mysqli_stmt_execute($stmt);
        }
    }
} elseif ($a === 'edit') {
    $id = (int)$_POST['iduser'];
    $role = $_POST['role'] ?? '';
    if (!in_array($role, $allowedRoles, true)) {
        redirect('../index.php?halaman=user');
    }
    if (($_POST['password'] ?? '') !== '') {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $s = mysqli_prepare($koneksi, "UPDATE user SET namauser=?,username=?,password=?,role=? WHERE iduser=?");
        mysqli_stmt_bind_param($s, 'ssssi', $_POST['namauser'], $_POST['username'], $hash, $role, $id);
    } else {
        $s = mysqli_prepare($koneksi, "UPDATE user SET namauser=?,username=?,role=? WHERE iduser=?");
        mysqli_stmt_bind_param($s, 'sssi', $_POST['namauser'], $_POST['username'], $role, $id);
    }
    mysqli_stmt_execute($s);
} elseif ($a === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    // Hapus data peminjam terkait dulu jika ada
    mysqli_query($koneksi, "DELETE FROM peminjam WHERE iduser=$id");
    mysqli_query($koneksi, "DELETE FROM user WHERE iduser=$id");
}

redirect('../index.php?halaman=user');
