<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Hapus Soal
|--------------------------------------------------------------------------
*/

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Soal
|--------------------------------------------------------------------------
*/

$id_soal = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id_soal <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "ID soal tidak valid.";

    header("Location: ../index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Soal
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    id_soal,
    id_paket,
    nomor

FROM soal

WHERE

    id_soal = :id_soal

AND

    dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_soal',
    $id_soal,
    PDO::PARAM_INT
);

$query->execute();

$soal = $query->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Validasi Soal
|--------------------------------------------------------------------------
*/

if (!$soal) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data soal tidak ditemukan.";

    header("Location: ../index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Data Pendukung
|--------------------------------------------------------------------------
*/

$id_paket = (int) $soal['id_paket'];

$nomor = (int) $soal['nomor'];

/*
|--------------------------------------------------------------------------
| Proses Hapus Soal
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | Mulai Transaction
    |--------------------------------------------------------------------------
    */

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Soft Delete
    |--------------------------------------------------------------------------
    */

    $sql = "

    UPDATE soal

    SET

        dihapus_pada = NOW()

    WHERE

        id_soal = :id_soal

    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':id_soal',
        $id_soal,
        PDO::PARAM_INT
    );

    $query->execute();

    /*
    |--------------------------------------------------------------------------
    | Rapikan Nomor Soal
    |--------------------------------------------------------------------------
    */

    $sql = "

    UPDATE soal

    SET

        nomor = nomor - 1

    WHERE

        id_paket = :id_paket

    AND

        nomor > :nomor

    AND

        dihapus_pada IS NULL

    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':id_paket',
        $id_paket,
        PDO::PARAM_INT
    );

    $query->bindValue(
        ':nomor',
        $nomor,
        PDO::PARAM_INT
    );

    $query->execute();

    /*
    |--------------------------------------------------------------------------
    | Perbarui Kode Soal
    |--------------------------------------------------------------------------
    */

    $sql = "

    SELECT

        id_soal,
        nomor

    FROM soal

    WHERE

        id_paket = :id_paket

    AND

        dihapus_pada IS NULL

    ORDER BY nomor ASC

    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':id_paket',
        $id_paket,
        PDO::PARAM_INT
    );

    $query->execute();

    $dataSoal = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dataSoal as $item) {

        $kode_soal = generateKodeSoal($item['nomor']);

        $update = $pdo->prepare("

            UPDATE soal

            SET

                kode_soal = :kode_soal

            WHERE

                id_soal = :id_soal

        ");

        $update->execute([

            ':kode_soal' => $kode_soal,

            ':id_soal'   => $item['id_soal']

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Lanjut Bagian 3
    |--------------------------------------------------------------------------
    */

        /*
    |--------------------------------------------------------------------------
    | Commit Transaction
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] = "Soal berhasil dihapus.";

    header("Location: index.php?id_paket=" . $id_paket);

    exit;

}

/*
|--------------------------------------------------------------------------
| Gagal
|--------------------------------------------------------------------------
*/

catch (PDOException $e) {

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
    | Pesan Error
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Terjadi kesalahan saat menghapus soal.";

    /*
    |--------------------------------------------------------------------------
    | Aktifkan saat proses debugging
    |--------------------------------------------------------------------------
    |
    | echo $e->getMessage();
    | exit;
    |
    */

    header("Location: index.php?id_paket=" . $id_paket);

    exit;

}