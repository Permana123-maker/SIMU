<?php

/*
|--------------------------------------------------------------------------
| SIMU - Login Guru
|--------------------------------------------------------------------------
| Sistem Informasi Manajemen Ujian Matematika SMP
| Versi : v0.4 Alpha
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../app/konfigurasi/konfigurasi.php';

$page_title = "Login Guru";

require_once __DIR__ . '/template/login_header.php';

?>

<div class="login-box">

    <div class="card shadow-lg border-0">

        <div class="card-body p-5">

            <div class="text-center mb-4">

                <h2 class="fw-bold mb-2">
                    CBT Matematika SMP
                </h2>

                <p class="text-muted mb-0">
                    Sistem Informasi Manajemen Ujian
                </p>

            </div>

            <?php if (isset($_GET['pesan']) && $_GET['pesan'] == "gagal") : ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-circle"></i>

                    Username atau Password salah.

                </div>

            <?php endif; ?>

            <form action="proses/login_proses.php" method="POST" autocomplete="off">

                <div class="mb-3">

                    <label class="form-label">

                        Username

                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Masukkan Username"
                        required
                        autofocus>

                </div>

                <div class="mb-4">

                    <label class="form-label">

                        Password

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan Password"
                        required>

                </div>

                <div class="d-grid">

                    <button type="submit" class="btn btn-primary btn-lg">

                        <i class="bi bi-box-arrow-in-right"></i>

                        MASUK

                    </button>

                </div>

            </form>

            <hr>

            <div class="text-center text-secondary">

                <small>

                    <?= NAMA_APLIKASI ?>

                    <br>

                    <?= VERSI_APLIKASI ?>

                </small>

            </div>

        </div>

    </div>

</div>

<?php

require_once __DIR__ . '/template/login_footer.php';

?>