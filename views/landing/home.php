<?php
$jumlahAlat = totalData($koneksi, 'alat');
$jumlahKategori = totalData($koneksi, 'kategori');
$jumlahPeminjam = totalData($koneksi, 'peminjam');
?>

<section class="landing-hero-v2" id="beranda">
    <div class="landing-noise"></div>
    <div class="landing-grid"></div>
    <div class="landing-beam landing-beam-1"></div>
    <div class="landing-beam landing-beam-2"></div>
    <div class="landing-orb landing-orb-a"></div>
    <div class="landing-orb landing-orb-b"></div>
    <div class="landing-orb landing-orb-c"></div>
    <div class="landing-particles" aria-hidden="true">
        <i style="--x:7%;--y:20%;--d:8s;--delay:-2s"></i>
        <i style="--x:16%;--y:68%;--d:10s;--delay:-5s"></i>
        <i style="--x:29%;--y:32%;--d:7s;--delay:-1s"></i>
        <i style="--x:41%;--y:78%;--d:11s;--delay:-7s"></i>
        <i style="--x:55%;--y:15%;--d:9s;--delay:-3s"></i>
        <i style="--x:66%;--y:58%;--d:12s;--delay:-8s"></i>
        <i style="--x:78%;--y:27%;--d:8s;--delay:-4s"></i>
        <i style="--x:90%;--y:73%;--d:13s;--delay:-6s"></i>
        <i style="--x:84%;--y:11%;--d:9s;--delay:-1s"></i>
        <i style="--x:12%;--y:43%;--d:11s;--delay:-9s"></i>
        <i s
        <i style="--x:94%;--y:40%;--d:10s;--delay:-6s"></i>
    </div>

    <div class="cursor-glow-v2" aria-hidden="true"></div>

    <div class="container landing-hero-inner">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-v2-copy">
                    <div class="hero-v2-badge"><span class="pulse-dot"></span> SISTEM PEMINJAMAN SARANA <b>ONLINE</b></div>
                    <h1>Pinjam alat sekolah.<br><span>Lebih cepat. Lebih rapi.</span></h1>
                    <p class="hero-v2-lead">Temukan sarana yang kamu butuhkan, cek ketersediaannya, lalu ajukan peminjaman tanpa proses yang ribet.</p>

                    <div class="hero-v2-actions">
                        <a class="hero-v2-primary" href="index.php?halaman=daftaralat">
                            <span>Jelajahi Sarana</span><i class="fas fa-arrow-right"></i>
                        </a>
                        <a class="hero-v2-secondary" href="index.php?halaman=registerpeminjam">
                            <i class="fas fa-user-plus"></i><span>Buat Akun</span>
                        </a>
                    </div>

                    <div class="hero-v2-mini-stats">
                        <div><strong><?= $jumlahAlat ?></strong><span>Total Alat</span></div>
                        <div><strong><?= $jumlahKategori ?></strong><span>Kategori</span></div>
                        <div><strong><?= $jumlahPeminjam ?></strong><span>Peminjam</span></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mt-5 mt-lg-0">
                <div class="system-scene" data-tilt-card>
                    <div class="scene-ring scene-ring-1"></div>
                    <div class="scene-ring scene-ring-2"></div>
                    <div class="scene-glass">
                        <div class="scene-scanline"></div>
                        <div class="scene-topbar">
                            <div class="window-dots"><i></i><i></i><i></i></div>
                            <span><i class="fas fa-shield-alt"></i> secure system</span>
                        </div>

                        <div class="scene-header">
                            <div>
                                <small>LIVE OVERVIEW</small>
                                <h3>Ruang Peminjaman</h3>
                                <p><i class="fas fa-wave-square"></i> Aktivitas sistem real-time</p>
                            </div>
                            <span class="online-chip"><i></i> Online</span>
                        </div>

                        <div class="scene-chart">
                            <div class="chart-label">
                                <span><i class="fas fa-chart-line"></i> Aktivitas Sarana</span>
                                <b><i></i> LIVE</b>
                            </div>
                            <div class="chart-bars">
                                <i style="--h:35%"></i><i style="--h:54%"></i><i style="--h:43%"></i><i style="--h:76%"></i><i style="--h:61%"></i><i style="--h:88%"></i><i style="--h:68%"></i><i style="--h:94%"></i><i style="--h:73%"></i><i style="--h:100%"></i>
                            </div>
                            <div class="chart-axis"><span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span></div>
                        </div>

                        <div class="scene-cards">
                            <div class="scene-metric">
                                <span class="scene-icon blue"><i class="fas fa-toolbox"></i></span>
                                <div><small>ALAT TERSEDIA</small><b><?= $jumlahAlat ?></b></div>
                                <i class="metric-arrow fas fa-arrow-up"></i>
                            </div>
                            <div class="scene-metric">
                                <span class="scene-icon cyan"><i class="fas fa-layer-group"></i></span>
                                <div><small>KATEGORI</small><b><?= $jumlahKategori ?></b></div>
                                <i class="metric-arrow fas fa-arrow-up"></i>
                            </div>
                        </div>

                        <div class="scene-status">
                            <span><i class="fas fa-check-circle"></i> Semua sistem normal</span>
                            <b><span class="status-wave"></span> 99.9%</b>
                        </div>
                    </div>

                    <div class="floating-card floating-card-a">
                        <span><i class="fas fa-check"></i></span>
                        <div><b>Pengajuan terkirim</b><small>baru saja</small></div>
                    </div>
                    <div class="floating-card floating-card-b">
                        <span><i class="fas fa-bolt"></i></span>
                        <div><b>Stok diperbarui</b><small>real-time</small></div>
                    </div>
                    <div class="scene-orbit-dot scene-orbit-dot-a"></div>
                    <div class="scene-orbit-dot scene-orbit-dot-b"></div>
                </div>
            </div>
        </div>
    </div>

    <a href="#fitur" class="hero-scroll-cue"><span></span> scroll untuk melihat fitur</a>
