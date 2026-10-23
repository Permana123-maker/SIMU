<?php
/*
|--------------------------------------------------------------------------
| SIMU - SIDEBAR SISWA
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$namaSiswa = $_SESSION['siswa']['nama_siswa'] ?? 'Siswa';
$nis       = $_SESSION['siswa']['nis'] ?? '-';
$kelas     = $_SESSION['siswa']['nama_kelas'] ?? '-';
?>

<style>
    .siswa-sidebar {
        width: 250px;
        min-height: 100vh;
        background: #0d6efd;
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        box-shadow: 2px 0 8px rgba(0,0,0,.15);
    }

    .siswa-sidebar-header {
        padding: 20px 15px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,.2);
    }

    .siswa-sidebar-header .logo {
        width: 55px;
        height: 55px;
        margin: 0 auto 10px;
        border-radius: 50%;
        background: #fff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: bold;
    }

    .siswa-sidebar-header h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
    }

    .siswa-sidebar-header small {
        display: block;
        margin-top: 4px;
        opacity: .85;
    }

    .siswa-profile {
        padding: 15px;
        border-bottom: 1px solid rgba(255,255,255,.2);
    }

    .siswa-profile strong {
        display: block;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .siswa-profile small {
        display: block;
        opacity: .85;
        margin-top: 2px;
    }

    .siswa-menu {
        padding: 15px 10px;
    }

    .siswa-menu-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        opacity: .7;
        padding: 8px 12px;
    }

    .siswa-menu a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        text-decoration: none;
        padding: 11px 12px;
        margin-bottom: 4px;
        border-radius: 7px;
        font-size: 14px;
        transition: .2s;
    }

    .siswa-menu a:hover,
    .siswa-menu a.active {
        background: rgba(255,255,255,.18);
    }

    .siswa-menu .icon {
        width: 22px;
        text-align: center;
        font-size: 17px;
    }

    .siswa-logout {
        position: absolute;
        left: 10px;
        right: 10px;
        bottom: 15px;
    }

    .siswa-logout a {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border-radius: 7px;
        color: #fff;
        text-decoration: none;
        background: rgba(0,0,0,.15);
        font-size: 14px;
    }

    .siswa-logout a:hover {
        background: rgba(0,0,0,.25);
    }

    /* Area konten agar tidak tertutup sidebar */
    .siswa-content {
        margin-left: 250px;
        min-height: 100vh;
    }

    /* Mobile */
    @media (max-width: 768px) {

        .siswa-sidebar {
            width: 220px;
        }

        .siswa-content {
            margin-left: 220px;
        }
    }

    @media (max-width: 576px) {

        .siswa-sidebar {
            width: 100%;
            min-height: auto;
            height: auto;
            position: relative;
        }

        .siswa-logout {
            position: static;
            margin: 10px;
        }

        .siswa-content {
            margin-left: 0;
        }
    }
</style>

<aside class="siswa-sidebar">

    <!-- HEADER -->
    <div class="siswa-sidebar-header">

        <div class="logo">
            S
        </div>

        <h5>SIMU</h5>

        <small>
            Ujian Matematika SMP
        </small>

    </div>


    <!-- PROFIL SISWA -->
    <div class="siswa-profile">

        <strong>
            <?= htmlspecialchars($namaSiswa); ?>
        </strong>

        <small>
            NIS: <?= htmlspecialchars($nis); ?>
        </small>

        <small>
            Kelas: <?= htmlspecialchars($kelas); ?>
        </small>

    </div>


    <!-- MENU -->
    <nav class="siswa-menu">

        <div class="siswa-menu-title">
            Menu Utama
        </div>

        <a href="../dashboard/index.php" class="active">
            <span class="icon">⌂</span>
            <span>Dashboard</span>
        </a>

        <a href="../ujian/index.php">
            <span class="icon">📝</span>
            <span>Ujian</span>
        </a>

        <a href="../hasil/index.php">
            <span class="icon">📊</span>
            <span>Hasil Ujian</span>
        </a>

        <div class="siswa-menu-title">
            Akun
        </div>

        <a href="../profil/index.php">
            <span class="icon">👤</span>
            <span>Profil Saya</span>
        </a>

    </nav>


    <!-- LOGOUT -->
    <div class="siswa-logout">

        <a href="../logout.php"
           onclick="return confirm('Apakah Anda yakin ingin keluar?');">

            <span>↪</span>
            <span>Keluar</span>

        </a>

    </div>

</aside>