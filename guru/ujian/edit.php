<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Edit Ujian
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
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
| Ambil Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun pelajaran aktif.";

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Ujian
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

    SELECT *

    FROM ujian

    WHERE id_ujian = ?

    AND dihapus_pada IS NULL

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

/*
|--------------------------------------------------------------------------
| Ambil Data Kelas
|--------------------------------------------------------------------------
|
| CATATAN:
| Tabel kelas TIDAK memiliki kolom jenjang.
|
|--------------------------------------------------------------------------
*/

$queryKelas = $pdo->prepare("

    SELECT

        id_kelas,
        nama_kelas

    FROM kelas

    WHERE dihapus_pada IS NULL

    ORDER BY nama_kelas ASC

");

$queryKelas->execute();

$dataKelas = $queryKelas->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Ambil Data Paket Soal
|--------------------------------------------------------------------------
|
| jenjang memang berada di tabel paket_soal.
|
|--------------------------------------------------------------------------
*/

$queryPaket = $pdo->prepare("

    SELECT

        id_paket,
        nama_paket,
        jenjang

    FROM paket_soal

    WHERE dihapus_pada IS NULL

    ORDER BY
        jenjang ASC,
        nama_paket ASC

");

$queryPaket->execute();

$dataPaket = $queryPaket->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="wrapper">

    <?php require_once "../template/sidebar.php"; ?>

    <div class="content">

        <?php require_once "../template/topbar.php"; ?>

        <div class="container-fluid mt-4">

            <div class="card shadow border-0">

                <!-- HEADER -->

                <div class="card-header bg-warning text-dark">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h4 class="mb-1">
                                ✏️ Edit Ujian
                            </h4>

                            <small>
                                Perbarui konfigurasi ujian
                            </small>

                        </div>

                        <div>

                            <a
                                href="index.php"
                                class="btn btn-dark"
                            >

                                <i class="bi bi-arrow-left-circle"></i>

                                Kembali

                            </a>

                        </div>

                    </div>

                </div>


                <!-- BODY -->

                <div class="card-body">

                    <?php

                    if (isset($_SESSION['pesan'])) {

                    ?>

                        <div class="alert alert-<?= htmlspecialchars($_SESSION['warna']); ?> alert-dismissible fade show">

                            <?= htmlspecialchars($_SESSION['pesan']); ?>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                            </button>

                        </div>

                    <?php

                        unset($_SESSION['pesan']);
                        unset($_SESSION['warna']);

                    }

                    ?>


                    <!-- FORM -->

                    <form
                        action="proses_edit.php"
                        method="POST"
                    >

                        <input
                            type="hidden"
                            name="id_ujian"
                            value="<?= (int) $ujian['id_ujian']; ?>"
                        >

                        <input
                            type="hidden"
                            name="id_tahun"
                            value="<?= (int) $ujian['id_tahun']; ?>"
                        >


                        <!-- IDENTITAS UJIAN -->

                        <div class="row">

                            <div class="col-md-8">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Nama Ujian

                                        <span class="text-danger">*</span>

                                    </label>

                                    <input
                                        type="text"
                                        name="nama_ujian"
                                        class="form-control"
                                        maxlength="100"
                                        required
                                        value="<?= htmlspecialchars($ujian['nama_ujian']); ?>"
                                    >

                                </div>

                            </div>


                            <div class="col-md-4">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Durasi (Menit)

                                        <span class="text-danger">*</span>

                                    </label>

                                    <input
                                        type="number"
                                        name="durasi"
                                        class="form-control"
                                        min="1"
                                        required
                                        value="<?= (int) $ujian['durasi']; ?>"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- KELAS DAN PAKET -->

                        <div class="row">

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Kelas

                                        <span class="text-danger">*</span>

                                    </label>

                                    <select
                                        name="id_kelas"
                                        class="form-select"
                                        required
                                    >

                                        <?php foreach ($dataKelas as $kelas) { ?>

                                            <option
                                                value="<?= (int) $kelas['id_kelas']; ?>"
                                                <?= (
                                                    $kelas['id_kelas'] == $ujian['id_kelas']
                                                )
                                                    ? 'selected'
                                                    : ''; ?>
                                            >

                                                <?= htmlspecialchars(
                                                    $kelas['nama_kelas']
                                                ); ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Paket Soal

                                        <span class="text-danger">*</span>

                                    </label>

                                    <select
                                        name="id_paket"
                                        class="form-select"
                                        required
                                    >

                                        <?php foreach ($dataPaket as $paket) { ?>

                                            <option
                                                value="<?= (int) $paket['id_paket']; ?>"
                                                <?= (
                                                    $paket['id_paket'] == $ujian['id_paket']
                                                )
                                                    ? 'selected'
                                                    : ''; ?>
                                            >

                                                <?= htmlspecialchars(
                                                    $paket['jenjang']
                                                ); ?>

                                                -

                                                <?= htmlspecialchars(
                                                    $paket['nama_paket']
                                                ); ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                            </div>

                        </div>


                        <hr>


                        <!-- JENIS PENILAIAN -->

                        <h5 class="mb-3">
                            Jenis Penilaian
                        </h5>


                        <div class="row">

                            <div class="col-md-4">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Jenis Penilaian
                                    </label>

                                    <select
                                        name="jenis_penilaian"
                                        id="jenis_penilaian"
                                        class="form-select"
                                    >

                                        <option
                                            value="PG"
                                            <?= (
                                                $ujian['jenis_penilaian'] == "PG"
                                            )
                                                ? 'selected'
                                                : ''; ?>
                                        >

                                            Pilihan Ganda

                                        </option>


                                        <option
                                            value="Campuran"
                                            <?= (
                                                $ujian['jenis_penilaian'] == "Campuran"
                                            )
                                                ? 'selected'
                                                : ''; ?>
                                        >

                                            Campuran (PG + Uraian)

                                        </option>

                                    </select>

                                </div>

                            </div>


                            <div class="col-md-4">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Bobot PG (%)
                                    </label>

                                    <input
                                        type="number"
                                        name="bobot_pg"
                                        id="bobot_pg"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        required
                                        value="<?= (float) $ujian['bobot_pg']; ?>"
                                    >

                                </div>

                            </div>


                            <div class="col-md-4">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Bobot Uraian (%)
                                    </label>

                                    <input
                                        type="number"
                                        name="bobot_uraian"
                                        id="bobot_uraian"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        required
                                        value="<?= (float) $ujian['bobot_uraian']; ?>"
                                    >

                                </div>

                            </div>

                        </div>


                        <hr>


                        <!-- PENGATURAN UJIAN -->

                        <h5 class="mb-3">
                            Pengaturan Ujian
                        </h5>


                        <div class="row">

                            <div class="col-md-6">

                                <!-- ACAK SOAL -->

                                <div class="form-check mb-3">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="acak_soal"
                                        id="acak_soal"
                                        value="Ya"
                                        <?= (
                                            $ujian['acak_soal'] == "Ya"
                                        )
                                            ? "checked"
                                            : ""; ?>
                                    >

                                    <label
                                        class="form-check-label"
                                        for="acak_soal"
                                    >

                                        Acak Urutan Soal

                                    </label>

                                </div>


                                <!-- ACAK PILIHAN -->

                                <div class="form-check mb-3">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="acak_pilihan"
                                        id="acak_pilihan"
                                        value="Ya"
                                        <?= (
                                            $ujian['acak_pilihan'] == "Ya"
                                        )
                                            ? "checked"
                                            : ""; ?>
                                    >

                                    <label
                                        class="form-check-label"
                                        for="acak_pilihan"
                                    >

                                        Acak Pilihan Jawaban

                                    </label>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <!-- FULLSCREEN -->

                                <div class="form-check mb-3">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="fullscreen"
                                        id="fullscreen"
                                        value="Ya"
                                        <?= (
                                            $ujian['fullscreen'] == "Ya"
                                        )
                                            ? "checked"
                                            : ""; ?>
                                    >

                                    <label
                                        class="form-check-label"
                                        for="fullscreen"
                                    >

                                        Wajib Fullscreen

                                    </label>

                                </div>


                                <!-- ANTI CHEAT -->

                                <div class="form-check mb-3">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="anti_cheat"
                                        id="anti_cheat"
                                        value="Ya"
                                        <?= (
                                            $ujian['anti_cheat'] == "Ya"
                                        )
                                            ? "checked"
                                            : ""; ?>
                                    >

                                    <label
                                        class="form-check-label"
                                        for="anti_cheat"
                                    >

                                        Aktifkan Anti Cheat

                                    </label>

                                </div>

                            </div>

                        </div>


                        <!-- MAKSIMAL PELANGGARAN -->

                        <div class="row">

                            <div class="col-md-4">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Maksimal Pelanggaran

                                    </label>

                                    <input
                                        type="number"
                                        name="maksimal_pelanggaran"
                                        class="form-control"
                                        min="1"
                                        required
                                        value="<?= (int) $ujian['maksimal_pelanggaran']; ?>"
                                    >

                                    <small class="text-muted">

                                        Ujian otomatis dihentikan jika batas
                                        pelanggaran tercapai.

                                    </small>

                                </div>

                            </div>

                        </div>


                        <hr>


                        <!-- TOMBOL -->

                        <div class="d-flex justify-content-end gap-2">

                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >

                                <i class="bi bi-arrow-left-circle"></i>

                                Kembali

                            </a>


                            <button
                                type="submit"
                                class="btn btn-warning"
                            >

                                <i class="bi bi-save"></i>

                                Simpan Perubahan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- JAVASCRIPT BOBOT -->

<script>

const jenisPenilaian =
    document.getElementById('jenis_penilaian');

const bobotPG =
    document.getElementById('bobot_pg');

const bobotUraian =
    document.getElementById('bobot_uraian');


function aturBobot() {

    if (jenisPenilaian.value === "PG") {

        bobotPG.value = 100;

        bobotUraian.value = 0;

        bobotPG.readOnly = true;

        bobotUraian.readOnly = true;

    } else {

        if (
            Number(bobotPG.value) === 100 &&
            Number(bobotUraian.value) === 0
        ) {

            bobotPG.value = 75;

            bobotUraian.value = 25;

        }

        bobotPG.readOnly = false;

        bobotUraian.readOnly = false;

    }

}


jenisPenilaian.addEventListener(
    "change",
    aturBobot
);


aturBobot();

</script>


<?php require_once "../template/footer.php"; ?>

</body>

</html>