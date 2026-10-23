<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$id_tahun  = isset($_POST['id_tahun']) ? (int) $_POST['id_tahun'] : 0;
$nama_kelas = strtoupper(trim($_POST['nama_kelas']));

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if ($id_tahun <= 0 || empty($nama_kelas)) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data yang dikirim tidak lengkap.";

    header("Location: tambah.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi Format Nama Kelas
|--------------------------------------------------------------------------
|
| Format yang diperbolehkan:
| VII 1
| VII 2
| VIII 5
| IX 3
|
*/

if (!preg_match('/^(VII|VIII|IX)\s+[1-9][0-9]*$/', $nama_kelas)) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Format nama kelas tidak valid. Contoh: VII 1 atau VIII 5.";

    header("Location: tambah.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Duplikasi
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT COUNT(*) 
    FROM kelas
    WHERE
        id_tahun = ?
        AND nama_kelas = ?
        AND dihapus_pada IS NULL
");

$query->execute([
    $id_tahun,
    $nama_kelas
]);

if ($query->fetchColumn() > 0) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Nama kelas sudah terdaftar pada tahun pelajaran ini.";

    header("Location: tambah.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Simpan Data
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    INSERT INTO kelas
    (
        id_tahun,
        nama_kelas
    )
    VALUES
    (
        ?,
        ?
    )
");

$berhasil = $query->execute([
    $id_tahun,
    $nama_kelas
]);

/*
|--------------------------------------------------------------------------
| Hasil
|--------------------------------------------------------------------------
*/

if ($berhasil) {

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] = "Data kelas berhasil ditambahkan.";

} else {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas gagal ditambahkan.";

}

header("Location: index.php");
exit;