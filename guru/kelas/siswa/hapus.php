<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi Parameter
|--------------------------------------------------------------------------
*/

if (

    !isset($_GET['id_siswa']) ||
    !is_numeric($_GET['id_siswa']) ||

    !isset($_GET['id_kelas']) ||
    !is_numeric($_GET['id_kelas'])

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data siswa tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

$id_siswa = (int) $_GET['id_siswa'];
$id_kelas = (int) $_GET['id_kelas'];

/*
|--------------------------------------------------------------------------
| Cek Data Siswa
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_siswa
    FROM siswa
    WHERE
        id_siswa = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_siswa
]);

if (!$query->fetch()) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Data siswa tidak ditemukan.";

    header("Location: index.php?id_kelas=".$id_kelas);
    exit;
}

/*
|--------------------------------------------------------------------------
| Soft Delete
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $query = $pdo->prepare("
        UPDATE siswa
        SET
            dihapus_pada = NOW()
        WHERE
            id_siswa = ?
    ");

    $query->execute([
        $id_siswa
    ]);

    $pdo->commit();

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] = "Data siswa berhasil dihapus.";

} catch (Exception $e) {

    $pdo->rollBack();

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Terjadi kesalahan saat menghapus data.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php?id_kelas=".$id_kelas);
exit;