<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Validasi Token Ujian
|--------------------------------------------------------------------------
*/

session_start();

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
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != "POST") {

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

    $_SESSION['pesan'] = "Belum ada Tahun Pelajaran aktif.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Data Session
|--------------------------------------------------------------------------
*/

$id_siswa = $_SESSION['siswa']['id_siswa'];

$id_kelas = $_SESSION['siswa']['id_kelas'];

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$id_ujian = isset($_POST['id_ujian'])
    ? (int) $_POST['id_ujian']
    : 0;

$token = strtoupper(trim($_POST['token'] ?? ""));

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if (

    $id_ujian <= 0

    ||

    empty($token)

) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Token ujian wajib diisi.";

    header("Location: token.php?id_ujian=" . $id_ujian);

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Ujian dan Token
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    u.id_ujian,
    u.status,

    t.token,
    t.status AS status_token

FROM ujian u

INNER JOIN token_ujian t
    ON u.id_ujian = t.id_ujian

WHERE

    u.id_ujian = ?

AND

    u.id_tahun = ?

AND

    u.id_kelas = ?

AND

    u.dihapus_pada IS NULL

LIMIT 1

");

$query->execute([

    $id_ujian,

    $tahun['id_tahun'],

    $id_kelas

]);

$data = $query->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if (!$data) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");

    exit;

}

if ($data['status'] != "Aktif") {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Ujian belum aktif.";

    header("Location: token.php?id_ujian=" . $id_ujian);

    exit;

}

if ($data['status_token'] != "Aktif") {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Token ujian belum diaktifkan oleh guru.";

    header("Location: token.php?id_ujian=" . $id_ujian);

    exit;

}

if (strtoupper($data['token']) !== $token) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Token ujian yang Anda masukkan salah.";

    header("Location: token.php?id_ujian=" . $id_ujian);

    exit;

}

/*
|--------------------------------------------------------------------------
| Buat Session Validasi Token
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['token_valid'])) {

    $_SESSION['token_valid'] = [];

}

$_SESSION['token_valid'][$id_ujian] = [

    'valid'      => true,

    'id_siswa'   => $id_siswa,

    'id_ujian'   => $id_ujian,

    'waktu'      => time()

];

/*
|--------------------------------------------------------------------------
| Redirect ke Halaman Konfirmasi
|--------------------------------------------------------------------------
*/

header("Location: konfirmasi.php?id_ujian=" . $id_ujian);

exit;