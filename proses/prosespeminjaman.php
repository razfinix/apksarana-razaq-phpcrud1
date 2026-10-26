<?php
require_once 'koneksi.php';
require_once 'session.php';
require_once 'helper.php';

$aksi = $_GET['aksi'] ?? '';

function ambilItemPeminjaman(): array {
    $input = $_POST['items'] ?? null;
    if (!is_array($input)) {
        $input = [['idalat' => $_POST['idalat'] ?? 0, 'jumlahpinjam' => $_POST['jumlahpinjam'] ?? 0]];
    }
    $gabung = [];
    foreach ($input as $item) {
        if (!is_array($item)) continue;
        $idalat = (int)($item['idalat'] ?? 0);
        $jumlah = (int)($item['jumlahpinjam'] ?? 0);
        if ($idalat < 1 || $jumlah < 1) continue;
        $gabung[$idalat] = ($gabung[$idalat] ?? 0) + $jumlah;
    }
    if (!$gabung) throw new Exception('Pilih minimal satu alat dan isi jumlahnya dengan benar.');
    return $gabung;
}

function kolomAda(mysqli $koneksi, string $tabel, string $kolom): bool {
    static $cache = [];
    $key = $tabel . '.' . $kolom;
    if (isset($cache[$key])) return $cache[$key];
    $tabel = preg_replace('/[^a-z0-9_]/i', '', $tabel);
    $kolom = preg_replace('/[^a-z0-9_]/i', '', $kolom);
    $q = mysqli_query($koneksi, "SHOW COLUMNS FROM `{$tabel}` LIKE '{$kolom}'");
    $cache[$key] = ($q && mysqli_num_rows($q) > 0);
    return $cache[$key];
}

function simpanPeminjaman(mysqli $koneksi, int $iduser, int $idpeminjam, string $status, array $items, bool $kurangiStok, string $tanggalrencana = ''): int {
    mysqli_begin_transaction($koneksi);
    try {
        $dataAlat = [];
        foreach ($items as $idalat => $jumlah) {
            $stmt = mysqli_prepare($koneksi, 'SELECT idalat,namaalat,jumlah,kondisi FROM alat WHERE idalat=? FOR UPDATE');
            mysqli_stmt_bind_param($stmt, 'i', $idalat);
            mysqli_stmt_execute($stmt);
            $alat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            if (!$alat) throw new Exception('Salah satu alat tidak ditemukan.');
            if ((int)$alat['jumlah'] < $jumlah) throw new Exception('Stok alat "'.$alat['namaalat'].'" tidak cukup. Stok tersedia: '.$alat['jumlah'].'.');
            $dataAlat[$idalat] = $alat;
        }

        $tanggal = date('Y-m-d');
        $pakaiRencana = kolomAda($koneksi, 'peminjaman', 'tanggalrencana');
        $kolomBukti = kolomAda($koneksi, 'peminjaman', 'buktipengembalian')
            ? 'buktipengembalian'
            : (kolomAda($koneksi, 'peminjaman', 'bukti_pengembalian') ? 'bukti_pengembalian' : '');
        $rencana = ($tanggalrencana !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalrencana))
            ? $tanggalrencana
            : date('Y-m-d', strtotime('+7 days'));
        $buktiKosong = '';

        if ($pakaiRencana && $kolomBukti !== '') {
            $sql = "INSERT INTO peminjaman(iduser,idpeminjam,tanggalpinjam,tanggalrencana,status,`$kolomBukti`) VALUES(?,?,?,?,?,?)";
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, 'iissss', $iduser, $idpeminjam, $tanggal, $rencana, $status, $buktiKosong);
        } elseif ($pakaiRencana) {
            $stmt = mysqli_prepare($koneksi, 'INSERT INTO peminjaman(iduser,idpeminjam,tanggalpinjam,tanggalrencana,status) VALUES(?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iisss', $iduser, $idpeminjam, $tanggal, $rencana, $status);
        } elseif ($kolomBukti !== '') {
            $sql = "INSERT INTO peminjaman(iduser,idpeminjam,tanggalpinjam,status,`$kolomBukti`) VALUES(?,?,?,?,?)";
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, 'iisss', $iduser, $idpeminjam, $tanggal, $status, $buktiKosong);
        } else {
            $stmt = mysqli_prepare($koneksi, 'INSERT INTO peminjaman(iduser,idpeminjam,tanggalpinjam,status) VALUES(?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iiss', $iduser, $idpeminjam, $tanggal, $status);
        }

        if (!$stmt || !mysqli_stmt_execute($stmt)) {
            throw new Exception('Gagal membuat transaksi peminjaman: ' . ($stmt ? mysqli_stmt_error($stmt) : mysqli_error($koneksi)));
        }
        $idpeminjaman = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmt);

        $stmtDetail = mysqli_prepare($koneksi, 'INSERT INTO detilpeminjaman(idpeminjaman,idalat,jumlahpinjam,kondisi) VALUES(?,?,?,?)');
        foreach ($items as $idalat => $jumlah) {
            $kondisi = $dataAlat[$idalat]['kondisi'];
            mysqli_stmt_bind_param($stmtDetail, 'iiis', $idpeminjaman, $idalat, $jumlah, $kondisi);
            if (!mysqli_stmt_execute($stmtDetail)) throw new Exception('Gagal menyimpan detail alat peminjaman.');
            if ($kurangiStok) {
                $stmtStok = mysqli_prepare($koneksi, 'UPDATE alat SET jumlah=jumlah-? WHERE idalat=? AND jumlah>=?');
                mysqli_stmt_bind_param($stmtStok, 'iii', $jumlah, $idalat, $jumlah);
                mysqli_stmt_execute($stmtStok);
                if (mysqli_stmt_affected_rows($stmtStok) !== 1) { mysqli_stmt_close($stmtStok); throw new Exception('Stok berubah saat transaksi diproses. Silakan ulangi.'); }
                mysqli_stmt_close($stmtStok);
            }
        }
        mysqli_stmt_close($stmtDetail);
        mysqli_commit($koneksi);
        return $idpeminjaman;
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        throw $e;
    }
}

