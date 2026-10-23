<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    header("Location:index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi File
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['file'])) {

    die("File tidak ditemukan.");

}

$file = $_FILES['file'];

if ($file['error'] != 0) {

    die("Upload file gagal.");

}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, ['xls','xlsx'])) {

    die("Format file harus Excel.");

}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$id_mapel = intval($_POST['mapel']);

$tmpFile = $file['tmp_name'];

$spreadsheet = IOFactory::load($tmpFile);

$sheet = $spreadsheet->getActiveSheet();

$rows = $sheet->toArray();

/*
|--------------------------------------------------------------------------
| Import Database
|--------------------------------------------------------------------------
*/

$berhasil = 0;
$gagal = 0;

foreach ($rows as $index => $row) {

    if ($index == 0) {

        continue;

    }

    $pertanyaan = trim($row[0]);
    $a = trim($row[1]);
    $b = trim($row[2]);
    $c = trim($row[3]);
    $d = trim($row[4]);
    $e = trim($row[5]);
    $jawaban = strtoupper(trim($row[6]));

    if ($pertanyaan == "") {

        continue;

    }

    try{

        $stmt = $pdo->prepare("

            INSERT INTO soal
            (

                id_mapel,
                pertanyaan,
                pilihan_a,
                pilihan_b,
                pilihan_c,
                pilihan_d,
                pilihan_e,
                jawaban

            )

            VALUES
            (

                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?

            )

        ");

        $stmt->execute([

            $id_mapel,
            $pertanyaan,
            $a,
            $b,
            $c,
            $d,
            $e,
            $jawaban

        ]);

        $berhasil++;

    }catch(Exception $e){

        $gagal++;

    }

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location:index.php?berhasil=".$berhasil."&gagal=".$gagal);

exit;