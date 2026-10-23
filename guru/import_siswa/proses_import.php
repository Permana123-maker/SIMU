<?php

/*
|--------------------------------------------------------------------------
| PROSES IMPORT SISWA
|--------------------------------------------------------------------------
| SIMU - Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
| Menyimpan data hasil preview ke database.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../../app/keamanan/session.php';
require_once __DIR__ . '/../../app/database/database.php';

/*
|--------------------------------------------------------------------------
| VALIDASI SESSION IMPORT
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['import_siswa']) ||
    empty($_SESSION['import_siswa'])
) {

    $_SESSION['error'] = "Tidak ada data yang akan diimport.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| AMBIL DATA SESSION
|--------------------------------------------------------------------------
*/

$dataImport = $_SESSION['import_siswa'];

$idTahun = $_SESSION['import_id_tahun'] ?? 0;

$idKelas = $_SESSION['import_id_kelas'] ?? 0;

/*
|--------------------------------------------------------------------------
| VALIDASI DATA SESSION
|--------------------------------------------------------------------------
*/

if ($idTahun <= 0) {

    $_SESSION['error'] = "Tahun Pelajaran tidak valid.";

    header("Location: index.php");

    exit;

}

if ($idKelas <= 0) {

    $_SESSION['error'] = "Kelas tidak valid.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| MULAI TRANSACTION
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | QUERY CEK DUPLIKASI NIS
    |--------------------------------------------------------------------------
    */

    $sqlCek = "
        SELECT COUNT(*)
        FROM siswa
        WHERE nis = :nis
        AND dihapus_pada IS NULL
    ";

    $stmtCek = $pdo->prepare($sqlCek);

    /*
    |--------------------------------------------------------------------------
    | LANJUT BAGIAN 2
    |--------------------------------------------------------------------------
    */

        /*
    |--------------------------------------------------------------------------
    | QUERY INSERT SISWA
    |--------------------------------------------------------------------------
    */

    $sqlInsert = "
        INSERT INTO siswa
        (
            id_tahun,
            id_kelas,
            nis,
            nama,
            jk,
            nomor_peserta,
            password
        )
        VALUES
        (
            :id_tahun,
            :id_kelas,
            :nis,
            :nama,
            :jk,
            :nomor_peserta,
            :password
        )
    ";

    $stmtInsert = $pdo->prepare($sqlInsert);

    /*
    |--------------------------------------------------------------------------
    | PROSES IMPORT
    |--------------------------------------------------------------------------
    */

    $jumlahImport = 0;

    foreach ($dataImport as $row) {

        /*
        |--------------------------------------------------------------------------
        | Lewati data yang tidak valid
        |--------------------------------------------------------------------------
        */

        if ($row['status'] !== 'VALID') {

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Cek apakah NIS sudah ada
        |--------------------------------------------------------------------------
        */

        $stmtCek->execute([

            ':nis' => $row['nis']

        ]);

        if ($stmtCek->fetchColumn() > 0) {

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Password Awal
        |--------------------------------------------------------------------------
        */

        $password = password_hash(

            $row['nis'],

            PASSWORD_DEFAULT

        );

        /*
        |--------------------------------------------------------------------------
        | Simpan Data
        |--------------------------------------------------------------------------
        */

        $stmtInsert->execute([

            ':id_tahun'       => $idTahun,

            ':id_kelas'       => $idKelas,

            ':nis'            => $row['nis'],

            ':nama'           => $row['nama'],

            ':jk'             => $row['jk'],

            ':nomor_peserta'  => $row['nis'],

            ':password'       => $password

        ]);

        $jumlahImport++;

    }

    /*
    |--------------------------------------------------------------------------
    | LANJUT BAGIAN 3
    |--------------------------------------------------------------------------
    */

        /*
    |--------------------------------------------------------------------------
    | SELESAIKAN TRANSAKSI
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | HAPUS SESSION IMPORT
    |--------------------------------------------------------------------------
    */

    unset($_SESSION['import_siswa']);
    unset($_SESSION['import_id_tahun']);
    unset($_SESSION['import_id_kelas']);
    unset($_SESSION['import_nama_kelas']);

    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI BERHASIL
    |--------------------------------------------------------------------------
    */

    $_SESSION['success'] =
        "Import siswa berhasil. " .
        number_format($jumlahImport) .
        " siswa berhasil diimport.";

    header("Location: index.php");

    exit;

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | ROLLBACK JIKA GAGAL
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['error'] =
        "Import gagal. " . $e->getMessage();

    header("Location: index.php");

    exit;

} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | ROLLBACK JIKA TERJADI ERROR LAIN
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['error'] =
        "Import gagal. " . $e->getMessage();

    header("Location: index.php");

    exit;

}