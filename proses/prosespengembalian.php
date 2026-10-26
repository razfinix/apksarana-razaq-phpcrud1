<?php
require_once 'koneksi.php';
require_once 'session.php';
require_once 'helper.php';
$role = $_SESSION['role'] ?? '';
$isPeminjam = $role === 'peminjam';
if ($isPeminjam) {
    cekLoginPeminjam();
} else {
    cekAdminPetugas();
}
$id = (int)($_POST['idpeminjaman'] ?? 0);
// Terima nama input foto dari kedua versi formulir.
$file = $_FILES['bukti_pengembalian'] ?? $_FILES['buktipengembalian'] ?? null;
if (!$file || !isset($file['error']) || (int)$file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
    $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    $pesan = ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE)
        ? 'Ukuran foto melewati batas upload PHP. Coba foto yang lebih kecil.'
        : 'Foto bukti pengembalian belum diterima. Pilih foto pada kolom bukti lalu coba lagi.';
    die(htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') . ' <a href="../index.php?halaman=riwayatpeminjaman">Kembali</a>');
}
if ($file['size'] > 3 * 1024 * 1024) die('Foto bukti maksimal 3 MB.');
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
$ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
if (!$ext || getimagesize($file['tmp_name']) === false) die('Format bukti harus JPG, PNG, atau WEBP.');
$namaBukti = bin2hex(random_bytes(16)) . '.' . $ext;
$folderBukti = __DIR__ . '/../assets/uploads/bukti_pengembalian/';
if (!is_dir($folderBukti)) mkdir($folderBukti, 0755, true);
if (!move_uploaded_file($file['tmp_name'], $folderBukti . $namaBukti)) die('Gagal menyimpan foto bukti pengembalian.');
if ($id < 1) die('Peminjaman tidak valid.');
mysqli_begin_transaction($koneksi);
try {
    $stmt = mysqli_prepare($koneksi, 'SELECT status,idpeminjam FROM peminjaman WHERE idpeminjaman=? FOR UPDATE');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$cek) throw new Exception('Data peminjaman tidak ditemukan.');
    if ($isPeminjam && (int)$cek['idpeminjam'] !== (int)($_SESSION['idpeminjam'] ?? 0)) throw new Exception('Kamu hanya dapat mengembalikan peminjaman milik akunmu sendiri.');
    if ($cek['status'] !== 'Dipinjam') throw new Exception('Hanya peminjaman berstatus Dipinjam yang dapat diproses pengembaliannya.');

    $det = mysqli_query($koneksi, "SELECT idalat, SUM(jumlahpinjam) AS jumlah FROM detilpeminjaman WHERE idpeminjaman=$id GROUP BY idalat");
    while ($r = mysqli_fetch_assoc($det)) {
        $jumlah = (int)$r['jumlah'];
        $idalat = (int)$r['idalat'];
        $stmt = mysqli_prepare($koneksi, 'UPDATE alat SET jumlah=jumlah+? WHERE idalat=?');
        mysqli_stmt_bind_param($stmt, 'ii', $jumlah, $idalat);
        if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
            mysqli_stmt_close($stmt);
            throw new Exception('Stok alat gagal dikembalikan.');
        }
        mysqli_stmt_close($stmt);
    }
    // Pilih nama kolom foto yang memang ada pada database pengguna.
    $kolomBukti = null;
    $resKolom = mysqli_query($koneksi, 'SHOW COLUMNS FROM peminjaman');
    if ($resKolom) {
        while ($kol = mysqli_fetch_assoc($resKolom)) {
            if (in_array($kol['Field'], ['buktipengembalian', 'bukti_pengembalian'], true)) {
                $kolomBukti = $kol['Field'];
                break;
            }
        }
    }
    if ($kolomBukti === null) throw new Exception('Kolom foto bukti belum tersedia pada tabel peminjaman. Periksa struktur database.');
    $sqlUpdate = "UPDATE peminjaman SET status='Selesai', `$kolomBukti`=?, tanggalpengembalian=CURDATE() WHERE idpeminjaman=? AND status='Dipinjam'";
    $stmt = mysqli_prepare($koneksi, $sqlUpdate);
    if (!$stmt) throw new Exception('Gagal menyiapkan penyimpanan foto: ' . mysqli_error($koneksi));
    mysqli_stmt_bind_param($stmt, 'si', $namaBukti, $id);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        mysqli_stmt_close($stmt);
        throw new Exception('Status transaksi berubah. Pengembalian dibatalkan.');
    }
    mysqli_stmt_close($stmt);
    mysqli_commit($koneksi);
    redirect($isPeminjam ? '../index.php?halaman=riwayatpeminjaman' : '../index.php?halaman=pengembalian');
} catch (Throwable $e) {
    mysqli_rollback($koneksi);
    die('Gagal memproses pengembalian: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . ' <a href="../index.php?halaman=' . ($isPeminjam ? 'riwayatpeminjaman' : 'peminjaman') . '">Kembali</a>');
}
