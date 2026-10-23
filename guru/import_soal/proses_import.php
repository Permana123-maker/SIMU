<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| PROSES IMPORT SOAL
|--------------------------------------------------------------------------
| Menyimpan Paket Soal dan seluruh Soal ke Database
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../../app/keamanan/session.php';
require_once __DIR__ . '/../../app/database/database.php';
require_once __DIR__ . '/../../app/helper/kode_soal.php';


/*
|--------------------------------------------------------------------------
| VALIDASI REQUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['error'] = "Akses tidak diizinkan.";

    header("Location: index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI SESSION IMPORT
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['import_soal']) ||
    !isset($_SESSION['import_soal_info'])
) {

    $_SESSION['error'] = "Data import sudah tidak tersedia.";

    header("Location: index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA SESSION
|--------------------------------------------------------------------------
*/

$dataImport = $_SESSION['import_soal'];
$info       = $_SESSION['import_soal_info'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA FORM
|--------------------------------------------------------------------------
*/

$kodePaket  = trim($_POST['kode_paket'] ?? '');
$namaPaket  = trim($_POST['nama_paket'] ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');
$idTahun    = (int) ($_POST['id_tahun'] ?? 0);
$jenjang    = trim($_POST['jenjang'] ?? '');


/*
|--------------------------------------------------------------------------
| VALIDASI INPUT
|--------------------------------------------------------------------------
*/

if (
    $kodePaket === '' ||
    $namaPaket === '' ||
    $idTahun <= 0 ||
    $jenjang === ''
) {

    $_SESSION['error'] = "Data paket soal belum lengkap.";

    header("Location: preview.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI JENJANG
|--------------------------------------------------------------------------
*/

if (!in_array($jenjang, ['VII', 'VIII', 'IX'], true)) {

    $_SESSION['error'] = "Jenjang tidak valid.";

    header("Location: preview.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI DUPLIKAT KODE PAKET
|--------------------------------------------------------------------------
|
| Catatan:
| Struktur tabel yang digunakan saat ini tidak memiliki kode_paket.
| Karena itu pengecekan berdasarkan kode_paket tidak dilakukan di sini.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| MULAI TRANSACTION
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PAKET SOAL
    |--------------------------------------------------------------------------
    */

    $sql = "
        INSERT INTO paket_soal
        (
            id_tahun,
            jenjang,
            nama_paket,
            keterangan
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )
    ";

    $stmtPaket = $pdo->prepare($sql);

    $stmtPaket->execute([
        $idTahun,
        $jenjang,
        $namaPaket,
        $keterangan !== '' ? $keterangan : null
    ]);


    /*
    |--------------------------------------------------------------------------
    | AMBIL ID PAKET
    |--------------------------------------------------------------------------
    */

    $idPaket = $pdo->lastInsertId();

    if (!$idPaket) {

        throw new Exception(
            "Gagal membuat Paket Soal."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SQL SIMPAN SOAL
    |--------------------------------------------------------------------------
    */

    $sqlSoal = "
        INSERT INTO soal
        (
            id_paket,
            kode_soal,
            nomor,
            jenis,
            pertanyaan,
            gambar,
            pilihan_a,
            pilihan_b,
            pilihan_c,
            pilihan_d,
            pilihan_e,
            kunci_jawaban,
            jawaban_uraian,
            bobot
        )
        VALUES
        (
            :id_paket,
            :kode_soal,
            :nomor,
            :jenis,
            :pertanyaan,
            :gambar,
            :pilihan_a,
            :pilihan_b,
            :pilihan_c,
            :pilihan_d,
            :pilihan_e,
            :kunci_jawaban,
            :jawaban_uraian,
            :bobot
        )
    ";

    $stmtSoal = $pdo->prepare($sqlSoal);


    /*
    |--------------------------------------------------------------------------
    | HITUNG SOAL YANG BERHASIL DISIMPAN
    |--------------------------------------------------------------------------
    */

    $jumlahSoal = 0;


    /*
    |--------------------------------------------------------------------------
    | LOOP DATA IMPORT
    |--------------------------------------------------------------------------
    */

    foreach ($dataImport as $item) {

        /*
        |--------------------------------------------------------------------------
        | LEWATI DATA YANG TIDAK VALID
        |--------------------------------------------------------------------------
        */

        if (
            !isset($item['status']) ||
            $item['status'] !== 'VALID'
        ) {

            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI DATA SOAL
        |--------------------------------------------------------------------------
        */

        $nomor = (int) ($item['nomor'] ?? 0);

        $jenis = strtoupper(
            trim($item['jenis'] ?? '')
        );

        $pertanyaan = trim(
            $item['pertanyaan'] ?? ''
        );


        if ($nomor <= 0) {

            throw new Exception(
                "Nomor soal tidak valid."
            );
        }


        if (
            !in_array(
                $jenis,
                ['PG', 'ISIAN', 'URAIAN'],
                true
            )
        ) {

            throw new Exception(
                "Jenis soal nomor {$nomor} tidak valid."
            );
        }


        if ($pertanyaan === '') {

            throw new Exception(
                "Pertanyaan soal nomor {$nomor} kosong."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GENERATE KODE SOAL
        |--------------------------------------------------------------------------
        */

        $kodeSoal = generateKodeSoal($nomor);


        /*
        |--------------------------------------------------------------------------
        | DEFAULT NILAI
        |--------------------------------------------------------------------------
        */

        $kunciJawaban = null;

        $jawabanUraian = null;


        /*
        |--------------------------------------------------------------------------
        | SESUAIKAN JENIS SOAL
        |--------------------------------------------------------------------------
        */

        if ($jenis === 'PG') {

            $kunciJawaban = trim(
                $item['kunci'] ?? ''
            );

        } elseif ($jenis === 'ISIAN') {

            $kunciJawaban = trim(
                $item['kunci'] ?? ''
            );

        } elseif ($jenis === 'URAIAN') {

            $jawabanUraian = trim(
                $item['kunci'] ?? ''
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PILIHAN JAWABAN
        |--------------------------------------------------------------------------
        */

        $pilihanA = trim(
            $item['pilihan_a'] ?? ''
        );

        $pilihanB = trim(
            $item['pilihan_b'] ?? ''
        );

        $pilihanC = trim(
            $item['pilihan_c'] ?? ''
        );

        $pilihanD = trim(
            $item['pilihan_d'] ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | BOBOT
        |--------------------------------------------------------------------------
        */

        $bobot = $item['bobot'] ?? 0;

        if ($bobot === '' || $bobot === null) {

            $bobot = 0;
        }

        $bobot = (float) $bobot;


        /*
        |--------------------------------------------------------------------------
        | SIMPAN SOAL
        |--------------------------------------------------------------------------
        */

        $stmtSoal->execute([

            ':id_paket'       => $idPaket,

            ':kode_soal'      => $kodeSoal,

            ':nomor'          => $nomor,

            ':jenis'          => $jenis,

            ':pertanyaan'     => $pertanyaan,

            ':gambar'         => null,

            ':pilihan_a'      => $pilihanA !== ''
                ? $pilihanA
                : null,

            ':pilihan_b'      => $pilihanB !== ''
                ? $pilihanB
                : null,

            ':pilihan_c'      => $pilihanC !== ''
                ? $pilihanC
                : null,

            ':pilihan_d'      => $pilihanD !== ''
                ? $pilihanD
                : null,

            ':pilihan_e'      => null,

            ':kunci_jawaban'  => $kunciJawaban !== ''
                ? $kunciJawaban
                : null,

            ':jawaban_uraian' => $jawabanUraian !== ''
                ? $jawabanUraian
                : null,

            ':bobot'          => $bobot
        ]);


        $jumlahSoal++;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI JUMLAH SOAL
    |--------------------------------------------------------------------------
    */

    if ($jumlahSoal <= 0) {

        throw new Exception(
            "Tidak ada soal valid yang dapat diimport."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COMMIT TRANSACTION
    |--------------------------------------------------------------------------
    */

    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | HAPUS SESSION IMPORT
    |--------------------------------------------------------------------------
    */

    unset($_SESSION['import_soal']);

    unset($_SESSION['import_soal_info']);


    /*
    |--------------------------------------------------------------------------
    | PESAN BERHASIL
    |--------------------------------------------------------------------------
    */

    $_SESSION['pesan'] =
        "Paket soal berhasil dibuat dan {$jumlahSoal} soal berhasil diimport.";

    $_SESSION['warna'] = "success";


    /*
    |--------------------------------------------------------------------------
    | KEMBALI KE HALAMAN IMPORT
    |--------------------------------------------------------------------------
    */

    header("Location: index.php");

    exit;


} catch (Exception $e) {


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

    $_SESSION['error'] =
        "Terjadi kesalahan saat proses import. " .
        $e->getMessage();


    /*
    |--------------------------------------------------------------------------
    | KEMBALI KE PREVIEW
    |--------------------------------------------------------------------------
    */

    header("Location: preview.php");

    exit;
}