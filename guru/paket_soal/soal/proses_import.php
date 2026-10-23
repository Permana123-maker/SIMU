<?php

/*
|--------------------------------------------------------------------------
| Session & Database
|--------------------------------------------------------------------------
*/

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";


/*
|--------------------------------------------------------------------------
| Composer Autoload
|--------------------------------------------------------------------------
*/

require_once "../../../vendor/autoload.php";


use PhpOffice\PhpSpreadsheet\IOFactory;


/*
|--------------------------------------------------------------------------
| Helper SIMU
|--------------------------------------------------------------------------
*/

require_once "../../../app/helper/nomor_soal.php";
require_once "../../../app/helper/kode_soal.php";



/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {


    header(
        "Location: index.php"
    );

    exit;

}



/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$id_paket = $_POST['id_paket'] ?? '';



/*
|--------------------------------------------------------------------------
| Validasi Paket Soal
|--------------------------------------------------------------------------
*/

if (empty($id_paket)) {


    $_SESSION['flash_error'] =

        "Paket soal belum dipilih";


    header(
        "Location: import.php"
    );

    exit;

}



/*
|--------------------------------------------------------------------------
| Validasi File Excel
|--------------------------------------------------------------------------
*/


if (!isset($_FILES['file_excel'])) {


    $_SESSION['flash_error'] =

        "File Excel belum dipilih";


    header(
        "Location: import.php"
    );

    exit;

}



$file = $_FILES['file_excel'];



if ($file['error'] !== UPLOAD_ERR_OK) {


    $_SESSION['flash_error'] =

        "File gagal diupload";


    header(
        "Location: import.php"
    );

    exit;

}



$ext = strtolower(
    pathinfo(
        $file['name'],
        PATHINFO_EXTENSION
    )
);



if (!in_array($ext, ['xlsx','xls'])) {


    $_SESSION['flash_error'] =

        "Format file harus Excel";


    header(
        "Location: import.php"
    );

    exit;

}

<?php

/*
|--------------------------------------------------------------------------
| Membaca File Excel
|--------------------------------------------------------------------------
*/


try {


    $spreadsheet = IOFactory::load(
        $file['tmp_name']
    );


    $sheet = $spreadsheet->getActiveSheet();


    $dataExcel = $sheet->toArray();



}

catch (Exception $e) {


    $_SESSION['flash_error'] =

        "File Excel tidak dapat dibaca";


    header(
        "Location: import.php"
    );

    exit;

}




/*
|--------------------------------------------------------------------------
| Proses Data Excel
|--------------------------------------------------------------------------
*/


$jumlahBerhasil = 0;


$jumlahGagal = 0;



/*
|--------------------------------------------------------------------------
| Lewati Header Baris Pertama
|--------------------------------------------------------------------------
*/


foreach ($dataExcel as $index => $row) {



    if ($index == 0) {


        continue;

    }





    /*
    |--------------------------------------------------------------------------
    | Ambil Data Kolom Excel
    |--------------------------------------------------------------------------
    */


    $jenis = trim(
        $row[1] ?? ''
    );


    $pertanyaan = trim(
        $row[2] ?? ''
    );


    $pilihan_a = trim(
        $row[3] ?? ''
    );


    $pilihan_b = trim(
        $row[4] ?? ''
    );


    $pilihan_c = trim(
        $row[5] ?? ''
    );


    $pilihan_d = trim(
        $row[6] ?? ''
    );


    $pilihan_e = trim(
        $row[7] ?? ''
    );


    $kunci_jawaban = trim(
        $row[8] ?? ''
    );


    $jawaban_uraian = trim(
        $row[9] ?? ''
    );


    $bobot = trim(
        $row[10] ?? 1
    );



    /*
    |--------------------------------------------------------------------------
    | Lewati Baris Kosong
    |--------------------------------------------------------------------------
    */


    if (empty($pertanyaan)) {


        continue;

    }

        /*
    |--------------------------------------------------------------------------
    | Validasi Jenis Soal
    |--------------------------------------------------------------------------
    */


    if (!in_array($jenis, ['PG','ISIAN','URAIAN'])) {


        $jumlahGagal++;

        continue;

    }




    /*
    |--------------------------------------------------------------------------
    | Generate Nomor & Kode Soal
    |--------------------------------------------------------------------------
    */


    $nomor = generateNomorSoal(
        $pdo,
        $id_paket
    );


    $kode_soal = generateKodeSoal(
        $nomor
    );




    /*
    |--------------------------------------------------------------------------
    | Penyesuaian Data
    |--------------------------------------------------------------------------
    */


    if ($jenis == "PG") {


        $jawaban_uraian = null;


    }


    elseif ($jenis == "ISIAN") {


        $pilihan_a = null;
        $pilihan_b = null;
        $pilihan_c = null;
        $pilihan_d = null;
        $pilihan_e = null;


    }


    elseif ($jenis == "URAIAN") {


        $pilihan_a = null;
        $pilihan_b = null;
        $pilihan_c = null;
        $pilihan_d = null;
        $pilihan_e = null;


        $kunci_jawaban = null;


    }




    /*
    |--------------------------------------------------------------------------
    | Simpan Soal
    |--------------------------------------------------------------------------
    */


    try {


        $stmt = $pdo->prepare("

            INSERT INTO soal (

                id_paket,

                kode_soal,

                nomor,

                jenis,

                pertanyaan,


                pilihan_a,

                pilihan_b,

                pilihan_c,

                pilihan_d,

                pilihan_e,


                kunci_jawaban,

                jawaban_uraian,

                bobot


            )


            VALUES (


                :id_paket,

                :kode_soal,

                :nomor,

                :jenis,

                :pertanyaan,


                :pilihan_a,

                :pilihan_b,

                :pilihan_c,

                :pilihan_d,

                :pilihan_e,


                :kunci_jawaban,

                :jawaban_uraian,

                :bobot


            )


        ");



        $stmt->execute([


            ':id_paket' =>

                $id_paket,


            ':kode_soal' =>

                $kode_soal,


            ':nomor' =>

                $nomor,


            ':jenis' =>

                $jenis,


            ':pertanyaan' =>

                $pertanyaan,


            ':pilihan_a' =>

                $pilihan_a,


            ':pilihan_b' =>

                $pilihan_b,


            ':pilihan_c' =>

                $pilihan_c,


            ':pilihan_d' =>

                $pilihan_d,


            ':pilihan_e' =>

                $pilihan_e,


            ':kunci_jawaban' =>

                $kunci_jawaban,


            ':jawaban_uraian' =>

                $jawaban_uraian,


            ':bobot' =>

                $bobot


        ]);



        $jumlahBerhasil++;


    }


    catch(PDOException $e) {


        $jumlahGagal++;


    }


}

<?php

/*
|--------------------------------------------------------------------------
| Hasil Import
|--------------------------------------------------------------------------
*/


if ($jumlahBerhasil > 0) {


    $_SESSION['flash_success'] =

        "Import berhasil. "

        . $jumlahBerhasil

        . " soal berhasil ditambahkan."

        . ($jumlahGagal > 0

            ? " "

            . $jumlahGagal

            . " soal gagal."

            : "");

}



else {


    $_SESSION['flash_error'] =

        "Tidak ada soal yang berhasil diimport.";


}



/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/


header(

    "Location: index.php"

);


exit;