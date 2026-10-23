<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location:tambah.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$nama_ujian   = trim($_POST['nama_ujian']);

$id_mapel     = intval($_POST['id_mapel']);

$tanggal      = $_POST['tanggal'];

$jam_mulai    = $_POST['jam_mulai'];

$durasi       = intval($_POST['durasi']);

$jumlah_soal  = intval($_POST['jumlah_soal']);

$status       = $_POST['status'];

/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

if (

    empty($nama_ujian) ||

    empty($id_mapel) ||

    empty($tanggal) ||

    empty($jam_mulai) ||

    empty($durasi) ||

    empty($jumlah_soal)

){

    header("Location:tambah.php?error=1");

    exit;

}

/*
|--------------------------------------------------------------------------
| Simpan Database
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

INSERT INTO ujian
(

    nama_ujian,

    id_mapel,

    tanggal,

    jam_mulai,

    durasi,

    jumlah_soal,

    status

)

VALUES
(

    ?,

    ?,

    ?,

    ?,

    ?,

    ?,

    ?

)

");

$stmt->execute([

    $nama_ujian,

    $id_mapel,

    $tanggal,

    $jam_mulai,

    $durasi,

    $jumlah_soal,

    $status

]);

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location:index.php?simpan=success");

exit;

?>