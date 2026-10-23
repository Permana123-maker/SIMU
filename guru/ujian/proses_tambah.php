<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Tambah Ujian
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";


/*
|--------------------------------------------------------------------------
| VALIDASI REQUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| TAHUN PELAJARAN AKTIF
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun pelajaran aktif.";

    header("Location: tambah.php");
    exit;

}

$id_tahun = (int) $tahun['id_tahun'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA FORM
|--------------------------------------------------------------------------
*/

$id_kelas = (int) ($_POST['id_kelas'] ?? 0);

$id_paket = (int) ($_POST['id_paket'] ?? 0);

$nama_ujian = trim($_POST['nama_ujian'] ?? '');

$durasi = (int) ($_POST['durasi'] ?? 0);

$jenis_penilaian =
    $_POST['jenis_penilaian'] ?? 'PG';

$bobot_pg =
    (float) ($_POST['bobot_pg'] ?? 100);

$bobot_uraian =
    (float) ($_POST['bobot_uraian'] ?? 0);


/*
|--------------------------------------------------------------------------
| CHECKBOX
|--------------------------------------------------------------------------
*/

$acak_soal = isset($_POST['acak_soal'])
    ? "Ya"
    : "Tidak";

$acak_pilihan = isset($_POST['acak_pilihan'])
    ? "Ya"
    : "Tidak";

$fullscreen = isset($_POST['fullscreen'])
    ? "Ya"
    : "Tidak";

$anti_cheat = isset($_POST['anti_cheat'])
    ? "Ya"
    : "Tidak";

$maksimal_pelanggaran =
    (int) ($_POST['maksimal_pelanggaran'] ?? 3);


/*
|--------------------------------------------------------------------------
| VALIDASI INPUT DASAR
|--------------------------------------------------------------------------
*/

