<?php

/*
|--------------------------------------------------------------------------
| DASHBOARD GURU
|--------------------------------------------------------------------------
| SIMU - Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../app/keamanan/session.php';
require_once __DIR__ . '/../app/database/database.php';

$page_title = "Dashboard";

require_once __DIR__ . '/template/header.php';

/*
|--------------------------------------------------------------------------
| DATA GURU LOGIN
|--------------------------------------------------------------------------
*/

$nama_guru = $_SESSION['guru']['nama'] ?? 'Guru';

/*
|--------------------------------------------------------------------------
| STATISTIK DASHBOARD
|--------------------------------------------------------------------------
*/

function hitungData(PDO $pdo, string $tabel, string $where = "")
{
    $sql = "SELECT COUNT(*) FROM {$tabel}";

    if ($where != "") {
        $sql .= " WHERE {$where}";
    }

    return (int) $pdo->query($sql)->fetchColumn();
}

try {

    $tahun_aktif = hitungData(
        $pdo,
        "tahun_ajaran",
        "status='Aktif' AND dihapus_pada IS NULL"
    );

    $jumlah_kelas = hitungData(
        $pdo,
        "kelas",
        "dihapus_pada IS NULL"
    );

    $jumlah_siswa = hitungData(
        $pdo,
        "siswa",
        "dihapus_pada IS NULL"
    );

    $jumlah_paket = hitungData(
        $pdo,
        "paket_soal",
        "dihapus_pada IS NULL"
    );

    $jumlah_soal = hitungData(
        $pdo,
        "soal",
        "dihapus_pada IS NULL"
    );

    $jumlah_ujian = hitungData(
        $pdo,
        "ujian",
        "dihapus_pada IS NULL"
    );

    $ujian_aktif = hitungData(
        $pdo,
        "ujian",
        "status='Aktif' AND dihapus_pada IS NULL"
    );

} catch (Exception $e) {

    $tahun_aktif = 0;
    $jumlah_kelas = 0;
    $jumlah_siswa = 0;
    $jumlah_paket = 0;
    $jumlah_soal = 0;
    $jumlah_ujian = 0;
    $ujian_aktif = 0;

}

?>

<div class="container-fluid">

    <!-- ====================================================== -->
    <!-- HEADER DASHBOARD -->
    <!-- ====================================================== -->

    <div class="row mb-4">

        <div class="col-lg-8">

            <h2 class="fw-bold mb-1">

                <i class="bi bi-speedometer2"></i>

                Dashboard Guru

            </h2>

            <p class="text-muted mb-0">

                Selamat datang kembali,

                <strong><?= htmlspecialchars($nama_guru) ?></strong>

            </p>

        </div>

        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">

            <span class="badge bg-primary fs-6">

                <?= date('d F Y'); ?>

            </span>

            <br>

            <small class="text-muted">

                <?= date('H:i:s'); ?> WIB

            </small>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- INFORMASI SINGKAT -->
    <!-- ====================================================== -->

    <div class="alert alert-primary border-0 shadow-sm">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <h5 class="mb-1">

                    <?= NAMA_APLIKASI ?>

                </h5>

                <small>

                    Sistem Informasi Manajemen Ujian Matematika SMP

                </small>

            </div>

            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">

                <span class="badge bg-success">

                    Versi <?= VERSI_APLIKASI ?>

                </span>

            </div>

        </div>

    </div>

   <!-- ====================================================== -->
<!-- KARTU STATISTIK -->
<!-- ====================================================== -->

<div class="row">

    <!-- Tahun Ajaran -->

    <div class="col-xl-4 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted d-block">
                            Tahun Ajaran Aktif
                        </small>

                        <h2 class="fw-bold text-primary mb-0">

                            <?= $tahun_aktif ?>

                        </h2>

                    </div>

                    <i class="bi bi-calendar-event fs-1 text-primary"></i>

                </div>

            </div>

        </div>

    </div>

    <!-- Kelas -->

    <div class="col-xl-4 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted d-block">
                            Jumlah Kelas
                        </small>

                        <h2 class="fw-bold text-success mb-0">

                            <?= number_format($jumlah_kelas) ?>

                        </h2>

                    </div>

                    <i class="bi bi-building fs-1 text-success"></i>

                </div>

            </div>

        </div>

    </div>

    <!-- Siswa -->

    <div class="col-xl-4 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted d-block">
                            Jumlah Siswa
                        </small>

                        <h2 class="fw-bold text-info mb-0">

                            <?= number_format($jumlah_siswa) ?>

                        </h2>

                    </div>

                    <i class="bi bi-people-fill fs-1 text-info"></i>

                </div>

            </div>

        </div>

    </div>

    <!-- Paket Soal -->

    <div class="col-xl-4 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted d-block">
                            Paket Soal
                        </small>

                        <h2 class="fw-bold text-warning mb-0">

                            <?= number_format($jumlah_paket) ?>

                        </h2>

                    </div>

                    <i class="bi bi-collection-fill fs-1 text-warning"></i>

                </div>

            </div>

        </div>

    </div>

    <!-- Jumlah Ujian -->

    <div class="col-xl-4 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted d-block">
                            Jumlah Ujian
                        </small>

                        <h2 class="fw-bold text-secondary mb-0">

                            <?= number_format($jumlah_ujian) ?>

                        </h2>

                    </div>

                    <i class="bi bi-clipboard-check fs-1 text-secondary"></i>

                </div>

            </div>

        </div>

    </div>

    <!-- Ujian Aktif -->

    <div class="col-xl-4 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted d-block">
                            Ujian Aktif
                        </small>

                        <h2 class="fw-bold text-danger mb-0">

                            <?= number_format($ujian_aktif) ?>

                        </h2>

                    </div>

                    <i class="bi bi-play-circle-fill fs-1 text-danger"></i>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- MENU CEPAT & INFORMASI SISTEM -->
