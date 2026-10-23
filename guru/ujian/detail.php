<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Detail Ujian
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Ujian
|--------------------------------------------------------------------------
*/

$id_ujian = isset($_GET['id_ujian'])
    ? (int) $_GET['id_ujian']
    : 0;

if ($id_ujian <= 0) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Detail Ujian
|--------------------------------------------------------------------------
|
| CATATAN:
| jenjang berasal dari tabel paket_soal (ps),
| BUKAN dari tabel kelas (k).
|
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

    SELECT

        u.*,

        tp.nama_tahun,

        k.nama_kelas,

        ps.nama_paket,

        ps.jenjang

    FROM ujian u

    INNER JOIN tahun_ajaran tp
        ON u.id_tahun = tp.id_tahun

    INNER JOIN kelas k
        ON u.id_kelas = k.id_kelas

    INNER JOIN paket_soal ps
        ON u.id_paket = ps.id_paket

    WHERE

        u.id_ujian = ?

    AND

        u.dihapus_pada IS NULL

    LIMIT 1

");

$query->execute([$id_ujian]);

$ujian = $query->fetch(PDO::FETCH_ASSOC);

if (!$ujian) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");
    exit;
}

?>

<div class="wrapper">

    <?php require_once "../template/sidebar.php"; ?>

    <div class="content">

        <?php require_once "../template/topbar.php"; ?>

        <div class="container-fluid mt-4">

            <div class="card shadow border-0">

                <!-- HEADER -->

                <div class="card-header bg-primary text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h4 class="mb-1">
                                👁 Detail Ujian
                            </h4>

                            <small>
                                Informasi lengkap data ujian
                            </small>

                        </div>

                        <div>

                            <a
                                href="index.php"
                                class="btn btn-light"
                            >

                                <i class="bi bi-arrow-left-circle"></i>

                                Kembali

                            </a>

                        </div>

                    </div>

                </div>


                <!-- BODY -->

                <div class="card-body">

                    <div class="row">

                        <!-- INFORMASI UTAMA -->

                        <div class="col-lg-6">

                            <table class="table table-bordered">

                                <tr>

                                    <th width="40%">
                                        Nama Ujian
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['nama_ujian']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Tahun Pelajaran
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['nama_tahun']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Jenjang
                                    </th>

                                    <td>

                                        <span class="badge bg-info text-dark">

                                            <?= htmlspecialchars(
                                                $ujian['jenjang']
                                            ); ?>

                                        </span>

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Kelas
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['nama_kelas']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Paket Soal
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['nama_paket']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Durasi
                                    </th>

                                    <td>

                                        <?= (int) $ujian['durasi']; ?>

                                        Menit

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Jenis Penilaian
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['jenis_penilaian']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Status
                                    </th>

                                    <td>

                                        <?php if (
                                            $ujian['status'] === "Aktif"
                                        ) { ?>

                                            <span class="badge bg-success">
                                                Aktif
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-secondary">
                                                Nonaktif
                                            </span>

                                        <?php } ?>

                                    </td>

                                </tr>

                            </table>

                        </div>


                        <!-- PENGATURAN UJIAN -->

                        <div class="col-lg-6">

                            <table class="table table-bordered">

                                <tr>

                                    <th width="55%">
                                        Bobot Pilihan Ganda
                                    </th>

                                    <td>

                                        <?= (float) $ujian['bobot_pg']; ?>%

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Bobot Uraian
                                    </th>

                                    <td>

                                        <?= (float) $ujian['bobot_uraian']; ?>%

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Acak Soal
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['acak_soal']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Acak Pilihan
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['acak_pilihan']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Fullscreen
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['fullscreen']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Anti Cheat
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $ujian['anti_cheat']
                                        ); ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Maksimal Pelanggaran
                                    </th>

                                    <td>

                                        <?= (int) $ujian[
                                            'maksimal_pelanggaran'
                                        ]; ?>

                                        Kali

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Dibuat Pada
                                    </th>

                                    <td>

                                        <?= !empty(
                                            $ujian['dibuat_pada']
                                        )
                                            ? date(
                                                'd-m-Y H:i',
                                                strtotime(
                                                    $ujian['dibuat_pada']
                                                )
                                            )
                                            : '-';
                                        ?>

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>


                    <hr>


                    <!-- TOMBOL -->

                    <div class="d-flex justify-content-end gap-2">

                        <a
                            href="edit.php?id_ujian=<?= (int) $ujian['id_ujian']; ?>"
                            class="btn btn-warning"
                        >

                            <i class="bi bi-pencil-square"></i>

                            Edit

                        </a>


                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >

                            <i class="bi bi-arrow-left-circle"></i>

                            Kembali

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once "../template/footer.php"; ?>

</body>

</html>