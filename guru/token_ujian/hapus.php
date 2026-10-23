<?php

/*
|--------------------------------------------------------------------------
| Session & Database
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Token
|--------------------------------------------------------------------------
*/

if (

    !isset($_GET['id_token']) ||
    !is_numeric($_GET['id_token'])

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data token ujian tidak ditemukan.";

    header("Location: index.php");
    exit;

}

$id_token = (int) $_GET['id_token'];

/*
|--------------------------------------------------------------------------
| Cek Data Token
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

id_token

FROM token_ujian

WHERE

id_token = :id_token

AND dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_token',
    $id_token,
    PDO::PARAM_INT
);

$query->execute();

if (!$query->fetch(PDO::FETCH_ASSOC)) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Data token ujian tidak ditemukan.";

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Soft Delete
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $sql = "

    UPDATE token_ujian

    SET

        dihapus_pada = NOW(),
        diubah_pada = NOW()

    WHERE

        id_token = :id_token

    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':id_token',
        $id_token,
        PDO::PARAM_INT
    );

    $query->execute();

    $pdo->commit();

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] = "Token ujian berhasil dihapus.";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Terjadi kesalahan saat menghapus token.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;