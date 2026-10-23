<?php

/*
|--------------------------------------------------------------------------
| SIMU (Sistem Informasi Manajemen Ujian)
|--------------------------------------------------------------------------
| AJAX Simpan Jawaban
|--------------------------------------------------------------------------
*/

session_start();

/*
|--------------------------------------------------------------------------
| Load System
|--------------------------------------------------------------------------
*/

require_once "../../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Response JSON
|--------------------------------------------------------------------------
*/

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| Validasi Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['siswa'])) {

    echo json_encode([

        'status' => false,

        'pesan' => 'Sesi login telah berakhir.'

    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| Hanya Menerima POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([

        'status' => false,

        'pesan' => 'Metode request tidak diizinkan.'

    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Login
|--------------------------------------------------------------------------
*/

$id_siswa = (int) $_SESSION['siswa']['id_siswa'];

/*
|--------------------------------------------------------------------------
| Ambil Data POST
|--------------------------------------------------------------------------
*/

$id_sesi = isset($_POST['id_sesi'])

    ? (int) $_POST['id_sesi']

    : 0;

$id_soal = isset($_POST['id_soal'])

    ? (int) $_POST['id_soal']

    : 0;

$jawaban = strtoupper(trim($_POST['jawaban'] ?? ''));

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if (

    $id_sesi <= 0 ||

    $id_soal <= 0

) {

    echo json_encode([

        'status' => false,

        'pesan' => 'Data tidak lengkap.'

    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Jawaban
|--------------------------------------------------------------------------
*/

if (

    !in_array(

        $jawaban,

        [

            'A',

            'B',

            'C',

            'D',

            ''

        ]

    )

) {

    echo json_encode([

        'status' => false,

        'pesan' => 'Jawaban tidak valid.'

    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Kepemilikan Sesi
|--------------------------------------------------------------------------
*/

$querySesi = $pdo->prepare("

SELECT

    id_sesi

FROM sesi_ujian

WHERE

    id_sesi = ?

AND

    id_siswa = ?

AND

    status = 'Sedang Mengerjakan'

LIMIT 1

");

$querySesi->execute([

    $id_sesi,

    $id_siswa

]);

if (!$querySesi->fetch(PDO::FETCH_ASSOC)) {

    echo json_encode([

        'status' => false,

        'pesan' => 'Sesi ujian tidak valid.'

    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Jawaban Sebelumnya
|--------------------------------------------------------------------------
*/

$queryJawaban = $pdo->prepare("

SELECT

    id_jawaban

FROM jawaban_siswa

WHERE

    id_sesi = ?

AND

    id_soal = ?

LIMIT 1

");

$queryJawaban->execute([

    $id_sesi,

    $id_soal

]);

$dataJawaban = $queryJawaban->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Simpan Jawaban
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    if ($dataJawaban) {

        $queryUpdate = $pdo->prepare("

UPDATE jawaban_siswa

SET

    jawaban = ?,

    diubah_pada = NOW()

WHERE

    id_jawaban = ?

");

        $queryUpdate->execute([

            $jawaban,

            $dataJawaban['id_jawaban']

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | INSERT
    |--------------------------------------------------------------------------
    */

    else {

        $queryInsert = $pdo->prepare("

INSERT INTO jawaban_siswa
(

    id_sesi,

    id_soal,

    jawaban,

    dibuat_pada,

    diubah_pada

)

VALUES
(

    ?,

    ?,

    ?,

    NOW(),

    NOW()

)

");

        $queryInsert->execute([

            $id_sesi,

            $id_soal,

            $jawaban

        ]);

    }

    $pdo->commit();

}

catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([

        'status' => false,

        'pesan' => 'Gagal menyimpan jawaban.',

        'error' => $e->getMessage()

    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| Response Berhasil
|--------------------------------------------------------------------------
*/

echo json_encode([

    'status' => true,

    'pesan' => 'Jawaban berhasil disimpan.',

    'data' => [

        'id_sesi' => $id_sesi,

        'id_soal' => $id_soal,

        'jawaban' => $jawaban

    ]

]);

exit;