<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    header("Location:index.php");
    exit;

}

$id = intval($_GET['id']);

/*
|--------------------------------------------------------------------------
| Cek Data
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    SELECT id

    FROM soal

    WHERE id = ?

");

$stmt->execute([$id]);

if ($stmt->rowCount() == 0) {

    header("Location:index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Hapus Data
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    DELETE FROM soal

    WHERE id = ?

");

$stmt->execute([$id]);

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location:index.php?hapus=success");

exit;

?>