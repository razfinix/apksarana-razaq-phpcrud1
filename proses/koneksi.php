<?php
/**
 * Konfigurasi database.
 * Untuk production: ganti host, username, password, database sesuai server hosting.
 * Bisa juga pakai environment variable: DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_SOCKET.
 */
$db = [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'database' => getenv('DB_NAME') ?: 'saranarazaq',
    'socket'   => getenv('DB_SOCKET') ?: '', // biarkan kosong kecuali di environment khusus
];

if ($db['socket'] !== '') {
    $koneksi = @mysqli_connect($db['host'], $db['username'], $db['password'], $db['database'], null, $db['socket']);
} else {
    $koneksi = @mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
}

if (!$koneksi) {
    http_response_code(503);
    die(
        '<div style="font-family:Arial,sans-serif;padding:25px;max-width:560px;margin:40px auto;border:1px solid #fcc;background:#fff5f5;border-radius:8px">'
        . '<h3 style="margin-top:0;color:#c00">Koneksi database gagal</h3>'
        . '<p>' . htmlspecialchars(mysqli_connect_error() ?: 'Tidak dapat terhubung ke MySQL/MariaDB.', ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p>Pastikan MySQL aktif dan database <b>saranarazaq</b> sudah diimpor dari <code>tools/saranarazaq.sql</code>.</p>'
        . '<p>Edit file <code>proses/koneksi.php</code> atau set environment variable DB_HOST, DB_USER, DB_PASS, DB_NAME.</p>'
        . '</div>'
    );
}
mysqli_set_charset($koneksi, 'utf8mb4');

if (getenv('APP_ENV') === 'production') {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}
