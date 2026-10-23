<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Hapus Tahun Pelajaran
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

$id_tahun = (int) ($_GET['id'] ?? 0);

if ($id_tahun <= 0) {

    $_SESSION['pesan'] = "Data Tahun Pelajaran tidak ditemukan.";

    $_SESSION['warna'] = "danger";

    header("Location: index.php");

    exit;

}

try {

    /*
    |--------------------------------------------------------------------------
    | Cek Data
    |--------------------------------------------------------------------------
    */

    $cek = $pdo->prepare("

        SELECT
            aktif

        FROM tahun_ajaran

        WHERE id_tahun = ?

        AND dihapus_pada IS NULL

        LIMIT 1

    ");

    $cek->execute([

        $id_tahun

    ]);

    $data = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$data) {

        $_SESSION['pesan'] = "Data Tahun Pelajaran tidak ditemukan.";

        $_SESSION['warna'] = "danger";

        header("Location: index.php");

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | Tahun Aktif Tidak Boleh Dihapus
    |--------------------------------------------------------------------------
    */

    if ($data['aktif'] === "Ya") {

        $_SESSION['pesan'] = "Tahun Pelajaran yang sedang aktif tidak dapat dihapus.";

        $_SESSION['warna'] = "warning";

        header("Location: index.php");

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | Soft Delete
    |--------------------------------------------------------------------------
    */

    $hapus = $pdo->prepare("

        UPDATE tahun_ajaran

        SET dihapus_pada = NOW()

        WHERE id_tahun = ?

        AND dihapus_pada IS NULL

    ");

    $hapus->execute([

        $id_tahun

    ]);

    $_SESSION['pesan'] = "Tahun Pelajaran berhasil dihapus.";

    $_SESSION['warna'] = "success";

} catch (PDOException $e) {

    $_SESSION['pesan'] = "Terjadi kesalahan saat menghapus data.";

    $_SESSION['warna'] = "danger";

}

header("Location: index.php");

exit;