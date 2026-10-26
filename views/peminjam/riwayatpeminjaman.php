
<?php
$id = (int) ($_SESSION['idpeminjam'] ?? 0);

$sql = "
    SELECT
        p.*,
        GROUP_CONCAT(
            CONCAT(a.namaalat, ' (', d.jumlahpinjam, ')')
            ORDER BY a.namaalat
            SEPARATOR ', '
        ) AS daftaralat
    FROM peminjaman p
    LEFT JOIN detilpeminjaman d
        ON d.idpeminjaman = p.idpeminjaman
    LEFT JOIN alat a
        ON a.idalat = d.idalat
    WHERE p.idpeminjam = $id
    GROUP BY p.idpeminjaman
    ORDER BY p.idpeminjaman DESC
";

$q = mysqli_query($koneksi, $sql);
$adaJatuhTempo = false;
?>

<div class="page-heading">
    <div>
        <span class="eyebrow">AKTIVITAS AKUN</span>
        <h1>Riwayat Peminjaman</h1>
        <p>
            Lihat riwayat peminjaman, pantau status alat,
            dan ajukan pengembalian melalui halaman ini.
        </p>
    </div>

    <a class="btn btn-primary"
        href="index.php?halaman=ajukanpeminjaman">
        <i class="fas fa-plus mr-1"></i>
        Ajukan Peminjaman
    </a>
</div>

<div class="card modern-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table modern-table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Alat</th>
                        <th>Tanggal Dipinjam</th>
                        <th>Batas Pengembalian</th>
                        <th>Tanggal Dikembalikan</th>
                        <th>Status</th>
                        <th>Pengembalian</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!$q): ?>

                        <tr>
                            <td colspan="7" class="text-center text-danger p-4">
                                Data gagal dimuat.
                                Periksa struktur tabel peminjaman dan detailnya.
                            </td>
                        </tr>

                    <?php elseif (mysqli_num_rows($q) === 0): ?>

                        <tr>
                            <td colspan="7" class="text-center text-muted p-4">
                                Kamu belum mempunyai riwayat peminjaman.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php while ($r = mysqli_fetch_assoc($q)): ?>

                            <?php
                            $status = $r['status'] ?? '';

                            // Kolom tanggalrencana belum tersedia
                            // di database yang kamu kirim.
                            $batasPengembalian = $r['tanggalrencana'] ?? null;

                            // Nama kolom bukti mengikuti database kamu.
                            $fotoBukti = $r['buktipengembalian']
                                ?? $r['bukti_pengembalian']
                                ?? '';

                            $tanggalPinjam = $r['tanggalpinjam'] ?? '';
                            $tanggalAktual = $r['tanggalpengembalian'] ?? '';

                            $terlambat = (
                                $status === 'Dipinjam'
                                && !empty($batasPengembalian)
                                && $batasPengembalian < date('Y-m-d')
                            );

                            if ($terlambat) {
                                $adaJatuhTempo = true;
                            }

                            $kelasStatus = 'status-borrowed';

                            if ($status === 'Menunggu') {
                                $kelasStatus = 'status-pending';
                            } elseif ($status === 'Ditolak') {
                                $kelasStatus = 'status-rejected';
                            } elseif ($status === 'Selesai') {
                                $kelasStatus = 'status-done';
                            }
                            ?>

                            <tr>
                                <td>
                                    #<?= (int) $r['idpeminjaman'] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $r['daftaralat'] ?: '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $tanggalPinjam ?: '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?php if (!empty($batasPengembalian)): ?>

                                        <?= htmlspecialchars(
                                            $batasPengembalian,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        <?php if ($terlambat): ?>
                                            <br>
                                            <span class="badge badge-danger">
                                                Terlambat
                                            </span>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <span class="text-muted">
                                            Belum ditentukan
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $tanggalAktual ?: '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <span class="status-badge <?= $kelasStatus ?>">
                                        <?= htmlspecialchars(
                                            $status ?: '-',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($status === 'Dipinjam'): ?>

                                        <form
                                            action="proses/prosespengembalian.php"
                                            method="post"
                                            enctype="multipart/form-data"
                                            class="form-pengembalian-peminjam">

                                            <input
                                                type="hidden"
                                                name="idpeminjaman"
                                                value="<?= (int) $r['idpeminjaman'] ?>">

                                            <label class="small mb-1">
                                                Ambil foto bukti dengan kamera
                                            </label>

                                            <input
                                                type="file"
                                                class="form-control-file mb-2"
                                                name="bukti_pengembalian"
                                                accept="image/*" capture="environment"
                                                required>

                                            <button
                                                class="btn btn-sm btn-success"
                                                type="submit"
                                                onclick="return confirm(
                                                'Pastikan alat sudah diserahkan kepada petugas. Lanjutkan pengembalian?'
                                            )">

                                                <i class="fas fa-undo mr-1"></i>
                                                Kembalikan
                                            </button>
                                        </form>

                                    <?php elseif ($status === 'Menunggu'): ?>

                                        <span class="text-muted">
                                            Menunggu persetujuan
                                        </span>

                                    <?php elseif ($status === 'Selesai' && $fotoBukti !== ''): ?>

                                        <a
                                            target="_blank"
                                            rel="noopener"
                                            href="assets/uploads/bukti_pengembalian/<?= rawurlencode(basename($fotoBukti)) ?>">

                                            <i class="fas fa-image mr-1"></i>
                                            Lihat bukti
                                        </a>

                                    <?php elseif ($status === 'Selesai'): ?>

                                        <span class="text-muted">
                                            Pengembalian selesai
                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">-</span>

                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($adaJatuhTempo): ?>
    <div class="alert alert-danger mt-3">
        <i class="fas fa-bell mr-1"></i>

        <strong>Pengingat pengembalian!</strong>

        Ada alat yang melewati batas pengembalian.
        Segera hubungi petugas dan serahkan alat yang dipinjam.
    </div>
<?php endif; ?>

<script>
    document.querySelectorAll('.form-pengembalian-peminjam')
        .forEach(function(form) {
            form.addEventListener('submit', function(event) {
                const file = form.querySelector('input[type="file"]');

                if (!file || !file.files || file.files.length === 0) {
                    event.preventDefault();
                    alert('Pilih foto bukti pengembalian terlebih dahulu.');
                }
            });
        });
</script>