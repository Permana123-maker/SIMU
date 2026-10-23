<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Ubah Status Ujian
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";

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
| Ambil Data
|--------------------------------------------------------------------------
*/

$id_ujian = (int) ($_GET['id_ujian'] ?? 0);

$status = $_GET['status'] ?? '';

$id_tahun = (int) $tahun['id_tahun'];

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

if ($id_ujian <= 0) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "ID ujian tidak valid.";

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Status
|--------------------------------------------------------------------------
*/

if ($status !== "Aktif" && $status !== "Nonaktif") {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Status ujian tidak valid.";

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Ujian
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

    SELECT

        id_ujian,
        nama_ujian,
        status

    FROM ujian

    WHERE

        id_ujian = ?

    AND

        id_tahun = ?

    AND

        dihapus_pada IS NULL

    LIMIT 1

");

$query->execute([

    $id_ujian,
    $id_tahun

]);

$ujian = $query->fetch(PDO::FETCH_ASSOC);

if (!$ujian) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Jika Status Sama
|--------------------------------------------------------------------------
*/

if ($ujian['status'] === $status) {

    $_SESSION['warna'] = "info";

    $_SESSION['pesan'] =
        "Status ujian \"" .
        $ujian['nama_ujian'] .
        "\" sudah " .
        $status .
        ".";

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ubah Status
|--------------------------------------------------------------------------
*/

try {

    $query = $pdo->prepare("

        UPDATE ujian

        SET

            status = ?

        WHERE

            id_ujian = ?

        AND

            id_tahun = ?

        AND

            dihapus_pada IS NULL

        LIMIT 1

    ");

    $query->execute([

        $status,
        $id_ujian,
        $id_tahun

    ]);

    /*
    |--------------------------------------------------------------------------
    | Pesan Berhasil
    |--------------------------------------------------------------------------
    */

    if ($status === "Aktif") {

        $_SESSION['warna'] = "success";

        $_SESSION['pesan'] =
            "Ujian \"" .
            $ujian['nama_ujian'] .
            "\" berhasil diaktifkan.";

    } else {

        $_SESSION['warna'] = "secondary";

        $_SESSION['pesan'] =
            "Ujian \"" .
            $ujian['nama_ujian'] .
            "\" berhasil dinonaktifkan.";

    }

    header("Location: index.php");
    exit;

} catch (Exception $e) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Terjadi kesalahan saat mengubah status ujian: " .
        $e->getMessage();

    header("Location: index.php");
    exit;

}