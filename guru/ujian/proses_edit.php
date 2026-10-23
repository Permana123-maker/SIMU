<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Edit Ujian
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Tahun Pelajaran Aktif
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
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$id_ujian = (int) ($_POST['id_ujian'] ?? 0);

$id_tahun = $tahun['id_tahun'];

$id_kelas = (int) ($_POST['id_kelas'] ?? 0);

$id_paket = (int) ($_POST['id_paket'] ?? 0);

$nama_ujian = trim($_POST['nama_ujian'] ?? '');

$durasi = (int) ($_POST['durasi'] ?? 0);

$jenis_penilaian = $_POST['jenis_penilaian'] ?? 'PG';

$bobot_pg = (float) ($_POST['bobot_pg'] ?? 100);

$bobot_uraian = (float) ($_POST['bobot_uraian'] ?? 0);

/*
|--------------------------------------------------------------------------
| Checkbox
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

$maksimal_pelanggaran = (int) ($_POST['maksimal_pelanggaran'] ?? 3);

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if (

    $id_ujian <= 0 ||

    empty($nama_ujian) ||

    $id_kelas <= 0 ||

    $id_paket <= 0 ||

    $durasi <= 0

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data yang dikirim tidak lengkap.";

    header("Location: edit.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Bobot
|--------------------------------------------------------------------------
*/

if (($bobot_pg + $bobot_uraian) != 100) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Total bobot penilaian harus 100%.";

    header("Location: edit.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Jenis Penilaian
|--------------------------------------------------------------------------
*/

if (

    $jenis_penilaian != "PG"

    &&

    $jenis_penilaian != "Campuran"

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Jenis penilaian tidak valid.";

    header("Location: edit.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Data Ujian
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    id_ujian

FROM ujian

WHERE

    id_ujian = ?

AND

    dihapus_pada IS NULL

LIMIT 1

");

$query->execute([$id_ujian]);

if ($query->rowCount() == 0) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    id_kelas,
    jenjang

FROM kelas

WHERE

    id_kelas = ?

AND

    dihapus_pada IS NULL

LIMIT 1

");

$query->execute([$id_kelas]);

$kelas = $query->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: edit.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Paket Soal
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    id_paket,
    jenjang

FROM paket_soal

WHERE

    id_paket = ?

AND

    dihapus_pada IS NULL

LIMIT 1

");

$query->execute([$id_paket]);

$paket = $query->fetch(PDO::FETCH_ASSOC);

if (!$paket) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Paket soal tidak ditemukan.";

    header("Location: edit.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Jenjang
|--------------------------------------------------------------------------
*/

if ($kelas['jenjang'] != $paket['jenjang']) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Jenjang kelas dan paket soal tidak sesuai.";

    header("Location: edit.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Duplikasi
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    COUNT(*) AS total

FROM ujian

WHERE

    id_tahun = ?

AND

    id_kelas = ?

AND

    nama_ujian = ?

AND

    id_ujian <> ?

AND

    dihapus_pada IS NULL

");

$query->execute([

    $id_tahun,
    $id_kelas,
    $nama_ujian,
    $id_ujian

]);

$cek = $query->fetch(PDO::FETCH_ASSOC);

if ($cek['total'] > 0) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Nama ujian sudah digunakan pada kelas tersebut.";

    header("Location: edit.php?id_ujian=".$id_ujian);
    exit;

}

/*
|--------------------------------------------------------------------------
| Update Data Ujian
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $query = $pdo->prepare("

    UPDATE ujian

    SET

        id_kelas = ?,
        id_paket = ?,
        nama_ujian = ?,
        durasi = ?,
        jenis_penilaian = ?,
        bobot_pg = ?,
        bobot_uraian = ?,
        acak_soal = ?,
        acak_pilihan = ?,
        fullscreen = ?,
        anti_cheat = ?,
        maksimal_pelanggaran = ?

    WHERE

        id_ujian = ?

    ");

    $query->execute([

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
        $id_ujian

    ]);

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] = "Data ujian berhasil diperbarui.";

    header("Location: index.php");

    exit;

} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    /*
    |--------------------------------------------------------------------------
    | Gagal
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Terjadi kesalahan: " . $e->getMessage();

    header("Location: edit.php?id_ujian=" . $id_ujian);

    exit;

}