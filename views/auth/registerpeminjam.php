<?php
/*
|--------------------------------------------------------------------------
| REGISTER PEMINJAM
|--------------------------------------------------------------------------
*/
?>

<div class="register-page">

    <div class="register-card">

        <!-- HEADER -->
        <div class="register-header">
            <div class="register-icon">
                <i class="fas fa-user-plus"></i>
            </div>

            <h1>Daftar Akun Peminjam</h1>

            <p>
                Buat akun untuk menggunakan sistem peminjaman sarana.
            </p>
        </div>


        <!-- PESAN ERROR -->
        <?php if (!empty($_SESSION['error_register'])): ?>

            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>

                <?= htmlspecialchars($_SESSION['error_register']); ?>

            </div>

            <?php unset($_SESSION['error_register']); ?>

        <?php endif; ?>


        <!-- FORM -->
        <form
            action="proses/prosesregisterpeminjam.php"
            method="POST"
            autocomplete="off"
        >

            <!-- ==============================
                 DATA PEMINJAM
            =============================== -->

            <div class="form-section">

                <div class="section-title">
                    <span>01</span>
                    Data Peminjam
                </div>


                <!-- NAMA -->
                <div class="form-group">

                    <label for="namapeminjam">
                        Nama Peminjam
                    </label>

                    <input
                        type="text"
                        id="namapeminjam"
                        name="namapeminjam"
                        class="form-control"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                </div>


                <!-- KELAS + NO HP -->
                <div class="form-row">

                    <div class="form-group">

                        <label for="kelas">
                            Kelas
                        </label>

                        <input
                            type="text"
                            id="kelas"
                            name="kelas"
                            class="form-control"
                            placeholder="Contoh: XI RPL 2"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="nohp">
                            No. HP
                        </label>

                        <input
                            type="text"
                            id="nohp"
                            name="nohp"
                            class="form-control"
                            placeholder="Contoh: 081234567890"
                            maxlength="15"
                            required
                        >

                    </div>

                </div>


                <!-- LOKASI -->
                <div class="form-group">

                    <label for="lokasi">
                        Lokasi / Tempat
                    </label>

                    <input
                        type="text"
                        id="lokasi"
                        name="lokasi"
                        class="form-control"
                        placeholder="Contoh: Lab RPL, Ruang Kelas, Perpustakaan"
                        maxlength="100"
                        required
                    >

                    <small class="form-help">
                        Lokasi digunakan sebagai tempat peminjam berada di sekolah.
                    </small>

                </div>

            </div>


            <!-- ==============================
                 AKUN LOGIN
            =============================== -->

            <div class="form-section">

                <div class="section-title">
                    <span>02</span>
                    Akun Login
                </div>


                <!-- USERNAME -->
                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Buat username"
                        maxlength="50"
                        required
                    >

                </div>


                <!-- PASSWORD -->
                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Minimal 6 karakter"
                        minlength="6"
                        required
                    >

                </div>

            </div>


            <!-- ==============================
                 TOMBOL
            =============================== -->

            <div class="form-actions">

                <button
                    type="submit"
                    name="daftar"
                    class="btn-register"
                >
                    <i class="fas fa-user-plus"></i>
                    Daftar Sekarang
                </button>


                <a
                    href="index.php?halaman=loginpeminjam"
                    class="btn-login"
                >
                    Sudah punya akun? Login
                </a>

            </div>

        </form>

    </div>

</div>


<!-- ======================================================
     STYLE REGISTER
====================================================== -->

<style>

.register-page {
    min-height: calc(100vh - 60px);
    padding: 40px 20px;
    background: #f4f7fb;
}

.register-card {
    width: 100%;
    max-width: 760px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 22px;
    padding: 35px;
    box-shadow: 0 15px 45px rgba(15, 23, 42, 0.08);
}

.register-header {
    text-align: center;
    margin-bottom: 35px;
}

.register-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    background: #eaf2ff;
    color: #2563eb;
    font-size: 25px;
}

.register-header h1 {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
    color: #0f172a;
}

.register-header p {
    margin: 0;
    color: #64748b;
    font-size: 15px;
}

.alert {
    padding: 13px 16px;
    margin-bottom: 25px;
    border-radius: 10px;
}

.alert-danger {
    color: #991b1b;
    background: #fee2e2;
    border: 1px solid #fecaca;
}

.form-section {
    margin-bottom: 30px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 22px;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
}

.section-title span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #eff6ff;
    color: #2563eb;
    font-size: 12px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.form-control {
    width: 100%;
    height: 46px;
    padding: 0 14px;
    border: 1px solid #d7dee9;
    border-radius: 11px;
    outline: none;
    background: #ffffff;
    color: #1e293b;
    font-size: 14px;
    box-sizing: border-box;
    transition: .2s ease;
}

.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
}

.form-control::placeholder {
    color: #94a3b8;
}

.form-help {
    display: block;
    margin-top: 7px;
    color: #64748b;
    font-size: 12px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-actions {
    margin-top: 10px;
}

.btn-register {
    width: 100%;
    height: 48px;
    border: 0;
    border-radius: 11px;
    background: #2563eb;
    color: #ffffff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s ease;
}

.btn-register:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.btn-login {
    display: block;
    margin-top: 15px;
    text-align: center;
    color: #2563eb;
    font-size: 14px;
    text-decoration: none;
}

.btn-login:hover {
    text-decoration: underline;
}


/* ======================================================
   RESPONSIVE HP
====================================================== */

@media (max-width: 600px) {

    .register-page {
        padding: 20px 12px;
    }

    .register-card {
        padding: 24px 18px;
        border-radius: 17px;
    }

    .register-header h1 {
        font-size: 23px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .form-control {
        height: 48px;
    }

}

</style>