if ($aksi === 'ajukan') {
    cekLoginPeminjam();
    $iduser = (int)($_SESSION['iduser'] ?? 0);
    $idpeminjam = (int)($_SESSION['idpeminjam'] ?? 0);
    if ($iduser < 1 || $idpeminjam < 1) die('Akun peminjam belum terhubung dengan data peminjam. Hubungi admin.');
    try {
        $items = ambilItemPeminjaman();
        $tanggalrencana = trim((string)($_POST['tanggalrencana'] ?? ''));
        if ($tanggalrencana !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalrencana) || $tanggalrencana < date('Y-m-d'))) {
            throw new Exception('Tanggal rencana pengembalian tidak valid (harus hari ini atau setelahnya).');
        }
        simpanPeminjaman($koneksi, $iduser, $idpeminjam, 'Menunggu', $items, false, $tanggalrencana);
        redirect('../index.php?halaman=riwayatpeminjaman');
    } catch (Throwable $e) {
        die('Gagal mengajukan peminjaman: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . ' <a href="../index.php?halaman=ajukanpeminjaman">Kembali ke formulir</a>');
    }
}

if ($aksi === 'tambah') {
    cekAdminPetugas();
    $idpeminjam = (int)($_POST['idpeminjam'] ?? 0);
    if ($idpeminjam < 1) die('Pilih peminjam terlebih dahulu.');
    try {
        $items = ambilItemPeminjaman();
        $tanggalrencana = trim((string)($_POST['tanggalrencana'] ?? ''));
        if ($tanggalrencana !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalrencana) || $tanggalrencana < date('Y-m-d'))) {
            throw new Exception('Tanggal rencana pengembalian tidak valid (harus hari ini atau setelahnya).');
        }
        simpanPeminjaman($koneksi, (int)$_SESSION['iduser'], $idpeminjam, 'Dipinjam', $items, true, $tanggalrencana);
        redirect('../index.php?halaman=peminjaman');
    } catch (Throwable $e) {
        die('Gagal menyimpan peminjaman: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . ' <a href="../index.php?halaman=createpeminjaman">Kembali ke formulir</a>');
    }
}

if ($aksi === 'status') {
    cekAdminPetugas();
    $id = (int)($_POST['idpeminjaman'] ?? 0);
    $statusBaru = $_POST['status'] ?? '';
    if ($id < 1 || !in_array($statusBaru, ['Dipinjam', 'Ditolak'], true)) die('Status tidak valid.');
    mysqli_begin_transaction($koneksi);
    try {
        $stmt = mysqli_prepare($koneksi, 'SELECT status FROM peminjaman WHERE idpeminjaman=? FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$p) throw new Exception('Data peminjaman tidak ditemukan.');
        if ($p['status'] !== 'Menunggu') throw new Exception('Peminjaman ini sudah diproses.');

        if ($statusBaru === 'Dipinjam') {
            $det = mysqli_query($koneksi, "SELECT idalat, SUM(jumlahpinjam) AS jumlah FROM detilpeminjaman WHERE idpeminjaman=$id GROUP BY idalat");
            while ($d = mysqli_fetch_assoc($det)) {
                $idalat = (int)$d['idalat']; $jumlah = (int)$d['jumlah'];
                $stmt = mysqli_prepare($koneksi, 'SELECT namaalat,jumlah FROM alat WHERE idalat=? FOR UPDATE');
                mysqli_stmt_bind_param($stmt, 'i', $idalat); mysqli_stmt_execute($stmt);
                $a = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
                if (!$a || (int)$a['jumlah'] < $jumlah) throw new Exception('Stok alat "'.($a['namaalat'] ?? 'tidak diketahui').'" tidak cukup untuk menyetujui peminjaman.');
                $stmt = mysqli_prepare($koneksi, 'UPDATE alat SET jumlah=jumlah-? WHERE idalat=? AND jumlah>=?');
                mysqli_stmt_bind_param($stmt, 'iii', $jumlah, $idalat, $jumlah); mysqli_stmt_execute($stmt);
                if (mysqli_stmt_affected_rows($stmt) !== 1) { mysqli_stmt_close($stmt); throw new Exception('Gagal memperbarui stok alat.'); }
                mysqli_stmt_close($stmt);
            }
        }
        $stmt = mysqli_prepare($koneksi, "UPDATE peminjaman SET status=?, tanggalpinjam=CASE WHEN ?='Dipinjam' THEN CURDATE() ELSE tanggalpinjam END WHERE idpeminjaman=? AND status='Menunggu'");
        mysqli_stmt_bind_param($stmt, 'ssi', $statusBaru, $statusBaru, $id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) !== 1) { mysqli_stmt_close($stmt); throw new Exception('Status transaksi sudah berubah.'); }
        mysqli_stmt_close($stmt);
        mysqli_commit($koneksi);
        redirect('../index.php?halaman=peminjaman');
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        die('Gagal memproses status: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . ' <a href="../index.php?halaman=peminjaman">Kembali</a>');
    }
}

http_response_code(400);
die('Aksi tidak dikenali.');