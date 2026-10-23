<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Dashboard Siswa
|--------------------------------------------------------------------------
*/

session_start();

require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['siswa'])) {

    header("Location: ../login/login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| TAHUN PELAJARAN AKTIF
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    die("Belum ada Tahun Pelajaran aktif.");

}

/*
|--------------------------------------------------------------------------
| DATA SESSION
|--------------------------------------------------------------------------
*/

$id_siswa    = $_SESSION['siswa']['id_siswa'];
$id_kelas    = $_SESSION['siswa']['id_kelas'];
$nama_siswa  = $_SESSION['siswa']['nama_siswa'];
$nis         = $_SESSION['siswa']['nis'];
$nama_kelas  = $_SESSION['siswa']['nama_kelas'];


/*
|--------------------------------------------------------------------------
| HITUNG UJIAN AKTIF
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM ujian
    WHERE
        id_tahun = ?
        AND id_kelas = ?
        AND status = 'Aktif'
        AND dihapus_pada IS NULL
");

$query->execute([
    $tahun['id_tahun'],
    $id_kelas
]);

$totalUjian = (int) $query->fetch(PDO::FETCH_ASSOC)['total'];


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

require_once "../template/header.php";

?>

<!-- SIDEBAR -->

<?php require_once "../template/sidebar.php"; ?>


<!-- CONTENT -->

<div class="siswa-content">

    <!-- TOPBAR -->

    <?php
    $topbar = "../template/topbar.php";

    if (file_exists($topbar)) {
        require_once $topbar;
    }
    ?>


    <!-- MAIN CONTENT -->

    <div class="container-fluid py-4">

        <!-- JUDUL -->

        <div class="mb-4">

            <h4 class="fw-bold mb-1">
                Dashboard Siswa
            </h4>

            <p class="text-muted mb-0">
                Selamat datang di Sistem Informasi Manajemen Ujian Matematika.
            </p>

        </div>


        <!-- INFORMASI SISWA -->

        <div class="row g-4 mb-4">

            <div class="col-lg-8">

                <div class="card shadow-sm border-0 h-100">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">
                            👋 Selamat Datang
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-borderless mb-0">

                                <tr>

                                    <th width="180">
                                        Nama Siswa
                                    </th>

                                    <td width="20">
                                        :
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($nama_siswa); ?>
                                        </strong>
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        NIS
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($nis); ?>
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Kelas
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($nama_kelas); ?>
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Tahun Pelajaran
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($tahun['nama_tahun']); ?>
                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            <!-- JUMLAH UJIAN -->

            <div class="col-lg-4">

                <div class="card shadow-sm border-0 h-100">

                    <div class="card-body text-center d-flex flex-column justify-content-center">

                        <div class="mb-2">

                            <span
                                style="
                                    display:inline-flex;
                                    align-items:center;
                                    justify-content:center;
                                    width:65px;
                                    height:65px;
                                    border-radius:50%;
                                    background:#e7f1ff;
                                    font-size:30px;
                                "
                            >
                                📝
                            </span>

                        </div>

                        <h6 class="text-muted">
                            Ujian Aktif
                        </h6>

                        <h1 class="display-3 text-primary fw-bold mb-0">
                            <?= $totalUjian; ?>
                        </h1>

                        <p class="text-muted mb-0">
                            Ujian siap dikerjakan
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- MENU UTAMA -->

        <div class="card shadow-sm border-0">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">
                    🚀 Menu Utama
                </h5>

            </div>

            <div class="card-body">

                <div class="row g-4">


                    <!-- UJIAN -->

                    <div class="col-md-4">

                        <div class="card border-primary h-100">

                            <div class="card-body text-center">

                                <div style="font-size:45px;">
                                    📝
                                </div>

                                <h5 class="mt-3">
                                    Daftar Ujian
                                </h5>

                                <p class="text-muted">
                                    Lihat ujian yang tersedia untuk kelas Anda.
                                </p>

                                <a
                                    href="../ujian/index.php"
                                    class="btn btn-primary"
                                >
                                    Lihat Ujian
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- PROFIL -->

                    <div class="col-md-4">

                        <div class="card border-warning h-100">

                            <div class="card-body text-center">

                                <div style="font-size:45px;">
                                    👤
                                </div>

                                <h5 class="mt-3">
                                    Profil Saya
                                </h5>

                                <p class="text-muted">
                                    Lihat informasi akun dan data diri Anda.
                                </p>

                                <button
                                    class="btn btn-warning"
                                    disabled
                                >
                                    Segera Hadir
                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- LOGOUT -->

                    <div class="col-md-4">

                        <div class="card border-danger h-100">

                            <div class="card-body text-center">

                                <div style="font-size:45px;">
                                    🚪
                                </div>

                                <h5 class="mt-3">
                                    Keluar
                                </h5>

                                <p class="text-muted">
                                    Keluar dari sistem SIMU dengan aman.
                                </p>

                                <a
                                    href="../login/logout.php"
                                    class="btn btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin keluar?')"
                                >
                                    Keluar
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- INFORMASI -->

        <div class="alert alert-info mt-4 shadow-sm">

            <strong>Informasi:</strong>

            Pastikan Anda mengerjakan ujian dengan jujur dan mengikuti
            seluruh aturan yang ditetapkan oleh sekolah.

        </div>

    </div>

</div>


<?php require_once "../template/footer.php"; ?>

</body>
</html>