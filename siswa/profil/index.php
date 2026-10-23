<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Profil Siswa
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
| DATA SESSION
|--------------------------------------------------------------------------
*/

$id_siswa   = $_SESSION['siswa']['id_siswa'];
$id_kelas   = $_SESSION['siswa']['id_kelas'];
$nis        = $_SESSION['siswa']['nis'];
$nama_siswa = $_SESSION['siswa']['nama_siswa'];
$nama_kelas = $_SESSION['siswa']['nama_kelas'];


/*
|--------------------------------------------------------------------------
| TAHUN PELAJARAN
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    die("Belum ada Tahun Pelajaran aktif.");

}


/*
|--------------------------------------------------------------------------
| AMBIL DATA SISWA TERBARU
|--------------------------------------------------------------------------
|
| Data diambil kembali dari database agar informasi profil
| selalu sesuai dengan data terbaru.
|
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        s.id_siswa,
        s.id_kelas,
        s.id_tahun,
        s.nis,
        s.nomor_peserta,
        s.nama,
        s.jk,
        s.status,
        k.nama_kelas

    FROM siswa s

    INNER JOIN kelas k
        ON s.id_kelas = k.id_kelas

    WHERE
        s.id_siswa = ?
        AND s.dihapus_pada IS NULL

    LIMIT 1
");

$query->execute([
    $id_siswa
]);

$siswa = $query->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| DATA TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if (!$siswa) {

    session_destroy();

    header("Location: ../login/login.php");
    exit;

}


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

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h4 class="fw-bold mb-1">
                    Profil Saya
                </h4>

                <p class="text-muted mb-0">
                    Informasi data diri dan akun siswa.
                </p>

            </div>


            <a
                href="../dashboard/index.php"
                class="btn btn-secondary"
            >
                ← Dashboard
            </a>

        </div>


        <div class="row g-4">


            <!-- PROFIL UTAMA -->

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm text-center h-100">

                    <div class="card-body py-5">


                        <!-- FOTO / ICON -->

                        <div
                            style="
                                width:100px;
                                height:100px;
                                border-radius:50%;
                                background:#e7f1ff;
                                color:#0d6efd;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                margin:0 auto 20px;
                                font-size:45px;
                            "
                        >
                            👤
                        </div>


                        <!-- NAMA -->

                        <h4 class="fw-bold mb-1">

                            <?= htmlspecialchars(
                                $siswa['nama']
                            ); ?>

                        </h4>


                        <!-- NIS -->

                        <p class="text-muted mb-3">

                            NIS:
                            <?= htmlspecialchars(
                                $siswa['nis']
                            ); ?>

                        </p>


                        <!-- KELAS -->

                        <span class="badge bg-primary px-3 py-2">

                            Kelas
                            <?= htmlspecialchars(
                                $siswa['nama_kelas']
                            ); ?>

                        </span>


                        <!-- STATUS -->

                        <div class="mt-4">

                            <?php if ($siswa['status'] === 'Aktif'): ?>

                                <span class="badge bg-success">

                                    ● Akun Aktif

                                </span>

                            <?php else: ?>

                                <span class="badge bg-danger">

                                    ● Tidak Aktif

                                </span>

                            <?php endif; ?>

                        </div>


                    </div>

                </div>

            </div>


            <!-- DATA SISWA -->

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm h-100">


                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">
                            📋 Data Siswa
                        </h5>

                    </div>


                    <div class="card-body">


                        <div class="table-responsive">

                            <table class="table table-borderless align-middle mb-0">


                                <!-- NAMA -->

                                <tr>

                                    <th width="200">
                                        Nama Lengkap
                                    </th>

                                    <td width="20">
                                        :
                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $siswa['nama']
                                        ); ?>

                                    </td>

                                </tr>


                                <!-- NIS -->

                                <tr>

                                    <th>
                                        NIS
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $siswa['nis']
                                        ); ?>

                                    </td>

                                </tr>


                                <!-- NOMOR PESERTA -->

                                <tr>

                                    <th>
                                        Nomor Peserta
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>

                                        <?= !empty($siswa['nomor_peserta'])
                                            ? htmlspecialchars(
                                                $siswa['nomor_peserta']
                                            )
                                            : '-';
                                        ?>

                                    </td>

                                </tr>


                                <!-- JENIS KELAMIN -->

                                <tr>

                                    <th>
                                        Jenis Kelamin
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>

                                        <?php

                                        if ($siswa['jk'] === 'L') {

                                            echo 'Laki-laki';

                                        } elseif ($siswa['jk'] === 'P') {

                                            echo 'Perempuan';

                                        } else {

                                            echo '-';

                                        }

                                        ?>

                                    </td>

                                </tr>


                                <!-- KELAS -->

                                <tr>

                                    <th>
                                        Kelas
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $siswa['nama_kelas']
                                        ); ?>

                                    </td>

                                </tr>


                                <!-- TAHUN PELAJARAN -->

                                <tr>

                                    <th>
                                        Tahun Pelajaran
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $tahun['nama_tahun']
                                        ); ?>

                                    </td>

                                </tr>


                                <!-- STATUS -->

                                <tr>

                                    <th>
                                        Status
                                    </th>

                                    <td>
                                        :
                                    </td>

                                    <td>

                                        <?php if ($siswa['status'] === 'Aktif'): ?>

                                            <span class="badge bg-success">
                                                Aktif
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-danger">
                                                Tidak Aktif
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>


                            </table>

                        </div>

                    </div>

                </div>

            </div>


        </div>


        <!-- INFORMASI AKUN -->

        <div class="card border-0 shadow-sm mt-4">


            <div class="card-header bg-light">

                <h5 class="mb-0">
                    🔐 Informasi Akun
                </h5>

            </div>


            <div class="card-body">

                <div class="alert alert-info mb-0">

                    <strong>Keamanan Akun</strong>

                    <br>

                    Gunakan akun SIMU hanya untuk keperluan ujian.
                    Jangan memberikan NIS dan password kepada siswa lain.

                </div>

            </div>

        </div>


    </div>

</div>


<?php require_once "../template/footer.php"; ?>


</body>
</html>