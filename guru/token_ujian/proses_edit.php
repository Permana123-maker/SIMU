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
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$id_token = isset($_POST['id_token'])
    ? (int) $_POST['id_token']
    : 0;

$status = trim($_POST['status'] ?? '');

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if (

    $id_token <= 0 ||
    $status == ""

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data token tidak lengkap.";

    header("Location: index.php");
    exit;

}

if (!in_array($status, ['Aktif', 'Nonaktif'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Status token tidak valid.";

    header("Location: edit.php?id_token=" . $id_token);
    exit;

}

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
    $_SESSION['pesan'] = "Data token tidak ditemukan.";

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Update Status Token
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $sql = "

    UPDATE token_ujian

    SET

        status = :status,

        diubah_pada = NOW()

    WHERE

        id_token = :id_token

    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':status',
        $status
    );

    $query->bindValue(
        ':id_token',
        $id_token,
        PDO::PARAM_INT
    );

    $query->execute();

    $pdo->commit();

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] = "Status token ujian berhasil diperbarui.";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Terjadi kesalahan saat memperbarui token.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;