<!-- ====================================================== -->

<div class="row">

    <!-- MENU CEPAT -->

    <div class="col-lg-8 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">

                    <i class="bi bi-lightning-charge-fill"></i>

                    Menu Cepat

                </h5>

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <a href="<?= BASE_URL ?>guru/tahun_ajaran/"

                            class="btn btn-outline-primary w-100 py-3">

                            <i class="bi bi-calendar-event fs-2 d-block mb-2"></i>

                            Tahun Ajaran

                        </a>

                    </div>

                    <div class="col-md-4">

                        <a href="<?= BASE_URL ?>guru/kelas/"

                            class="btn btn-outline-success w-100 py-3">

                            <i class="bi bi-building fs-2 d-block mb-2"></i>

                            Kelas

                        </a>

                    </div>

                    <div class="col-md-4">

                        <a href="<?= BASE_URL ?>guru/siswa/"

                            class="btn btn-outline-info w-100 py-3">

                            <i class="bi bi-people-fill fs-2 d-block mb-2"></i>

                            Siswa

                        </a>

                    </div>

                    <div class="col-md-6">

                        <a href="<?= BASE_URL ?>guru/paket_soal/"

                            class="btn btn-outline-warning w-100 py-3">

                            <i class="bi bi-collection-fill fs-2 d-block mb-2"></i>

                            Paket Soal

                        </a>

                    </div>

                    <div class="col-md-6">

                        <a href="<?= BASE_URL ?>guru/ujian/"

                            class="btn btn-outline-danger w-100 py-3">

                            <i class="bi bi-clipboard-check fs-2 d-block mb-2"></i>

                            Ujian

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- INFORMASI SISTEM -->

    <div class="col-lg-4 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="bi bi-info-circle-fill"></i>

                    Informasi Sistem

                </h5>

            </div>

            <div class="card-body">

                <table class="table table-borderless table-sm align-middle mb-0">

                    <tr>

                        <td width="45%">Aplikasi</td>

                        <td><strong><?= NAMA_APLIKASI ?></strong></td>

                    </tr>

                    <tr>

                        <td>Versi</td>

                        <td><?= VERSI_APLIKASI ?></td>

                    </tr>

                    <tr>

                        <td>Login</td>

                        <td><?= htmlspecialchars($nama_guru) ?></td>

                    </tr>

                    <tr>

                        <td>Status</td>

                        <td>

                            <span class="badge bg-success">

                                Aktif

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td>Tanggal</td>

                        <td><?= date('d-m-Y') ?></td>

                    </tr>

                    <tr>

                        <td>Jam Server</td>

                        <td><?= date('H:i:s') ?> WIB</td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- ALUR PENGGUNAAN SIMU -->
<!-- ====================================================== -->

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-dark text-white">

        <h5 class="mb-0">

            <i class="bi bi-diagram-3-fill"></i>

            Alur Penggunaan SIMU

        </h5>

    </div>

    <div class="card-body">

        <div class="row text-center">

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-primary mb-2">

                        <i class="bi bi-calendar-event"></i>

                    </div>

                    <strong>1.</strong><br>

                    Aktifkan<br>

                    Tahun Ajaran

                </div>

            </div>

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-success mb-2">

                        <i class="bi bi-building"></i>

                    </div>

                    <strong>2.</strong><br>

                    Kelola<br>

                    Data Kelas

                </div>

            </div>

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-info mb-2">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <strong>3.</strong><br>

                    Import<br>

                    Data Siswa

                </div>

            </div>

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-warning mb-2">

                        <i class="bi bi-collection-fill"></i>

                    </div>

                    <strong>4.</strong><br>

                    Buat<br>

                    Paket Soal

                </div>

            </div>

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-danger mb-2">

                        <i class="bi bi-upload"></i>

                    </div>

                    <strong>5.</strong><br>

                    Import<br>

                    Soal Excel

                </div>

            </div>

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-secondary mb-2">

                        <i class="bi bi-clipboard-check"></i>

                    </div>

                    <strong>6.</strong><br>

                    Buat<br>

                    Ujian

                </div>

            </div>

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-success mb-2">

                        <i class="bi bi-play-circle-fill"></i>

                    </div>

                    <strong>7.</strong><br>

                    Aktifkan<br>

                    Ujian

                </div>

            </div>

            <div class="col-lg col-md-6 mb-3">

                <div class="p-3 border rounded h-100">

                    <div class="fs-2 text-primary mb-2">

                        <i class="bi bi-bar-chart-fill"></i>

                    </div>

                    <strong>8.</strong><br>

                    Lihat<br>

                    Hasil Ujian

                </div>

            </div>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- FOOTER DASHBOARD -->
<!-- ====================================================== -->

<div class="card border-0 shadow-sm">

    <div class="card-body text-center">

        <p class="mb-1">

            <strong><?= NAMA_APLIKASI ?></strong>

            <span class="text-muted">

                Versi <?= VERSI_APLIKASI ?>

            </span>

        </p>

        <small class="text-muted">

            Sistem Informasi Manajemen Ujian Matematika SMP

            <br>

            © <?= date('Y') ?> - Seluruh hak cipta dilindungi.

        </small>

    </div>

</div>

</div>
<!-- End Container Fluid -->

<?php require_once __DIR__ . '/template/footer.php'; ?>