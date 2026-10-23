<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Mulai Ujian Siswa
|--------------------------------------------------------------------------
*/

session_start();

require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";


/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['siswa'])) {

    header("Location: ../login/login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| REQUEST HARUS POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| TAHUN PELAJARAN AKTIF
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada Tahun Pelajaran aktif.";

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| DATA SISWA
|--------------------------------------------------------------------------
*/

$id_siswa = (int) $_SESSION['siswa']['id_siswa'];
$id_kelas = (int) $_SESSION['siswa']['id_kelas'];


/*
|--------------------------------------------------------------------------
| DATA FORM
|--------------------------------------------------------------------------
*/

$id_ujian = isset($_POST['id_ujian'])
    ? (int) $_POST['id_ujian']
    : 0;

$persetujuan = isset($_POST['persetujuan']);


/*
|--------------------------------------------------------------------------
| VALIDASI FORM
|--------------------------------------------------------------------------
*/

if ($id_ujian <= 0 || !$persetujuan) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Anda harus menyetujui tata tertib ujian.";

    header("Location: konfirmasi.php?id_ujian=" . $id_ujian);
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI TOKEN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['token_valid'][$id_ujian])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Silakan lakukan validasi token kembali.";

    header("Location: token.php?id_ujian=" . $id_ujian);
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI PEMILIK TOKEN
|--------------------------------------------------------------------------
*/

if (
    $_SESSION['token_valid'][$id_ujian]['id_siswa']
    !=
    $id_siswa
) {

    unset($_SESSION['token_valid'][$id_ujian]);

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Validasi siswa tidak sesuai.";

    header("Location: ../login/login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI TOKEN KEDALUWARSA
|--------------------------------------------------------------------------
*/

if (
    (time() - $_SESSION['token_valid'][$id_ujian]['waktu'])
    > 300
) {

    unset($_SESSION['token_valid'][$id_ujian]);

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Token telah kedaluwarsa.";

    header("Location: token.php?id_ujian=" . $id_ujian);
    exit;

}


/*
|--------------------------------------------------------------------------
| AMBIL DATA UJIAN
|--------------------------------------------------------------------------
|
| Tidak menggunakan status Aktif/Tidak Aktif.
|
*/

$query = $pdo->prepare("

    SELECT

        id_ujian,
        id_paket,
        durasi,
        jenis_penilaian,

        acak_soal,
        acak_pilihan,

        fullscreen,
        anti_cheat,
        maksimal_pelanggaran

    FROM ujian

    WHERE

        id_ujian = ?

        AND id_tahun = ?

        AND id_kelas = ?

        AND dihapus_pada IS NULL

    LIMIT 1

");

$query->execute([

    $id_ujian,
    $tahun['id_tahun'],
    $id_kelas

]);

$ujian = $query->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| UJIAN TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if (!$ujian) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Ujian tidak ditemukan.";

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDASI PAKET SOAL
|--------------------------------------------------------------------------
*/

if (empty($ujian['id_paket'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Paket soal untuk ujian ini belum ditentukan.";

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| CEK JUMLAH SOAL
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

    SELECT COUNT(*)

    FROM soal

    WHERE

        id_paket = ?

        AND dihapus_pada IS NULL

");

$query->execute([

    $ujian['id_paket']

]);

$jumlahSoal = (int) $query->fetchColumn();


/*
|--------------------------------------------------------------------------
| SOAL BELUM TERSEDIA
|--------------------------------------------------------------------------
*/

if ($jumlahSoal <= 0) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Soal untuk ujian ini belum tersedia.";

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| CEK SESI UJIAN YANG SUDAH ADA
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

    SELECT

        id_sesi,
        status,
        waktu_mulai,
        waktu_selesai

    FROM sesi_ujian

    WHERE

        id_ujian = ?

        AND id_siswa = ?

    LIMIT 1

");

$query->execute([

    $id_ujian,
    $id_siswa

]);

$sesi = $query->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| JIKA SUDAH PERNAH MEMULAI
|--------------------------------------------------------------------------
*/

if ($sesi) {


    /*
    |--------------------------------------------------------------------------
    | SUDAH SELESAI
    |--------------------------------------------------------------------------
    */

    if ($sesi['status'] == 'Selesai') {

        unset($_SESSION['token_valid'][$id_ujian]);

        $_SESSION['warna'] = "warning";
        $_SESSION['pesan'] = "Anda sudah menyelesaikan ujian ini.";

        header("Location: ../dashboard/index.php");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | MASIH MENGERJAKAN
    |--------------------------------------------------------------------------
    */

    if ($sesi['status'] == 'Sedang Mengerjakan') {

        unset($_SESSION['token_valid'][$id_ujian]);

        header(
            "Location: soal/index.php?id_ujian=" . $id_ujian
        );

        exit;

    }

}


/*
|--------------------------------------------------------------------------
| MULAI TRANSACTION
|--------------------------------------------------------------------------
*/

$pdo->beginTransaction();


try {


    /*
    |--------------------------------------------------------------------------
    | WAKTU SERVER
    |--------------------------------------------------------------------------
    */

    $waktu_mulai = date('Y-m-d H:i:s');

    $waktu_selesai = date(
        'Y-m-d H:i:s',
        strtotime(
            $waktu_mulai .
            " +" .
            (int) $ujian['durasi'] .
            " minutes"
        )
    );


    /*
    |--------------------------------------------------------------------------
    | INFORMASI PERANGKAT
    |--------------------------------------------------------------------------
    */

    $alamat_ip = $_SERVER['REMOTE_ADDR'] ?? '-';

    $browser = $_SERVER['HTTP_USER_AGENT'] ?? '-';

    $perangkat = $_SERVER['HTTP_USER_AGENT'] ?? '-';


    /*
    |--------------------------------------------------------------------------
    | SIMPAN SESI UJIAN
    |--------------------------------------------------------------------------
    */

    $query = $pdo->prepare("

        INSERT INTO sesi_ujian

        (

            id_ujian,
            id_siswa,

            waktu_mulai,
            waktu_selesai,

            status,

            jumlah_pelanggaran,

            alamat_ip,
            perangkat,
            browser

        )

        VALUES

        (

            ?, ?,

            ?, ?,

            'Sedang Mengerjakan',

            0,

            ?, ?, ?

        )

    ");

    $query->execute([

        $id_ujian,
        $id_siswa,

        $waktu_mulai,
        $waktu_selesai,

        $alamat_ip,
        $perangkat,
        $browser

    ]);


    /*
    |--------------------------------------------------------------------------
    | ID SESI
    |--------------------------------------------------------------------------
    */

    $id_sesi = (int) $pdo->lastInsertId();


    /*
    |--------------------------------------------------------------------------
    | AMBIL SOAL
    |--------------------------------------------------------------------------
    */

    $query = $pdo->prepare("

        SELECT

            id_soal

        FROM soal

        WHERE

            id_paket = ?

            AND dihapus_pada IS NULL

        ORDER BY nomor ASC

    ");

    $query->execute([

        $ujian['id_paket']

    ]);

    $daftarSoal = $query->fetchAll(PDO::FETCH_COLUMN);


    /*
    |--------------------------------------------------------------------------
    | VALIDASI SOAL
    |--------------------------------------------------------------------------
    */

    if (count($daftarSoal) == 0) {

        throw new Exception(
            "Soal belum tersedia."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ACAK SOAL
    |--------------------------------------------------------------------------
    */

    if (
        isset($ujian['acak_soal'])
        &&
        (
            $ujian['acak_soal'] == "Ya"
            ||
            $ujian['acak_soal'] == 1
            ||
            $ujian['acak_soal'] == "1"
        )
    ) {

        shuffle($daftarSoal);

    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN URUTAN SOAL
    |--------------------------------------------------------------------------
    */

    $queryUrutan = $pdo->prepare("

        INSERT INTO urutan_soal

        (

            id_sesi,
            id_soal,
            nomor_tampil

        )

        VALUES

        (

            ?, ?, ?

        )

    ");

    $nomorTampil = 1;

    foreach ($daftarSoal as $id_soal) {

        $queryUrutan->execute([

            $id_sesi,
            $id_soal,
            $nomorTampil

        ]);

        $nomorTampil++;

    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN URUTAN PILIHAN
    |--------------------------------------------------------------------------
    */

    $queryPilihan = $pdo->prepare("

        INSERT INTO urutan_pilihan

        (

            id_sesi,
            id_soal,

            pilihan_a,
            pilihan_b,
            pilihan_c,
            pilihan_d

        )

        VALUES

        (

            ?, ?,

            ?, ?, ?, ?

        )

    ");


    /*
    |--------------------------------------------------------------------------
    | SIMPAN MAPPING PILIHAN
    |--------------------------------------------------------------------------
    */

    foreach ($daftarSoal as $id_soal) {

        $mapping = [

            'A',
            'B',
            'C',
            'D'

        ];


        if (
            isset($ujian['acak_pilihan'])
            &&
            (
                $ujian['acak_pilihan'] == "Ya"
                ||
                $ujian['acak_pilihan'] == 1
                ||
                $ujian['acak_pilihan'] == "1"
            )
        ) {

            shuffle($mapping);

        }


        $queryPilihan->execute([

            $id_sesi,
            $id_soal,

            $mapping[0],
            $mapping[1],
            $mapping[2],
            $mapping[3]

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | COMMIT
    |--------------------------------------------------------------------------
    */

    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | HAPUS TOKEN
    |--------------------------------------------------------------------------
    */

    unset($_SESSION['token_valid'][$id_ujian]);


    /*
    |--------------------------------------------------------------------------
    | MASUK KE HALAMAN SOAL
    |--------------------------------------------------------------------------
    */

    header(
        "Location: soal/index.php?id_ujian=" . $id_ujian
    );

    exit;


}


/*
|--------------------------------------------------------------------------
| ERROR
|--------------------------------------------------------------------------
*/

catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Terjadi kesalahan saat memulai ujian: " .
        $e->getMessage();

    header(
        "Location: konfirmasi.php?id_ujian=" . $id_ujian
    );

    exit;

}