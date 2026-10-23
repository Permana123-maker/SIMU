<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: index.php");
    exit;
}

$id_kelas = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| Pastikan Data Masih Ada
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_kelas,
        nama_kelas
    FROM kelas
    WHERE
        id_kelas = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([$id_kelas]);

$kelas = $query->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Apakah Masih Ada Siswa
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT COUNT(*)
    FROM siswa
    WHERE
        id_kelas = ?
        AND dihapus_pada IS NULL
");

$query->execute([$id_kelas]);

$jumlahSiswa = $query->fetchColumn();

if ($jumlahSiswa > 0) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Kelas tidak dapat dihapus karena masih memiliki {$jumlahSiswa} siswa.";

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Soft Delete
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    UPDATE kelas
    SET
        dihapus_pada = NOW()
    WHERE
        id_kelas = ?
");

$berhasil = $query->execute([$id_kelas]);

/*
|--------------------------------------------------------------------------
| Hasil
|--------------------------------------------------------------------------
*/

if ($berhasil) {

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] = "Data kelas berhasil dihapus.";

} else {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas gagal dihapus.";

}

header("Location: index.php");
exit;