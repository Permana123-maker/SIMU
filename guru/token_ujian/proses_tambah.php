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

$id_ujian = isset($_POST['id_ujian'])
    ? (int) $_POST['id_ujian']
    : 0;

$status = trim($_POST['status'] ?? 'Nonaktif');

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if ($id_ujian <= 0) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Ujian belum dipilih.";

    header("Location: tambah.php");
    exit;

}

if (!in_array($status, ['Aktif', 'Nonaktif'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Status token tidak valid.";

    header("Location: tambah.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Ujian
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

id_ujian

FROM ujian

WHERE

id_ujian = :id_ujian

AND dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_ujian',
    $id_ujian,
    PDO::PARAM_INT
);

$query->execute();

if (!$query->fetch(PDO::FETCH_ASSOC)) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: tambah.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Apakah Ujian Sudah Memiliki Token
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

id_token

FROM token_ujian

WHERE

id_ujian = :id_ujian

AND dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_ujian',
    $id_ujian,
    PDO::PARAM_INT
);

$query->execute();

if ($query->fetch(PDO::FETCH_ASSOC)) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Ujian tersebut sudah memiliki token.";

    header("Location: tambah.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Generate Token Unik
|--------------------------------------------------------------------------
*/

do {

    $token = strtoupper(substr(str_shuffle(

        'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'

    ), 0, 6));

    $sql = "

    SELECT

        id_token

    FROM token_ujian

    WHERE

        token = :token

    LIMIT 1

    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':token',
        $token
    );

    $query->execute();

} while ($query->fetch(PDO::FETCH_ASSOC));

/*
|--------------------------------------------------------------------------
| Simpan Data
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $sql = "

    INSERT INTO token_ujian
    (
        id_ujian,
        token,
        status
    )
    VALUES
    (
        :id_ujian,
        :token,
        :status
    )

    ";

    $query = $pdo->prepare($sql);

    $query->bindValue(
        ':id_ujian',
        $id_ujian,
        PDO::PARAM_INT
    );

    $query->bindValue(
        ':token',
        $token
    );

    $query->bindValue(
        ':status',
        $status
    );

    $query->execute();

    $pdo->commit();

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] = "Token ujian berhasil dibuat.";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Terjadi kesalahan saat menyimpan token.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php");

exit;