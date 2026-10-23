<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Hapus Paket Soal
|--------------------------------------------------------------------------
| Soft Delete Paket Soal beserta Soal di dalamnya
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";


/*
|--------------------------------------------------------------------------
| VALIDASI ID PAKET
|--------------------------------------------------------------------------
*/

$id_paket = isset($_GET['id_paket'])
    ? (int) $_GET['id_paket']
    : 0;

if ($id_paket <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data paket soal tidak valid.";

    header("Location: index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| CEK PAKET
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id_paket,
        nama_paket
    FROM paket_soal
    WHERE
        id_paket = :id_paket
    AND
        dihapus_pada IS NULL
    LIMIT 1
";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_paket',
    $id_paket,
    PDO::PARAM_INT
);

$query->execute();

$paket = $query->fetch(PDO::FETCH_ASSOC);


if (!$paket) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Paket soal tidak ditemukan.";

    header("Location: index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| MULAI TRANSACTION
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | SOFT DELETE SELURUH SOAL DALAM PAKET
    |--------------------------------------------------------------------------
    */

    $sql = "
        UPDATE soal
        SET
            dihapus_pada = NOW(),
            diubah_pada = NOW()
        WHERE
            id_paket = :id_paket
        AND
            dihapus_pada IS NULL
    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':id_paket',
        $id_paket,
        PDO::PARAM_INT
    );

    $query->execute();


    /*
    |--------------------------------------------------------------------------
    | SOFT DELETE PAKET SOAL
    |--------------------------------------------------------------------------
    */

    $sql = "
        UPDATE paket_soal
        SET
            dihapus_pada = NOW(),
            diubah_pada = NOW()
        WHERE
            id_paket = :id_paket
        AND
            dihapus_pada IS NULL
    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':id_paket',
        $id_paket,
        PDO::PARAM_INT
    );

    $query->execute();


    /*
    |--------------------------------------------------------------------------
    | COMMIT
    |--------------------------------------------------------------------------
    */

    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | PESAN BERHASIL
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] =
        "Paket soal dan seluruh soalnya berhasil dihapus.";


    /*
    |--------------------------------------------------------------------------
    | KEMBALI KE HALAMAN PAKET SOAL
    |--------------------------------------------------------------------------
    */

    header("Location: index.php");

    exit;


} catch (PDOException $e) {


    /*
    |--------------------------------------------------------------------------
    | ROLLBACK
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();
    }


    /*
    |--------------------------------------------------------------------------
    | PESAN ERROR
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Terjadi kesalahan saat menghapus paket soal.";


    /*
    |--------------------------------------------------------------------------
    | KEMBALI KE HALAMAN PAKET SOAL
    |--------------------------------------------------------------------------
    */

    header("Location: index.php");

    exit;
}