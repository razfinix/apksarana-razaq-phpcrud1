<?php

require_once 'proses/koneksi.php';
require_once 'proses/helper.php';
require_once 'proses/session.php';


/* =========================================================
   HALAMAN YANG DIPANGGIL
========================================================= */

$halaman = $_GET['halaman'] ?? 'home';


/* =========================================================
   HALAMAN AUTH
========================================================= */

$authPages = [
    'loginuser',
    'loginpeminjam',
    'registerpeminjam'
];


/* =========================================================
   HALAMAN PUBLIC / LANDING
========================================================= */

$landingPages = [
    'home',
    'daftaralat',
    'detailalat',
    'daftarkategori',
    'detailkategori',
    'tentang',
    'kontak',
    'daftarisi'
];


/* =========================================================
   HEADER
========================================================= */

include 'views/component/header.php';


/* =========================================================
   STATUS PUBLIC
========================================================= */

$roleLogin = $_SESSION['role'] ?? '';
$isPeminjam = isset($_SESSION['iduser']) && $roleLogin === 'peminjam';
$isAdminPetugas = isset($_SESSION['iduser']) && in_array($roleLogin, ['admin', 'petugas'], true);

$isPublic = !$isPeminjam && !$isAdminPetugas && (
    in_array($halaman, $landingPages, true)
    || in_array($halaman, $authPages, true)
    || $halaman === 'logout'
);


/* =========================================================
   BODY
========================================================= */

?>