</section>

<section class="landing-stats-strip">
    <div class="container">
        <div class="stats-strip-inner">
            <div><span class="strip-icon"><i class="fas fa-box-open"></i></span>
                <div><b><?= $jumlahAlat ?>+</b><small>Sarana terdata</small></div>
            </div>
            <div><span class="strip-icon"><i class="fas fa-layer-group"></i></span>
                <div><b><?= $jumlahKategori ?></b><small>Kategori sarana</small></div>
            </div>
            <div><span class="strip-icon"><i class="fas fa-users"></i></span>
                <div><b><?= $jumlahPeminjam ?></b><small>Akun peminjam</small></div>
            </div>
            <div class="strip-note"><i class="fas fa-shield-alt"></i><span>Pengelolaan lebih teratur untuk kebutuhan sekolah.</span></div>
        </div>
    </div>
</section>

<section class="landing-features-v2" id="fitur">
    <div class="container">
        <div class="section-v2-heading">
            <div><span class="section-v2-label">FITUR UTAMA</span>
                <h2>Satu tempat untuk semua<br><span>urusan peminjaman.</span></h2>
            </div>
            <p>Dirancang supaya peminjam mudah mencari alat, sedangkan admin dan petugas lebih mudah mengelola transaksi.</p>
        </div>

        <div class="feature-v2-grid">
            <a class="feature-v2-card feature-v2-wide" href="index.php?halaman=daftaralat">
                <span class="feature-v2-number">01</span>
                <div class="feature-v2-icon"><i class="fas fa-search"></i></div>
                <h3>Cari sarana dengan cepat</h3>
                <p>Lihat alat, kategori, stok, kondisi, dan lokasi sebelum menentukan sarana yang ingin dipinjam.</p>
                <span class="feature-v2-link">Lihat sarana <i class="fas fa-arrow-right"></i></span>
                <div class="feature-v2-line"></div>
            </a>
            <a class="feature-v2-card" href="index.php?halaman=loginpeminjam">
                <span class="feature-v2-number">02</span>
                <div class="feature-v2-icon cyan"><i class="fas fa-file-signature"></i></div>
                <h3>Ajukan peminjaman</h3>
                <p>Ajukan alat yang dibutuhkan dan pantau status pengajuanmu dari akun peminjam.</p>
                <span class="feature-v2-link">Mulai pinjam <i class="fas fa-arrow-right"></i></span>
            </a>
            <div class="feature-v2-card">
                <span class="feature-v2-number">03</span>
                <div class="feature-v2-icon purple"><i class="fas fa-history"></i></div>
                <h3>Riwayat lebih jelas</h3>
                <p>Semua aktivitas peminjaman tersimpan sehingga lebih mudah dipantau kembali.</p>
                <span class="feature-v2-link">Terorganisir <i class="fas fa-check"></i></span>
            </div>
        </div>
    </div>
</section>

<section class="landing-process-v2">
    <div class="container">
        <div class="process-v2-heading"><span class="section-v2-label">CARA KERJA</span>
            <h2>Dari cari alat sampai selesai,<br><span>semuanya jelas.</span></h2>
        </div>
        <div class="process-v2-grid">
            <div class="process-v2-item"><span>01</span><i class="fas fa-search"></i>
                <h3>Pilih alat</h3>
                <p>Cari sarana yang sesuai kebutuhanmu.</p>
            </div>
            <div class="process-v2-connector"></div>
            <div class="process-v2-item"><span>02</span><i class="fas fa-paper-plane"></i>
                <h3>Ajukan</h3>
                <p>Isi jumlah dan kirim pengajuan peminjaman.</p>
            </div>
            <div class="process-v2-connector"></div>
            <div class="process-v2-item"><span>03</span><i class="fas fa-check-circle"></i>
                <h3>Gunakan</h3>
                <p>Pantau status sampai proses selesai.</p>
            </div>
        </div>
    </div>
</section>

<section class="landing-cta-v2">
    <div class="cta-v2-glow"></div>
    <div class="container">
        <div class="cta-v2-box">
            <div><span class="section-v2-label">SIAP MULAI?</span>
                <h2>Butuh alat untuk kegiatanmu?</h2>
                <p>Masuk ke sistem dan mulai cari sarana yang kamu perlukan.</p>
            </div>
            <div class="cta-v2-actions"><a class="hero-v2-primary" href="index.php?halaman=loginpeminjam"><span>Masuk ke Sistem</span><i class="fas fa-arrow-right"></i></a><a href="index.php?halaman=registerpeminjam">Belum punya akun? Daftar</a></div>
        </div>
    </div>
</section>