if (
    $nama_ujian === '' ||
    $id_kelas <= 0 ||
    $id_paket <= 0 ||
    $durasi <= 0
) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Semua data wajib diisi.";

    header("Location: tambah.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI JENIS PENILAIAN
|--------------------------------------------------------------------------
*/

if (
    $jenis_penilaian !== "PG" &&
    $jenis_penilaian !== "Campuran"
) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Jenis penilaian tidak valid.";

    header("Location: tambah.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| ATUR BOBOT
|--------------------------------------------------------------------------
*/

if ($jenis_penilaian === "PG") {

    $bobot_pg = 100;
    $bobot_uraian = 0;

} else {

    $bobot_pg = 75;
    $bobot_uraian = 25;

}


/*
|--------------------------------------------------------------------------
| VALIDASI KELAS
|--------------------------------------------------------------------------
| Tabel kelas TIDAK memiliki kolom jenjang.
|--------------------------------------------------------------------------
*/

$queryKelas = $pdo->prepare("

    SELECT
        id_kelas,
        nama_kelas

    FROM kelas

    WHERE
        id_kelas = ?

        AND dihapus_pada IS NULL

    LIMIT 1

");

$queryKelas->execute([
    $id_kelas
]);

$kelas = $queryKelas->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Kelas tidak ditemukan.";

    header("Location: tambah.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI PAKET SOAL
|--------------------------------------------------------------------------
| jenjang berada di tabel paket_soal.
|--------------------------------------------------------------------------
*/

$queryPaket = $pdo->prepare("

    SELECT
        id_paket,
        nama_paket,
        jenjang

    FROM paket_soal

    WHERE
        id_paket = ?

        AND dihapus_pada IS NULL

    LIMIT 1

");

$queryPaket->execute([
    $id_paket
]);

$paket = $queryPaket->fetch(PDO::FETCH_ASSOC);

if (!$paket) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Paket soal tidak ditemukan.";

    header("Location: tambah.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| HITUNG SOAL DALAM PAKET
|--------------------------------------------------------------------------
*/

$queryJumlah = $pdo->prepare("

    SELECT

        COUNT(*) AS total_soal,

        SUM(
            CASE
                WHEN jenis = 'PG' THEN 1
                ELSE 0
            END
        ) AS total_pg,

        SUM(
            CASE
                WHEN jenis = 'URAian' THEN 1
                ELSE 0
            END
        ) AS total_uraian

    FROM soal

    WHERE
        id_paket = ?

        AND dihapus_pada IS NULL

");

$queryJumlah->execute([
    $id_paket
]);

$jumlah = $queryJumlah->fetch(PDO::FETCH_ASSOC);

$totalSoal =
    (int) ($jumlah['total_soal'] ?? 0);

$totalPG =
    (int) ($jumlah['total_pg'] ?? 0);

$totalUraian =
    (int) ($jumlah['total_uraian'] ?? 0);


/*
|--------------------------------------------------------------------------
| PAKET HARUS MEMILIKI SOAL
|--------------------------------------------------------------------------
*/

if ($totalSoal <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Paket soal \"" .
        $paket['nama_paket'] .
        "\" belum memiliki soal.";

    header("Location: tambah.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI SOAL PILIHAN GANDA
|--------------------------------------------------------------------------
*/

if (
    $jenis_penilaian === "PG" &&
    $totalPG <= 0
) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Paket soal yang dipilih belum memiliki " .
        "soal Pilihan Ganda.";

    header("Location: tambah.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI SOAL CAMPURAN
|--------------------------------------------------------------------------
*/

if ($jenis_penilaian === "Campuran") {

    if (
        $totalPG <= 0 ||
        $totalUraian <= 0
    ) {

        $_SESSION['warna'] = "danger";

        $_SESSION['pesan'] =
            "Paket soal Campuran harus memiliki " .
            "soal Pilihan Ganda dan Uraian.";

        header("Location: tambah.php");
        exit;

    }

}


/*
|--------------------------------------------------------------------------
| CEK DUPLIKASI UJIAN
|--------------------------------------------------------------------------
*/

$queryCek = $pdo->prepare("

    SELECT
        COUNT(*) AS total

    FROM ujian

    WHERE
        id_tahun = ?

        AND id_kelas = ?

        AND nama_ujian = ?

        AND dihapus_pada IS NULL

");

$queryCek->execute([

    $id_tahun,
    $id_kelas,
    $nama_ujian

]);

$cek = $queryCek->fetch(PDO::FETCH_ASSOC);

if ((int) $cek['total'] > 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Ujian dengan nama tersebut sudah ada " .
        "pada kelas ini.";

    header("Location: tambah.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| SIMPAN UJIAN
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    $queryInsert = $pdo->prepare("

        INSERT INTO ujian (

            id_tahun,
            id_kelas,
            id_paket,
            nama_ujian,
            durasi,
            jenis_penilaian,
            bobot_pg,
            bobot_uraian,
            acak_soal,
            acak_pilihan,
            fullscreen,
            anti_cheat,
            maksimal_pelanggaran,
            status

        )

        VALUES (

            ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?

        )

    ");


    $queryInsert->execute([

        $id_tahun,
        $id_kelas,
        $id_paket,
        $nama_ujian,
        $durasi,
        $jenis_penilaian,
        $bobot_pg,
        $bobot_uraian,
        $acak_soal,
        $acak_pilihan,
        $fullscreen,
        $anti_cheat,
        $maksimal_pelanggaran,
        "Aktif"

    ]);


    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | BERHASIL
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] =
        "Ujian berhasil ditambahkan. " .
        "Paket soal \"" .
        $paket['nama_paket'] .
        "\" memiliki " .
        $totalSoal .
        " soal.";


    header("Location: index.php");
    exit;


} catch (Exception $e) {


    /*
    |--------------------------------------------------------------------------
    | ROLLBACK
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    /*
    |--------------------------------------------------------------------------
    | ERROR
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Terjadi kesalahan saat menyimpan ujian: " .
        $e->getMessage();


    header("Location: tambah.php");
    exit;

}