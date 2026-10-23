<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location:../ujian/index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$id_ujian = intval($_POST['id_ujian']);

$peserta = $_POST['peserta'] ?? [];

/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

if ($id_ujian <= 0) {

    header("Location:../ujian/index.php");
    exit;

}

try{

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Hapus Peserta Lama
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("

        DELETE FROM peserta_ujian

        WHERE id_ujian = ?

    ");

    $stmt->execute([$id_ujian]);

    /*
    |--------------------------------------------------------------------------
    | Simpan Peserta Baru
    |--------------------------------------------------------------------------
    */

    if(!empty($peserta)){

        $stmt = $pdo->prepare("

            INSERT INTO peserta_ujian
            (

                id_ujian,

                id_siswa

            )

            VALUES
            (

                ?,

                ?

            )

        ");

        foreach($peserta as $id_siswa){

            $stmt->execute([

                $id_ujian,

                intval($id_siswa)

            ]);

        }

    }

    $pdo->commit();

}catch(Exception $e){

    $pdo->rollBack();

    die($e->getMessage());

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location:index.php?id=".$id_ujian."&success=1");

exit;

?>