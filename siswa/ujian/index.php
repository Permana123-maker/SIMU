<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Daftar Ujian Siswa
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
| DATA SISWA
|--------------------------------------------------------------------------
*/

$id_siswa   = $_SESSION['siswa']['id_siswa'];
$id_kelas   = $_SESSION['siswa']['id_kelas'];
$nama_siswa = $_SESSION['siswa']['nama_siswa'];
$nis        = $_SESSION['siswa']['nis'];
$nama_kelas = $_SESSION['siswa']['nama_kelas'];


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
| AMBIL DAFTAR UJIAN
|--------------------------------------------------------------------------
|
| Ujian ditampilkan berdasarkan:
| 1. Tahun Pelajaran aktif
| 2. Kelas siswa
| 3. Ujian belum dihapus
|
| TIDAK menggunakan status Aktif/Tidak Aktif.
|
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        u.id_ujian,
        u.id_tahun,
        u.id_kelas,
        u.nama_ujian,
        u.durasi,
        u.jenis_penilaian,
        u.bobot_pg,
        u.bobot_uraian,
        u.acak_soal,
        u.acak_pilihan,
        u.fullscreen,
        u.anti_cheat,
        u.maksimal_pelanggaran

    FROM ujian u

    WHERE
        u.id_tahun = ?
        AND u.id_kelas = ?
        AND u.dihapus_pada IS NULL

    ORDER BY
        u.id_ujian DESC
");

$query->execute([
    $tahun['id_tahun'],
    $id_kelas
]);

$daftarUjian = $query->fetchAll(PDO::FETCH_ASSOC);


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
                    Daftar Ujian
                </h4>

                <p class="text-muted mb-0">
                    Ujian yang tersedia untuk kelas
                    <strong>
                        <?= htmlspecialchars($nama_kelas); ?>
                    </strong>
                </p>

            </div>


            <a
                href="../dashboard/index.php"
                class="btn btn-secondary"
            >
                ← Dashboard
            </a>

        </div>


        <!-- INFORMASI SISWA -->

        <div class="alert alert-info shadow-sm">

            <strong>
                <?= htmlspecialchars($nama_siswa); ?>
            </strong>

            &nbsp;|&nbsp;

            NIS:
            <?= htmlspecialchars($nis); ?>

            &nbsp;|&nbsp;

            Kelas:
            <?= htmlspecialchars($nama_kelas); ?>

            &nbsp;|&nbsp;

            Tahun Pelajaran:
            <?= htmlspecialchars($tahun['nama_tahun']); ?>

        </div>


        <!-- DAFTAR UJIAN -->

        <?php if (count($daftarUjian) > 0): ?>


            <div class="row g-4">

                <?php foreach ($daftarUjian as $ujian): ?>


                    <div class="col-lg-6 col-xl-4">

                        <div class="card shadow-sm border-0 h-100">


                            <!-- HEADER CARD -->

                            <div class="card-header bg-primary text-white">

                                <h5 class="mb-0">

                                    <?= htmlspecialchars(
                                        $ujian['nama_ujian']
                                    ); ?>

                                </h5>

                            </div>


                            <!-- BODY -->

                            <div class="card-body">


                                <!-- JENIS PENILAIAN -->

                                <div class="mb-3">

                                    <small class="text-muted">
                                        Jenis Penilaian
                                    </small>

                                    <div class="fw-semibold">

                                        <?= htmlspecialchars(
                                            $ujian['jenis_penilaian']
                                        ); ?>

                                    </div>

                                </div>


                                <!-- DURASI -->

                                <div class="mb-3">

                                    <small class="text-muted">
                                        Durasi
                                    </small>

                                    <div class="fw-semibold">

                                        <?= (int) $ujian['durasi']; ?>
                                        menit

                                    </div>

                                </div>


                                <!-- PENGACAKAN SOAL -->

                                <div class="mb-3">

                                    <small class="text-muted">
                                        Pengacakan Soal
                                    </small>

                                    <div>

                                        <?php if ($ujian['acak_soal']): ?>

                                            <span class="badge bg-success">
                                                Digunakan
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Tidak Digunakan
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- PENGACAKAN PILIHAN -->

                                <div class="mb-3">

                                    <small class="text-muted">
                                        Pengacakan Pilihan
                                    </small>

                                    <div>

                                        <?php if ($ujian['acak_pilihan']): ?>

                                            <span class="badge bg-success">
                                                Digunakan
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Tidak Digunakan
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- TOMBOL MULAI -->

                                <div class="d-grid mt-4">

                                    <a
                                        href="mulai.php?id_ujian=<?= (int) $ujian['id_ujian']; ?>"
                                        class="btn btn-primary"
                                    >

                                        📝 Mulai Ujian

                                    </a>

                                </div>


                            </div>

                        </div>

                    </div>


                <?php endforeach; ?>

            </div>


        <?php else: ?>


            <!-- TIDAK ADA UJIAN -->

            <div class="card shadow-sm border-0">

                <div class="card-body text-center py-5">

                    <div style="font-size:60px;">
                        📚
                    </div>

                    <h5 class="mt-3">
                        Belum Ada Ujian
                    </h5>

                    <p class="text-muted mb-0">

                        Saat ini belum ada ujian
                        untuk kelas Anda.

                    </p>

                </div>

            </div>


        <?php endif; ?>


    </div>

</div>


<?php require_once "../template/footer.php"; ?>

</body>
</html>