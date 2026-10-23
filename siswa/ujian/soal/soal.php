<?php

/*
|--------------------------------------------------------------------------
| SIMU (Sistem Informasi Manajemen Ujian)
|--------------------------------------------------------------------------
| Halaman Soal Ujian
|--------------------------------------------------------------------------
*/

session_start();

/*
|--------------------------------------------------------------------------
| Load System
|--------------------------------------------------------------------------
*/

require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";

/*
|--------------------------------------------------------------------------
| Validasi Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['siswa'])) {

    header("Location: ../login/login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    die("Belum ada Tahun Pelajaran aktif.");

}

/*
|--------------------------------------------------------------------------
| Data Login Siswa
|--------------------------------------------------------------------------
*/

$id_siswa = (int) $_SESSION['siswa']['id_siswa'];
$id_kelas = (int) $_SESSION['siswa']['id_kelas'];

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
    $_SESSION['pesan'] = "Ujian tidak ditemukan.";

    header("Location: ../dashboard/index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Sesi Ujian
|--------------------------------------------------------------------------
*/

$querySesi = $pdo->prepare("

SELECT

    su.id_sesi,
    su.id_ujian,
    su.id_siswa,

    su.waktu_mulai,
    su.waktu_selesai,

    su.status,
    su.jumlah_pelanggaran,

    u.id_paket,
    u.nama_ujian,
    u.durasi,

    u.jenis_penilaian,

    u.acak_soal,
    u.acak_pilihan,

    u.fullscreen,
    u.anti_cheat,
    u.maksimal_pelanggaran

FROM sesi_ujian su

INNER JOIN ujian u

ON su.id_ujian = u.id_ujian

WHERE

    su.id_ujian = ?

AND

    su.id_siswa = ?

LIMIT 1

");

$querySesi->execute([

    $id_ujian,

    $id_siswa

]);

$sesi = $querySesi->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Validasi Sesi
|--------------------------------------------------------------------------
*/

if (!$sesi) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Sesi ujian tidak ditemukan.";

    header("Location: ../dashboard/index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Status Sesi
|--------------------------------------------------------------------------
*/

if ($sesi['status'] == "Selesai") {

    header("Location: selesai.php?id_ujian=".$id_ujian);
    exit;

}

if ($sesi['status'] != "Sedang Mengerjakan") {

    header("Location: konfirmasi.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Waktu Server
|--------------------------------------------------------------------------
*/

$serverTime = time();

$deadline = strtotime($sesi['waktu_selesai']);

if ($serverTime >= $deadline) {

    header("Location: selesai.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Nomor Soal Aktif
|--------------------------------------------------------------------------
*/

$nomor = isset($_GET['nomor'])

    ? (int) $_GET['nomor']

    : 1;

if ($nomor < 1) {

    $nomor = 1;

}

/*
|--------------------------------------------------------------------------
| Jumlah Soal
|--------------------------------------------------------------------------
*/

$queryJumlah = $pdo->prepare("

SELECT

    COUNT(*) AS total

FROM urutan_soal

WHERE

    id_sesi = ?

");

$queryJumlah->execute([

    $sesi['id_sesi']

]);

$totalSoal = (int) $queryJumlah->fetch(PDO::FETCH_ASSOC)['total'];

if ($totalSoal <= 0) {

    die("Data urutan soal tidak ditemukan.");

}

if ($nomor > $totalSoal) {

    $nomor = $totalSoal;

}

/*
|--------------------------------------------------------------------------
| Ambil Soal Aktif
|--------------------------------------------------------------------------
*/

$querySoal = $pdo->prepare("

SELECT

    us.nomor_tampil,

    s.id_soal,
    s.kode_soal,
    s.nomor,
    s.jenis,
    s.pertanyaan,

    s.pilihan_a,
    s.pilihan_b,
    s.pilihan_c,
    s.pilihan_d,

    s.kunci_jawaban,
    s.bobot,

    up.pilihan_a AS map_a,
    up.pilihan_b AS map_b,
    up.pilihan_c AS map_c,
    up.pilihan_d AS map_d

FROM urutan_soal us

INNER JOIN soal s

ON us.id_soal = s.id_soal

INNER JOIN urutan_pilihan up

ON

    up.id_sesi = us.id_sesi

AND

    up.id_soal = us.id_soal

WHERE

    us.id_sesi = ?

AND

    us.nomor_tampil = ?

LIMIT 1

");

$querySoal->execute([

    $sesi['id_sesi'],

    $nomor

]);

$soal = $querySoal->fetch(PDO::FETCH_ASSOC);

if (!$soal) {

    die("Soal tidak ditemukan.");

}

/*
|--------------------------------------------------------------------------
| Mapping Pilihan Jawaban
|--------------------------------------------------------------------------
*/

$pilihanAsli = [

    'A' => $soal['pilihan_a'],

    'B' => $soal['pilihan_b'],

    'C' => $soal['pilihan_c'],

    'D' => $soal['pilihan_d']

];

$pilihanTampil = [

    'A' => $pilihanAsli[$soal['map_a']],

    'B' => $pilihanAsli[$soal['map_b']],

    'C' => $pilihanAsli[$soal['map_c']],

    'D' => $pilihanAsli[$soal['map_d']]

];

/*
|--------------------------------------------------------------------------
| Ambil Jawaban Siswa
|--------------------------------------------------------------------------
*/

$queryJawaban = $pdo->prepare("

SELECT

    jawaban

FROM jawaban_siswa

WHERE

    id_sesi = ?

AND

    id_soal = ?

LIMIT 1

");

$queryJawaban->execute([

    $sesi['id_sesi'],

    $soal['id_soal']

]);

$dataJawaban = $queryJawaban->fetch(PDO::FETCH_ASSOC);

$jawabanSiswa = $dataJawaban['jawaban'] ?? '';

/*
|--------------------------------------------------------------------------
| Radio Button Checked
|--------------------------------------------------------------------------
*/

$checked = [

    'A' => '',

    'B' => '',

    'C' => '',

    'D' => ''

];

if ($jawabanSiswa != '') {

    $mappingHuruf = [

        'A' => $soal['map_a'],

        'B' => $soal['map_b'],

        'C' => $soal['map_c'],

        'D' => $soal['map_d']

    ];

    foreach ($mappingHuruf as $hurufTampil => $hurufAsli) {

        if ($hurufAsli === $jawabanSiswa) {

            $checked[$hurufTampil] = "checked";

            break;

        }

    }

}

require_once "../template/header.php";
?>

<div class="wrapper">

    <?php require_once "../template/sidebar.php"; ?>

    <div class="content">

        <?php require_once "../template/topbar.php"; ?>

        <div class="container-fluid mt-3">

<!-- =======================================================
HEADER UJIAN
======================================================= -->

<div class="row mb-3">

    <div class="col-lg-8">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <h4 class="mb-1">

                    <?= htmlspecialchars($sesi['nama_ujian']); ?>

                </h4>

                <small class="text-muted">

                    Soal

                    <strong><?= $nomor; ?></strong>

                    dari

                    <strong><?= $totalSoal; ?></strong>

                </small>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card shadow-sm border-0">

            <div class="card-body text-center">

                <div class="text-muted">

                    Sisa Waktu

                </div>

                <h3

                    id="timer"

                    class="text-success fw-bold mb-0"

                    data-selesai="<?= strtotime($sesi['waktu_selesai']); ?>"

                >

                    --:--:--

                </h3>

                <div

    id="status-autosave"

    class="small text-success mt-2"

>

    Siap

</div>

            </div>

        </div>

    </div>

</div>

<!-- =======================================================
CARD SOAL
======================================================= -->

<div

    class="card shadow-sm border-0"

    id="card-soal"

>

<div class="card-header">

    <strong>

        <?= htmlspecialchars($soal['kode_soal']); ?>

    </strong>

</div>

<div class="card-body">

<form id="form-soal">

<input
type="hidden"
id="id_sesi"
value="<?= $sesi['id_sesi']; ?>">

<input
type="hidden"
id="id_ujian"
value="<?= $id_ujian; ?>">

<input
type="hidden"
id="id_soal"
value="<?= $soal['id_soal']; ?>">

<input
type="hidden"
id="nomor_soal"
value="<?= $nomor; ?>">

<!-- =======================================================
PERTANYAAN
======================================================= -->

<div

    id="pertanyaan-soal"

    class="mb-4"

>

<?= nl2br(htmlspecialchars($soal['pertanyaan'])); ?>

</div>

<!-- =======================================================
PILIHAN JAWABAN
======================================================= -->

<?php

foreach (

    [

        'A',

        'B',

        'C',

        'D'

    ]

    as

    $huruf

) {

?>

<div class="form-check mb-3">

<input

    class="form-check-input"

    type="radio"

    name="jawaban"

    id="jawaban<?= $huruf; ?>"

    value="<?= $huruf; ?>"

    data-id-soal="<?= $soal['id_soal']; ?>"

    <?= $checked[$huruf]; ?>

>

<label

    class="form-check-label"

    for="jawaban<?= $huruf; ?>"

>

<strong>

<?= $huruf; ?>.

</strong>

<?= htmlspecialchars($pilihanTampil[$huruf]); ?>

</label>

</div>

<?php

}

?>

</form>

</div>

</div>

<!-- =======================================================
NAVIGASI NOMOR SOAL
======================================================= -->

<div class="card shadow-sm border-0 mt-4">

    <div class="card-header">

        <strong>

            Navigasi Soal

        </strong>

    </div>

    <div class="card-body">

        <div class="d-flex flex-wrap gap-2">

<?php

$queryNavigasi = $pdo->prepare("

SELECT

    nomor_tampil,

    id_soal

FROM urutan_soal

WHERE

    id_sesi = ?

ORDER BY nomor_tampil

");

$queryNavigasi->execute([

    $sesi['id_sesi']

]);

$daftarNomor = $queryNavigasi->fetchAll(PDO::FETCH_ASSOC);

foreach ($daftarNomor as $item) {

?>

<button

    type="button"

    class="btn <?= ($item['nomor_tampil'] == $nomor)
        ? 'btn-primary'
        : 'btn-outline-secondary'; ?>"

    data-nomor="<?= $item['nomor_tampil']; ?>"

>

    <?= $item['nomor_tampil']; ?>

</button>

<?php

}

?>

        </div>

    </div>

</div>

<!-- =======================================================
TOMBOL NAVIGASI
======================================================= -->

<div class="row mt-4">

    <div class="col-md-4">

<?php if ($nomor > 1) { ?>

<button

    type="button"

    class="btn btn-secondary w-100"

    id="btn-sebelumnya"

    data-nomor="<?= $nomor - 1; ?>"

>

    ← Sebelumnya

</button>

<?php } else { ?>

<button

    type="button"

    class="btn btn-secondary w-100"

    disabled

>

    ← Sebelumnya

</button>

<?php } ?>

    </div>

    <div class="col-md-4 text-center">

        <span class="fw-bold">

            Soal

            <?= $nomor; ?>

            /

            <?= $totalSoal; ?>

        </span>

    </div>

    <div class="col-md-4">

<?php if ($nomor < $totalSoal) { ?>

<button

    type="button"

    class="btn btn-primary w-100"

    id="btn-berikutnya"

    data-nomor="<?= $nomor + 1; ?>"

>

    Berikutnya →

</button>

<?php } else { ?>

<button

    type="button"

    class="btn btn-success w-100"

    id="btn-selesai"

>

    Selesai Ujian

</button>

<?php } ?>

    </div>

</div>

        </div>

    </div>

</div>

<?php require_once "../template/footer.php"; ?>