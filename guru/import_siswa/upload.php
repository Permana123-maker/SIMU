<?php

/*
|--------------------------------------------------------------------------
| UPLOAD IMPORT SISWA
|--------------------------------------------------------------------------
| SIMU - Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
| Membaca file Excel dan menyimpan hasilnya ke Session
| untuk ditampilkan pada halaman Preview.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../../app/keamanan/session.php';
require_once __DIR__ . '/../../app/database/database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

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
| VALIDASI TAHUN PELAJARAN
|--------------------------------------------------------------------------
*/

$idTahun = isset($_POST['id_tahun'])
    ? (int) $_POST['id_tahun']
    : 0;

if ($idTahun <= 0) {

    $_SESSION['error'] = "Tahun Pelajaran tidak valid.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| VALIDASI KELAS
|--------------------------------------------------------------------------
*/

$idKelas = isset($_POST['id_kelas'])
    ? (int) $_POST['id_kelas']
    : 0;

if ($idKelas <= 0) {

    $_SESSION['error'] = "Silakan pilih kelas terlebih dahulu.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| VALIDASI FILE
|--------------------------------------------------------------------------
*/

if (
    !isset($_FILES['file_excel']) ||
    empty($_FILES['file_excel']['name'])
) {

    $_SESSION['error'] = "File Excel belum dipilih.";

    header("Location: index.php");

    exit;

}

$file = $_FILES['file_excel'];

if ($file['error'] !== UPLOAD_ERR_OK) {

    $_SESSION['error'] = "Upload file gagal.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| VALIDASI UKURAN FILE
|--------------------------------------------------------------------------
*/

$maxSize = 5 * 1024 * 1024; // 5 MB

if ($file['size'] > $maxSize) {

    $_SESSION['error'] =
        "Ukuran file maksimal 5 MB.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| LANJUT BAGIAN 2
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| VALIDASI EKSTENSI FILE
|--------------------------------------------------------------------------
*/

$ekstensi = strtolower(

    pathinfo(

        $file['name'],

        PATHINFO_EXTENSION

    )

);

$ekstensiDiizinkan = [

    'xlsx',

    'xls'

];

if (!in_array($ekstensi, $ekstensiDiizinkan)) {

    $_SESSION['error'] =
        "Format file harus .xlsx atau .xls.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| MEMBACA FILE EXCEL
|--------------------------------------------------------------------------
*/

try {

    $spreadsheet = IOFactory::load($file['tmp_name']);

    $sheet = $spreadsheet->getActiveSheet();

    /*
    |--------------------------------------------------------------------------
    | VALIDASI HEADER TEMPLATE
    |--------------------------------------------------------------------------
    */

    $headerA = trim((string) $sheet->getCell('A1')->getValue());
    $headerB = trim((string) $sheet->getCell('B1')->getValue());
    $headerC = trim((string) $sheet->getCell('C1')->getValue());
    $headerD = trim((string) $sheet->getCell('D1')->getValue());

    if (

        strtoupper($headerA) !== 'NO' ||

        strtoupper($headerB) !== 'NIS' ||

        strtoupper($headerC) !== 'NAMA LENGKAP' ||

        strtoupper($headerD) !== 'JENIS KELAMIN'

    ) {

        $_SESSION['error'] =
            "Template Excel tidak sesuai. Gunakan template resmi SIMU.";

        header("Location: index.php");

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI FILE KOSONG
    |--------------------------------------------------------------------------
    */

    $barisTerakhir = $sheet->getHighestRow();

    if ($barisTerakhir < 2) {

        $_SESSION['error'] =
            "File Excel tidak memiliki data siswa.";

        header("Location: index.php");

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | PERSIAPAN ARRAY IMPORT
    |--------------------------------------------------------------------------
    */

    $hasilImport = [];

    /*
    |--------------------------------------------------------------------------
    | LANJUT BAGIAN 3
    |--------------------------------------------------------------------------
    */

        /*
    |--------------------------------------------------------------------------
    | MEMBACA DATA EXCEL
    |--------------------------------------------------------------------------
    */

    for ($baris = 2; $baris <= $barisTerakhir; $baris++) {

        /*
        |--------------------------------------------------------------------------
        | Kolom Excel
        |--------------------------------------------------------------------------
        | A = No
        | B = NIS
        | C = Nama Lengkap
        | D = Jenis Kelamin
        |--------------------------------------------------------------------------
        */

        $nis = trim((string) $sheet->getCell('B' . $baris)->getValue());

        $nama = trim((string) $sheet->getCell('C' . $baris)->getValue());

        $jk = strtoupper(
            trim((string) $sheet->getCell('D' . $baris)->getValue())
        );

        /*
        |--------------------------------------------------------------------------
        | LEWATI BARIS KOSONG
        |--------------------------------------------------------------------------
        */

        if (

            $nis === '' &&

            $nama === '' &&

            $jk === ''

        ) {

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | STATUS AWAL
        |--------------------------------------------------------------------------
        */

        $status = "VALID";

        $keterangan = "";

        /*
        |--------------------------------------------------------------------------
        | VALIDASI NIS
        |--------------------------------------------------------------------------
        */

        if ($nis === '') {

            $status = "ERROR";

            $keterangan = "NIS tidak boleh kosong.";

        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI NAMA
        |--------------------------------------------------------------------------
        */

        elseif ($nama === '') {

            $status = "ERROR";

            $keterangan = "Nama Lengkap tidak boleh kosong.";

        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI JENIS KELAMIN
        |--------------------------------------------------------------------------
        */

        elseif (!in_array($jk, ['L', 'P'])) {

            $status = "ERROR";

            $keterangan = "Jenis Kelamin harus L atau P.";

        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKAT NIS DI FILE EXCEL
        |--------------------------------------------------------------------------
        */

        foreach ($hasilImport as $data) {

            if ($data['nis'] === $nis) {

                $status = "ERROR";

                $keterangan = "NIS ganda di dalam file Excel.";

                break;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KE ARRAY PREVIEW
        |--------------------------------------------------------------------------
        */

        $hasilImport[] = [

            'baris'      => $baris,

            'nis'        => $nis,

            'nama'       => $nama,

            'jk'         => $jk,

            'status'     => $status,

            'keterangan' => $keterangan

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI HASIL IMPORT
    |--------------------------------------------------------------------------
    */

    if (count($hasilImport) === 0) {

        $_SESSION['error'] =
            "Tidak ada data siswa yang dapat diproses.";

        header("Location: index.php");

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | LANJUT BAGIAN 4
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | AMBIL NAMA KELAS
    |--------------------------------------------------------------------------
    */

    $sql = "

        SELECT
            nama_kelas

        FROM kelas

        WHERE id_kelas = ?

        LIMIT 1

    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        $idKelas

    ]);

    $kelas = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$kelas) {

        $_SESSION['error'] =
            "Data kelas tidak ditemukan.";

        header("Location: index.php");

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN KE SESSION
    |--------------------------------------------------------------------------
    */

    $_SESSION['import_siswa'] = $hasilImport;

    $_SESSION['import_id_tahun'] = $idTahun;

    $_SESSION['import_id_kelas'] = $idKelas;

    $_SESSION['import_nama_kelas'] = $kelas['nama_kelas'];

    /*
    |--------------------------------------------------------------------------
    | PINDAH KE HALAMAN PREVIEW
    |--------------------------------------------------------------------------
    */

    header("Location: preview.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| HANDLE ERROR
|--------------------------------------------------------------------------
*/

catch (Exception $e) {

    $_SESSION['error'] =
        "Terjadi kesalahan saat membaca file Excel.<br>"
        . $e->getMessage();

    header("Location: index.php");

    exit;

}