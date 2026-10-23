<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Hasil Ujian Siswa
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
| AMBIL HASIL UJIAN
|--------------------------------------------------------------------------
|
| Relasi:
|
| hasil_ujian
|      ↓
| sesi_ujian
|      ↓
| ujian
|
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        h.id_hasil,
        h.id_sesi,
        h.nilai_pg,
        h.nilai_uraian,
        h.nilai_akhir,
        h.dibuat_pada,
        h.diubah_pada,

        s.id_siswa,
        s.id_ujian,
        s.waktu_mulai,
        s.waktu_selesai,

        u.nama_ujian,
        u.id_tahun,
        u.id_kelas

    FROM hasil_ujian h

    INNER JOIN sesi_ujian s
        ON h.id_sesi = s.id_sesi

    INNER JOIN ujian u
        ON s.id_ujian = u.id_ujian

    WHERE
        s.id_siswa = ?
        AND u.id_tahun = ?
        AND h.dibuat_pada IS NOT NULL

    ORDER BY
        h.dibuat_pada DESC
");

$query->execute([
    $id_siswa,
    $tahun['id_tahun']
]);

$hasilUjian = $query->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| JUMLAH HASIL
|--------------------------------------------------------------------------
*/

$totalHasil = count($hasilUjian);


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
                    Hasil Ujian
                </h4>

                <p class="text-muted mb-0">
                    Daftar hasil ujian yang telah Anda selesaikan.
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

            &nbsp; | &nbsp;

            NIS:
            <?= htmlspecialchars($nis); ?>

            &nbsp; | &nbsp;

            Kelas:
            <?= htmlspecialchars($nama_kelas); ?>

            &nbsp; | &nbsp;

            Tahun Pelajaran:
            <?= htmlspecialchars($tahun['nama_tahun']); ?>

        </div>


        <!-- RINGKASAN -->

        <div class="row g-4 mb-4">


            <!-- TOTAL UJIAN -->

            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div
                                style="
                                    width:55px;
                                    height:55px;
                                    border-radius:50%;
                                    background:#e7f1ff;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:26px;
                                "
                            >
                                📝
                            </div>

                            <div class="ms-3">

                                <small class="text-muted">
                                    Ujian Selesai
                                </small>

                                <h3 class="fw-bold mb-0 text-primary">
                                    <?= $totalHasil; ?>
                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- NILAI TERTINGGI -->

            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div
                                style="
                                    width:55px;
                                    height:55px;
                                    border-radius:50%;
                                    background:#e8f8ef;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:26px;
                                "
                            >
                                🏆
                            </div>

                            <div class="ms-3">

                                <small class="text-muted">
                                    Nilai Tertinggi
                                </small>

                                <h3 class="fw-bold mb-0 text-success">

                                    <?php

                                    if ($totalHasil > 0) {

                                        $nilaiTertinggi = max(
                                            array_column(
                                                $hasilUjian,
                                                'nilai_akhir'
                                            )
                                        );

                                        echo number_format(
                                            (float) $nilaiTertinggi,
                                            2,
                                            ',',
                                            '.'
                                        );

                                    } else {

                                        echo "0,00";

                                    }

                                    ?>

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- RATA-RATA -->

            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div
                                style="
                                    width:55px;
                                    height:55px;
                                    border-radius:50%;
                                    background:#fff4df;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:26px;
                                "
                            >
                                📊
                            </div>

                            <div class="ms-3">

                                <small class="text-muted">
                                    Rata-rata Nilai
                                </small>

                                <h3 class="fw-bold mb-0 text-warning">

                                    <?php

                                    if ($totalHasil > 0) {

                                        $jumlahNilai = 0;

                                        foreach ($hasilUjian as $hasil) {

                                            $jumlahNilai +=
                                                (float) $hasil['nilai_akhir'];

                                        }

                                        $rataRata =
                                            $jumlahNilai / $totalHasil;

                                        echo number_format(
                                            $rataRata,
                                            2,
                                            ',',
                                            '.'
                                        );

                                    } else {

                                        echo "0,00";

                                    }

                                    ?>

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- DAFTAR HASIL -->

        <div class="card border-0 shadow-sm">


            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    📋 Daftar Hasil Ujian
                </h5>

            </div>


            <div class="card-body">


                <?php if ($totalHasil > 0): ?>


                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th width="60">
                                        No
                                    </th>

                                    <th>
                                        Nama Ujian
                                    </th>

                                    <th class="text-center">
                                        Nilai PG
                                    </th>

                                    <th class="text-center">
                                        Nilai Uraian
                                    </th>

                                    <th class="text-center">
                                        Nilai Akhir
                                    </th>

                                    <th class="text-center">
                                        Tanggal
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                $no = 1;

                                foreach ($hasilUjian as $hasil):

                                    $nilaiAkhir =
                                        (float) $hasil['nilai_akhir'];

                                ?>

                                    <tr>


                                        <!-- NO -->

                                        <td>
                                            <?= $no++; ?>
                                        </td>


                                        <!-- NAMA UJIAN -->

                                        <td>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $hasil['nama_ujian']
                                                ); ?>

                                            </strong>

                                        </td>


                                        <!-- NILAI PG -->

                                        <td class="text-center">

                                            <?= number_format(
                                                (float) $hasil['nilai_pg'],
                                                2,
                                                ',',
                                                '.'
                                            ); ?>

                                        </td>


                                        <!-- NILAI URAIAN -->

                                        <td class="text-center">

                                            <?= number_format(
                                                (float) $hasil['nilai_uraian'],
                                                2,
                                                ',',
                                                '.'
                                            ); ?>

                                        </td>


                                        <!-- NILAI AKHIR -->

                                        <td class="text-center">

                                            <?php

                                            if ($nilaiAkhir >= 75) {

                                                $badge = "success";

                                            } elseif ($nilaiAkhir >= 60) {

                                                $badge = "warning";

                                            } else {

                                                $badge = "danger";

                                            }

                                            ?>

                                            <span
                                                class="badge bg-<?= $badge; ?>"
                                                style="
                                                    font-size:14px;
                                                    padding:8px 12px;
                                                "
                                            >

                                                <?= number_format(
                                                    $nilaiAkhir,
                                                    2,
                                                    ',',
                                                    '.'
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- TANGGAL -->

                                        <td class="text-center">

                                            <?php

                                            if (
                                                !empty(
                                                    $hasil['dibuat_pada']
                                                )
                                            ) {

                                                echo date(
                                                    'd-m-Y H:i',
                                                    strtotime(
                                                        $hasil['dibuat_pada']
                                                    )
                                                );

                                            } else {

                                                echo "-";

                                            }

                                            ?>

                                        </td>


                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                <?php else: ?>


                    <!-- BELUM ADA HASIL -->

                    <div class="text-center py-5">

                        <div style="font-size:65px;">
                            📊
                        </div>

                        <h5 class="mt-3">
                            Belum Ada Hasil Ujian
                        </h5>

                        <p class="text-muted">

                            Anda belum memiliki hasil ujian
                            pada Tahun Pelajaran ini.

                        </p>

                        <a
                            href="../ujian/index.php"
                            class="btn btn-primary"
                        >
                            📝 Lihat Daftar Ujian
                        </a>

                    </div>


                <?php endif; ?>


            </div>

        </div>


        <!-- INFORMASI NILAI -->

        <?php if ($totalHasil > 0): ?>

            <div class="alert alert-secondary mt-4">

                <strong>Informasi Nilai:</strong>

                Nilai akhir merupakan hasil pengolahan nilai
                sesuai dengan ketentuan penilaian ujian.

                Silakan hubungi guru apabila terdapat
                ketidaksesuaian pada hasil ujian.

            </div>

        <?php endif; ?>


    </div>

</div>


<?php require_once "../template/footer.php"; ?>


</body>
</html>