<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location:index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$id_siswa       = (int) $_POST['id_siswa'];
$id_tahun       = (int) $_POST['id_tahun'];
$id_kelas       = (int) $_POST['id_kelas'];
$nis            = trim($_POST['nis']);
$nama           = strtoupper(trim($_POST['nama']));
$jenis_kelamin  = $_POST['jenis_kelamin'];
$status         = $_POST['status'];
$username       = trim($_POST['username']);
$password       = trim($_POST['password']);

/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

if (

    $id_siswa <= 0 ||

    $id_tahun <= 0 ||

    $id_kelas <= 0 ||

    empty($nis) ||

    empty($nama) ||

    empty($jenis_kelamin) ||

    empty($status) ||

    empty($username)

) {

    $_SESSION['pesan'] = "Semua data wajib diisi.";
    $_SESSION['warna'] = "danger";

    header("Location:edit.php?id=".$id_siswa);
    exit;

}

/*
|--------------------------------------------------------------------------
| Cek NIS
|--------------------------------------------------------------------------
*/

$cek = $pdo->prepare("

SELECT COUNT(*)

FROM siswa

WHERE

nis = ?

AND id_siswa <> ?

AND dihapus_pada IS NULL

");

$cek->execute([

$nis,

$id_siswa

]);

if($cek->fetchColumn()>0){

$_SESSION['pesan']="NIS sudah digunakan.";
$_SESSION['warna']="warning";

header("Location:edit.php?id=".$id_siswa);
exit;

}

/*
|--------------------------------------------------------------------------
| Cek Username
|--------------------------------------------------------------------------
*/

$cek = $pdo->prepare("

SELECT COUNT(*)

FROM siswa

WHERE

username = ?

AND id_siswa <> ?

AND dihapus_pada IS NULL

");

$cek->execute([

$username,

$id_siswa

]);

if($cek->fetchColumn()>0){

$_SESSION['pesan']="Username sudah digunakan.";
$_SESSION['warna']="warning";

header("Location:edit.php?id=".$id_siswa);
exit;

}

/*
|--------------------------------------------------------------------------
| Update Data
|--------------------------------------------------------------------------
*/

if($password==""){

$update = $pdo->prepare("

UPDATE siswa

SET

id_kelas=?,

nis=?,

nama=?,

jenis_kelamin=?,

username=?,

status=?,

diubah_pada=NOW()

WHERE id_siswa=?

");

$berhasil = $update->execute([

$id_kelas,

$nis,

$nama,

$jenis_kelamin,

$username,

$status,

$id_siswa

]);

}else{

$password_hash=password_hash($password,PASSWORD_DEFAULT);

$update = $pdo->prepare("

UPDATE siswa

SET

id_kelas=?,

nis=?,

nama=?,

jenis_kelamin=?,

username=?,

password=?,

status=?,

diubah_pada=NOW()

WHERE id_siswa=?

");

$berhasil = $update->execute([

$id_kelas,

$nis,

$nama,

$jenis_kelamin,

$username,

$password_hash,

$status,

$id_siswa

]);

}

if($berhasil){

$_SESSION['pesan']="Data siswa berhasil diperbarui.";
$_SESSION['warna']="success";

}else{

$_SESSION['pesan']="Data siswa gagal diperbarui.";
$_SESSION['warna']="danger";

}

header("Location:index.php");
exit;