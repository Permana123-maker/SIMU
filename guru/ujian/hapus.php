<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Hapus Ujian
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

$id_ujian = isset($_GET['id_ujian'])
    ? (int) $_GET['id_ujian']
    : 0;

if ($id_ujian <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Data
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    id_ujian,
    nama_ujian

FROM ujian

WHERE

    id_ujian = ?

AND

    dihapus_pada IS NULL

LIMIT 1

");

$query->execute([$id_ujian]);

$ujian = $query->fetch(PDO::FETCH_ASSOC);

if (!$ujian) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Soft Delete Ujian
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $query = $pdo->prepare("

    UPDATE ujian

    SET

        dihapus_pada = NOW()

    WHERE

        id_ujian = ?

    ");

    $query->execute([$id_ujian]);

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] = "Ujian \"" . $ujian['nama_ujian'] . "\" berhasil dihapus.";

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

    $_SESSION['pesan'] = "Gagal menghapus data : " . $e->getMessage();

    header("Location: index.php");

    exit;

}

?>