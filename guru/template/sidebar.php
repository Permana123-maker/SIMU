<?php
/*
|--------------------------------------------------------------------------
| SIDEBAR TEMPLATE SIMU
|--------------------------------------------------------------------------
| Versi Final
|--------------------------------------------------------------------------
*/

$current = basename($_SERVER['PHP_SELF']);

function menuActive(array $pages)
{
    global $current;

    return in_array($current, $pages) ? "active" : "";
}
?>

<aside class="sidebar">

    <div class="sidebar-header">

        <h5>

            <i class="bi bi-grid-fill"></i>

            MENU GURU

        </h5>

    </div>

    <ul class="sidebar-menu">

        <!-- Dashboard -->

        <li>

            <a href="<?= BASE_URL ?>guru/dashboard.php"
               class="<?= menuActive(['dashboard.php']) ?>">

                <i class="bi bi-speedometer2"></i>

                Dashboard

            </a>

        </li>

        <!-- Tahun Pelajaran -->

        <li>

            <a href="<?= BASE_URL ?>guru/tahun_pelajaran/"
               class="<?= menuActive(['index.php']) ?>">

                <i class="bi bi-calendar-event"></i>

                Tahun Pelajaran

            </a>

        </li>

        <!-- Import Siswa -->

        <li>

            <a href="<?= BASE_URL ?>guru/import_siswa/">

                <i class="bi bi-people-fill"></i>

                Import Siswa

            </a>

        </li>

        <!-- Import Soal -->

        <li>

            <a href="<?= BASE_URL ?>guru/import_soal/">

                <i class="bi bi-upload"></i>

                Import Soal

            </a>

        </li>

        <!-- Paket Soal -->

        <li>

            <a href="<?= BASE_URL ?>guru/paket_soal/">

                <i class="bi bi-journal-text"></i>

                Paket Soal

            </a>

        </li>

        <!-- Sesi Ujian -->

        <li>

            <a href="<?= BASE_URL ?>guru/sesi_ujian/">

                <i class="bi bi-display"></i>

                Sesi Ujian

            </a>

        </li>

        <!-- Hasil -->

        <li>

            <a href="<?= BASE_URL ?>guru/hasil/">

                <i class="bi bi-bar-chart-fill"></i>

                Hasil Ujian

            </a>

        </li>

        <!-- Pengaturan -->

        <li>

            <a href="<?= BASE_URL ?>guru/pengaturan/">

                <i class="bi bi-gear-fill"></i>

                Pengaturan

            </a>

        </li>

    </ul>

    <div class="sidebar-footer">

        <div class="text-center mb-3">

            <small>

                <?= NAMA_APLIKASI ?>

                <br>

                <?= VERSI_APLIKASI ?>

            </small>

        </div>

        <a href="<?= BASE_URL ?>guru/logout.php"
           class="btn btn-danger w-100">

            <i class="bi bi-box-arrow-right"></i>

            Logout

        </a>

    </div>

</aside>