<body class="<?php
                if ($isPeminjam) {
                    echo 'hold-transition sidebar-mini layout-fixed peminjam-layout';
                } elseif ($isAdminPetugas) {
                    echo 'hold-transition sidebar-mini layout-fixed';
                } else {
                    echo 'hold-transition layout-top-nav';
                }
                ?>">


    <?php

    /* =========================================================
   LOGOUT
========================================================= */

    if ($halaman === 'logout') {

        include 'views/auth/logout.php';


        /* =========================================================
   ADMIN & PETUGAS
========================================================= */
    } elseif (
        isset($_SESSION['iduser'])
        &&
        in_array(
            $_SESSION['role'] ?? '',
            ['admin', 'petugas'],
            true
        )
    ) {

        /*
     * NAVBAR ADMIN / PETUGAS
     */
        include 'views/component/user/navbar.php';


        /*
     * SIDEBAR SESUAI ROLE
     */
        if (($_SESSION['role'] ?? '') === 'admin') {

            include 'views/component/user/sidebaradmin.php';
        } else {

            include 'views/component/user/sidebarpetugas.php';
        }


        /*
     * KONTEN ADMIN / PETUGAS
     */
        echo '<div class="content-wrapper">';
        echo '<section class="content">';
        echo '<div class="container-fluid">';


        switch ($halaman) {

            /* =========================
           DASHBOARD
        ========================= */

            case 'dashboardadmin':

                cekAdmin();

                include 'views/user/dashboard/dashboardadmin.php';

                break;


            case 'dashboardpetugas':

                cekPetugas();

                include 'views/user/dashboard/dashboardpetugas.php';

                break;


            case 'profilakun':

                cekLoginUser();

                include 'views/component/profilakun.php';

                break;


            /* =========================
           USER
        ========================= */

            case 'user':
            case 'createuser':
            case 'edituser':
            case 'showuser':

                cekAdmin();

                $fileUser = [
                    'user'       => 'index',
                    'createuser' => 'create',
                    'edituser'   => 'edit',
                    'showuser'   => 'show'
                ];

                include 'views/user/user/'
                    . $fileUser[$halaman]
                    . '.php';

                break;


            /* =========================
           PEMINJAM
        ========================= */

            case 'peminjam':
            case 'createpeminjam':
            case 'editpeminjam':
            case 'showpeminjam':

                cekAdminPetugas();

                $filePeminjam = [
                    'peminjam'       => 'index',
                    'createpeminjam' => 'create',
                    'editpeminjam'   => 'edit',
                    'showpeminjam'   => 'show'
                ];

                include 'views/user/peminjam/'
                    . $filePeminjam[$halaman]
                    . '.php';

                break;


            /* =========================
           LOKASI PEMINJAMAN
        ========================= */

            case 'asal':
            case 'createasal':
            case 'editasal':
            case 'showasal':

                cekAdminPetugas();

                $fileAsal = [
                    'asal'       => 'index',
                    'createasal' => 'create',
                    'editasal'   => 'edit',
                    'showasal'   => 'show'
                ];

                include 'views/user/asal/'
                    . $fileAsal[$halaman]
                    . '.php';

                break;


            /* =========================
           KATEGORI
        ========================= */

            case 'kategori':
            case 'createkategori':
            case 'editkategori':
            case 'showkategori':

                cekAdminPetugas();

                $fileKategori = [
                    'kategori'       => 'index',
                    'createkategori' => 'create',
                    'editkategori'   => 'edit',
                    'showkategori'   => 'show'
                ];

                include 'views/user/kategori/'
                    . $fileKategori[$halaman]
                    . '.php';

                break;


            /* =========================
           ALAT / SARANA
        ========================= */

            case 'alat':
            case 'createalat':
            case 'editalat':
            case 'showalat':

                cekAdminPetugas();

                $fileAlat = [
                    'alat'       => 'index',
                    'createalat' => 'create',
                    'editalat'   => 'edit',
                    'showalat'   => 'show'
                ];

                include 'views/user/alat/'
                    . $fileAlat[$halaman]
                    . '.php';

                break;


            /* =========================
           PEMINJAMAN
        ========================= */

            case 'peminjaman':
            case 'createpeminjaman':
            case 'showpeminjaman':

                cekAdminPetugas();

                $filePeminjaman = [
                    'peminjaman'       => 'index',
                    'createpeminjaman' => 'create',
                    'showpeminjaman'   => 'show'
                ];

                include 'views/user/peminjaman/'
                    . $filePeminjaman[$halaman]
                    . '.php';

                break;


            /* =========================
           PENGEMBALIAN
        ========================= */

            case 'pengembalian':
            case 'riwayatpengembalian':
            case 'createpengembalian':
            case 'showpengembalian':

                cekAdminPetugas();

                $filePengembalian = [
                    'pengembalian'       => 'index',
                    'riwayatpengembalian' => 'riwayat',
                    'createpengembalian' => 'create',
                    'showpengembalian'   => 'show'
                ];

                include 'views/user/pengembalian/'
                    . $filePengembalian[$halaman]
                    . '.php';

                break;


            /* =========================
           LAPORAN
        ========================= */

            case 'laporanharian':
            case 'laporanbulanan':
            case 'laporantahunan':

                cekAdminPetugas();

                include 'views/user/laporan/'
                    . $halaman
                    . '.php';

                break;


            /* =========================
           ERROR
        ========================= */

            default:

                include 'views/errors/404.php';

                break;
        }


        echo '</div>';
        echo '</section>';
        echo '</div>';


        /*
     * FOOTER
     */
        include 'views/component/user/footer.php';


        /* =========================================================
   PEMINJAM
========================================================= */
    } elseif (
        isset($_SESSION['iduser'])
        &&
        ($_SESSION['role'] ?? '') === 'peminjam'
    ) {

        /*
     * WRAPPER UTAMA
     */
        echo '<div class="wrapper">';


        /*
     * NAVBAR PEMINJAM
     */
        include 'views/component/peminjam/navbar.php';


        /*
     * SIDEBAR PEMINJAM
     */
        include 'views/component/peminjam/sidebar.php';


        /*
     * KONTEN UTAMA
     */
        echo '<div class="content-wrapper">';

        echo '<section class="content">';

        echo '<div class="container-fluid">';


        switch ($halaman) {

            /* =========================
           DASHBOARD PEMINJAM
        ========================= */

            case 'dashboardpeminjam':

                cekLoginPeminjam();

                include 'views/peminjam/dashboardpeminjam.php';

                break;


            /* =========================
           DAFTAR SARANA
        ========================= */

            case 'daftaralat':

                include 'views/landing/daftaralat.php';

                break;


            /* =========================
           DETAIL SARANA
        ========================= */

            case 'detailalat':

                include 'views/landing/detailalat.php';

                break;


            /* =========================
           KATEGORI
        ========================= */

            case 'daftarkategori':

                include 'views/landing/daftarkategori.php';

                break;


            /* =========================
           DETAIL KATEGORI
        ========================= */

            case 'detailkategori':

                include 'views/landing/detailkategori.php';

                break;


            /* =========================
           PROFIL
        ========================= */

            case 'profil':

                cekLoginPeminjam();

                include 'views/peminjam/profil.php';

                break;


            /* =========================
           RIWAYAT PEMINJAMAN
        ========================= */

            case 'ajukanpeminjaman':

                cekLoginPeminjam();

                include 'views/peminjam/ajukanpeminjaman.php';

                break;


            case 'riwayatpeminjaman':

                cekLoginPeminjam();

                include 'views/peminjam/riwayatpeminjaman.php';

                break;


            /* =========================
           ERROR
        ========================= */

            default:

                include 'views/errors/404.php';

                break;
        }


        echo '</div>';

        echo '</section>';

        echo '</div>';


        /*
     * FOOTER PEMINJAM
     */
        include 'views/component/peminjam/footer.php';


        /*
     * TUTUP WRAPPER
     */
        echo '</div>';


        /* =========================================================
   TAMU / PUBLIC
========================================================= */
    } elseif (
        in_array($halaman, $landingPages, true)
        ||
        in_array($halaman, $authPages, true)
    ) {

        echo '<div class="wrapper">';


        /*
     * NAVBAR TAMU
     */
        include 'views/component/tamu/navbar.php';


        /*
     * HALAMAN
     */
        if (in_array($halaman, $authPages, true)) {

            include 'views/auth/' . $halaman . '.php';
        } else {

            include 'views/landing/' . $halaman . '.php';
        }


        /*
     * FOOTER TAMU
     */
        include 'views/component/tamu/footer.php';

        echo '</div>';


        /* =========================================================
   ERROR GLOBAL
========================================================= */
    } else {

        include 'views/component/tamu/navbar.php';

        include 'views/errors/404.php';

        include 'views/component/tamu/footer.php';
    }


    /* =========================================================
   JAVASCRIPT
========================================================= */

    include 'views/component/footerjs.php';

    ?>

</body>

</html>