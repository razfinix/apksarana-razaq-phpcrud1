<?php $q = mysqli_query($koneksi, "SELECT a.*,k.namakategori FROM alat a LEFT JOIN kategori k ON k.idkategori=a.idkategori ORDER BY a.namaalat");
function fotoBawaanAlat($nama)
{
    $n = strtolower($nama);
    if (strpos($n, 'laptop') !== false || strpos($n, 'komputer') !== false) return 'laptop_asus.jpg';
    if (strpos($n, 'proyektor') !== false || strpos($n, 'projector') !== false) return 'proyektor_epson.jpg';
    if (strpos($n, 'kabel') !== false || strpos($n, 'hdmi') !== false) return 'kabel_hdmi.jpg';
    if (strpos($n, 'mouse') !== false) return 'mouse_wireless.jpg';
    if (strpos($n, 'kamera') !== false || strpos($n, 'camera') !== false) return 'kamera.jpg';
    if (strpos($n, 'toolbox') !== false || strpos($n, 'bor') !== false || strpos($n, 'obeng') !== false || strpos($n, 'peralatan') !== false) return 'toolbox_set.jpg';
    return 'toolbox_set.jpg';
}
?>
<section class="page-public">
    <div class="container">
        <div class="public-page-heading">
            <div><span class="eyebrow">INVENTARIS</span>
                <h1>Data alat Sarana</h1>
                <p>Jelajahi sarana sekolah berdasarkan stok, kondisi, dan lokasi.</p>
            </div><span class="heading-chip"><i class="fas fa-tools mr-1"></i> <?= $q ? mysqli_num_rows($q) : 0 ?> item</span>
        </div>
        <div class="row"><?php while ($r = mysqli_fetch_assoc($q)): ?><div class="col-md-6 col-xl-4 mb-4">
                    <div class="tool-card">
                        <div class="tool-icon"><?php $fotoAlat = (!empty($r['foto']) && $r['foto'] !== 'default.png' && file_exists(__DIR__ . '/../../assets/uploads/sarana/' . $r['foto'])) ? $r['foto'] : fotoBawaanAlat($r['namaalat']); ?><img src="assets/uploads/sarana/<?= htmlspecialchars($fotoAlat) ?>" alt="Foto <?= htmlspecialchars($r['namaalat']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:12px"></div>
                        <div class="tool-content"><span class="tool-category"><?= htmlspecialchars($r['namakategori'] ?? 'Tanpa kategori') ?></span>
                            <h3><?= htmlspecialchars($r['namaalat']) ?></h3>
                            <div class="tool-meta"><span><i class="fas fa-box"></i> <?= $r['jumlah'] ?> tersedia</span><span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($r['lokasi']) ?></span></div>
                            <div class="tool-bottom"><span class="condition-dot"></span><?= htmlspecialchars($r['kondisi']) ?><a href="index.php?halaman=detailalat&id=<?= $r['idalat'] ?>">Detail <i class="fas fa-arrow-right"></i></a></div>
                        </div>
                    </div>
                </div><?php endwhile; ?></div>
    </div>
</section>