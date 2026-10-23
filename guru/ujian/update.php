<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location:index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$id            = intval($_POST['id']);

$nama_ujian    = trim($_POST['nama_ujian']);

$id_mapel      = intval($_POST['id_mapel']);

$tanggal       = $_POST['tanggal'];

$jam_mulai     = $_POST['jam_mulai'];

$durasi        = intval($_POST['durasi']);

$jumlah_soal   = intval($_POST['jumlah_soal']);

$status        = $_POST['status'];

/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

if (

    empty($id) ||

    empty($nama_ujian) ||

    empty($id_mapel) ||

    empty($tanggal) ||

    empty($jam_mulai) ||

    empty($durasi) ||

    empty($jumlah_soal)

){

    header("Location:edit.php?id=".$id);

    exit;

}

/*
|--------------------------------------------------------------------------
| Update Database
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

UPDATE ujian SET

    nama_ujian   = ?,

    id_mapel     = ?,

    tanggal      = ?,

    jam_mulai    = ?,

    durasi       = ?,

    jumlah_soal  = ?,

    status       = ?

WHERE id = ?

");

$stmt->execute([

    $nama_ujian,

    $id_mapel,

    $tanggal,

    $jam_mulai,

    $durasi,

    $jumlah_soal,

    $status,

    $id

]);

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location:index.php?update=success");

exit;

?>