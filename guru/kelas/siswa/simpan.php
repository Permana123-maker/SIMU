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

$id_tahun       = (int) $_POST['id_tahun'];
$id_kelas       = (int) $_POST['id_kelas'];
$nis            = trim($_POST['nis']);
$nama           = strtoupper(trim($_POST['nama']));
$jenis_kelamin  = $_POST['jenis_kelamin'];
$status         = $_POST['status'];
$username       = trim($_POST['username']);
$password       = $_POST['password'];

/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

if (

    $id_tahun <= 0 ||

    $id_kelas <= 0 ||

    empty($nis) ||

    empty($nama) ||

    empty($jenis_kelamin) ||

    empty($status) ||

    empty($username) ||

    empty($password)

) {

    $_SESSION['pesan'] = "Semua data wajib diisi.";
    $_SESSION['warna'] = "danger";

    header("Location:tambah.php");
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

AND dihapus_pada IS NULL

");

$cek->execute([$nis]);

if ($cek->fetchColumn() > 0) {

    $_SESSION['pesan'] = "NIS sudah digunakan.";
    $_SESSION['warna'] = "warning";

    header("Location:tambah.php");
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

AND dihapus_pada IS NULL

");

$cek->execute([$username]);

if ($cek->fetchColumn() > 0) {

    $_SESSION['pesan'] = "Username sudah digunakan.";
    $_SESSION['warna'] = "warning";

    header("Location:tambah.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Enkripsi Password
|--------------------------------------------------------------------------
*/

$password = password_hash($password, PASSWORD_DEFAULT);

/*
|--------------------------------------------------------------------------
| Simpan Data
|--------------------------------------------------------------------------
*/

$simpan = $pdo->prepare("

INSERT INTO siswa (

id_tahun,

id_kelas,

nis,

nama,

jenis_kelamin,

username,

password,

status,

dibuat_pada

)

VALUES (

?,?,?,?,?,?,?,?,NOW()

)

");

$berhasil = $simpan->execute([

$id_tahun,

$id_kelas,

$nis,

$nama,

$jenis_kelamin,

$username,

$password,

$status

]);

if ($berhasil) {

    $_SESSION['pesan'] = "Data siswa berhasil disimpan.";
    $_SESSION['warna'] = "success";

} else {

    $_SESSION['pesan'] = "Data siswa gagal disimpan.";
    $_SESSION['warna'] = "danger";

}

header("Location:index.php");
exit;