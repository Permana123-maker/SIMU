<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Aktifkan Tahun Pelajaran
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

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Pastikan Data Ada
    |--------------------------------------------------------------------------
    */

    $cek = $pdo->prepare("

        SELECT id_tahun

        FROM tahun_ajaran

        WHERE id_tahun = ?

        AND dihapus_pada IS NULL

        LIMIT 1

    ");

    $cek->execute([

        $id_tahun

    ]);

    if (!$cek->fetch()) {

        throw new Exception("Data tidak ditemukan.");

    }

    /*
    |--------------------------------------------------------------------------
    | Nonaktifkan Semua Tahun Pelajaran
    |--------------------------------------------------------------------------
    */

    $nonaktif = $pdo->prepare("

        UPDATE tahun_ajaran

        SET aktif = 'Tidak'

        WHERE dihapus_pada IS NULL

    ");

    $nonaktif->execute();

    /*
    |--------------------------------------------------------------------------
    | Aktifkan Tahun Pelajaran Terpilih
    |--------------------------------------------------------------------------
    */

    $aktifkan = $pdo->prepare("

        UPDATE tahun_ajaran

        SET aktif = 'Ya'

        WHERE id_tahun = ?

        AND dihapus_pada IS NULL

    ");

    $aktifkan->execute([

        $id_tahun

    ]);

    /*
    |--------------------------------------------------------------------------
    | Commit
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    $_SESSION['pesan'] = "Tahun Pelajaran berhasil diaktifkan.";

    $_SESSION['warna'] = "success";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['pesan'] = "Gagal mengaktifkan Tahun Pelajaran.";

    $_SESSION['warna'] = "danger";

}

header("Location: index.php");

exit;