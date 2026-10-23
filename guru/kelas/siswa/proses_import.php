<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/nomor_peserta.php";
require_once "../../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$id_kelas = isset($_POST['id_kelas']) ? (int) $_POST['id_kelas'] : 0;
$id_tahun = isset($_POST['id_tahun']) ? (int) $_POST['id_tahun'] : 0;

if ($id_kelas <= 0 || $id_tahun <= 0) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak valid.";

    header("Location: index.php?id_kelas=".$id_kelas);
    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi Upload
|--------------------------------------------------------------------------
*/

if (

    !isset($_FILES['file_excel']) ||
    $_FILES['file_excel']['error'] != UPLOAD_ERR_OK

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Silakan pilih file Excel.";

    header("Location: import.php?id_kelas=".$id_kelas);
    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi Ekstensi
|--------------------------------------------------------------------------
*/

$namaFile = $_FILES['file_excel']['name'];

$ekstensi = strtolower(

    pathinfo(

        $namaFile,

        PATHINFO_EXTENSION

    )

);

if (!in_array($ekstensi, ['xlsx', 'xls'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Format file harus .xls atau .xlsx.";

    header("Location: import.php?id_kelas=".$id_kelas);
    exit;
}

/*
|--------------------------------------------------------------------------
| Load Excel
|--------------------------------------------------------------------------
*/

try {

    $spreadsheet = IOFactory::load(

        $_FILES['file_excel']['tmp_name']

    );

} catch (Exception $e) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "File Excel tidak dapat dibaca.<br>" .
        $e->getMessage();

    header("Location: import.php?id_kelas=".$id_kelas);
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Sheet Aktif
|--------------------------------------------------------------------------
*/

$sheet = $spreadsheet->getActiveSheet();

$dataExcel = $sheet->toArray(

    null,

    true,

    true,

    true

);

/*
|--------------------------------------------------------------------------
| Validasi Jumlah Baris
|--------------------------------------------------------------------------
*/

if (count($dataExcel) <= 1) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "File Excel tidak memiliki data.";

    header("Location: import.php?id_kelas=".$id_kelas);
    exit;
}

/*
|--------------------------------------------------------------------------
| Persiapan Import
|--------------------------------------------------------------------------
*/

$totalImport = 0;

$totalLewat = 0;

$barisError = [];

/*
|--------------------------------------------------------------------------
| Mulai Database Transaction
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    /*
    |----------------------------------------------------------------------
    | Lanjut ke proses pembacaan setiap baris Excel
    |----------------------------------------------------------------------
    */

      /*
    |--------------------------------------------------------------------------
    | Proses Setiap Baris Excel
    |--------------------------------------------------------------------------
    */

    foreach ($dataExcel as $nomorBaris => $baris) {

        /*
        |--------------------------------------------------------------------------
        | Lewati Header
        |--------------------------------------------------------------------------
        */

        if ($nomorBaris == 1) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Data
        |--------------------------------------------------------------------------
        */

        $nis = trim($baris['A']);
        $nama = trim($baris['B']);
        $jk = strtoupper(trim($baris['C']));

        /*
        |--------------------------------------------------------------------------
        | Lewati Baris Kosong
        |--------------------------------------------------------------------------
        */

        if (

            $nis == "" &&
            $nama == "" &&
            $jk == ""

        ) {

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Data Wajib
        |--------------------------------------------------------------------------
        */

        if (

            $nis == "" ||
            $nama == "" ||
            $jk == ""

        ) {

            $totalLewat++;

            $barisError[] =
                "Baris {$nomorBaris} : Data belum lengkap.";

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Jenis Kelamin
        |--------------------------------------------------------------------------
        */

        if (!in_array($jk, ['L', 'P'])) {

            $totalLewat++;

            $barisError[] =
                "Baris {$nomorBaris} : Jenis kelamin harus L atau P.";

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Cek NIS
        |--------------------------------------------------------------------------
        */

        $query = $pdo->prepare("
            SELECT
                id_siswa
            FROM siswa
            WHERE
                nis = ?
                AND id_tahun = ?
                AND dihapus_pada IS NULL
            LIMIT 1
        ");

        $query->execute([
            $nis,
            $id_tahun
        ]);

        if ($query->fetch()) {

            $totalLewat++;

            $barisError[] =
                "Baris {$nomorBaris} : NIS {$nis} sudah digunakan.";

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Generate Nomor Peserta
        |--------------------------------------------------------------------------
        */

        $nomorPeserta = generateNomorPeserta(

            $pdo,
            $id_tahun
            $nomorurut

        );

        /*
        |--------------------------------------------------------------------------
        | Password Awal
        |--------------------------------------------------------------------------
        */

        $password = password_hash(

            $nomorPeserta,

            PASSWORD_DEFAULT

        );

        /*
        |--------------------------------------------------------------------------
        | Simpan Data
        |--------------------------------------------------------------------------
        */

        $query = $pdo->prepare("
            INSERT INTO siswa
            (
                nis,
                nama,
                jk,
                id_kelas,
                id_tahun,
                nomor_peserta,
                password,
                status
            )
            VALUES
            (
                :nis,
                :nama,
                :jk,
                :id_kelas,
                :id_tahun,
                :nomor_peserta,
                :password,
                'Aktif'
            )
        ");

        $query->bindValue(':nis', $nis);
        $query->bindValue(':nama', $nama);
        $query->bindValue(':jk', $jk);
        $query->bindValue(':id_kelas', $id_kelas, PDO::PARAM_INT);
        $query->bindValue(':id_tahun', $id_tahun, PDO::PARAM_INT);
        $query->bindValue(':nomor_peserta', $nomorPeserta);
        $query->bindValue(':password', $password);

        $query->execute();

        $totalImport++;

    }

    /*
    |--------------------------------------------------------------------------
    | Lanjut ke Commit Transaction
    |--------------------------------------------------------------------------
    */
    
        /*
    |--------------------------------------------------------------------------
    | Commit Transaction
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | Flash Message
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $pesan = "";

    $pesan .= "<strong>Import selesai.</strong><br><br>";

    $pesan .=
        "Berhasil diimport : <strong>{$totalImport}</strong> siswa.<br>";

    $pesan .=
        "Dilewati : <strong>{$totalLewat}</strong> siswa.";

    if (!empty($barisError)) {

        $pesan .= "<hr>";

        $pesan .= "<strong>Detail:</strong>";

        $pesan .= "<ul class='mb-0 mt-2'>";

        foreach ($barisError as $error) {

            $pesan .= "<li>{$error}</li>";

        }

        $pesan .= "</ul>";

    }

    $_SESSION['pesan'] = $pesan;

} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Terjadi kesalahan saat import data.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php?id_kelas=".$id_kelas);
exit;