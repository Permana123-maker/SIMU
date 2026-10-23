<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Tambah Tahun Pelajaran
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/konfigurasi/kelas_default.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$nama_tahun = trim($_POST['nama_tahun'] ?? '');
$aktif      = trim($_POST['aktif'] ?? 'Tidak');

/*
|--------------------------------------------------------------------------
| Validasi Input Kosong
|--------------------------------------------------------------------------
*/

if ($nama_tahun === '') {

    $_SESSION['pesan'] = "Nama Tahun Pelajaran wajib diisi.";

    $_SESSION['warna'] = "danger";

    header("Location: tambah.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Format Tahun Pelajaran
|--------------------------------------------------------------------------
*/

if (!preg_match('/^\d{4}\/\d{4}$/', $nama_tahun)) {

    $_SESSION['pesan'] = "Format Tahun Pelajaran harus menggunakan format 2026/2027.";

    $_SESSION['warna'] = "danger";

    header("Location: tambah.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Logika Tahun
|--------------------------------------------------------------------------
*/

list($tahun_awal, $tahun_akhir) = explode('/', $nama_tahun);

$tahun_awal  = (int) $tahun_awal;
$tahun_akhir = (int) $tahun_akhir;

if ($tahun_akhir !== ($tahun_awal + 1)) {

    $_SESSION['pesan'] = "Tahun akhir harus satu tahun setelah tahun awal.";

    $_SESSION['warna'] = "danger";

    header("Location: tambah.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Duplikasi Tahun Pelajaran
|--------------------------------------------------------------------------
*/

$cek = $pdo->prepare("

    SELECT COUNT(*)

    FROM tahun_ajaran

    WHERE nama_tahun = ?

    AND dihapus_pada IS NULL

");

$cek->execute([

    $nama_tahun

]);

if ($cek->fetchColumn() > 0) {

    $_SESSION['pesan'] = "Tahun Pelajaran sudah tersedia.";

    $_SESSION['warna'] = "warning";

    header("Location: tambah.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Simpan Data
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Jika Aktif = Ya
    | Maka seluruh Tahun Pelajaran dinonaktifkan
    |--------------------------------------------------------------------------
    */

    if ($aktif === "Ya") {

        $nonaktifkan = $pdo->prepare("

            UPDATE tahun_ajaran

            SET aktif = 'Tidak'

            WHERE aktif = 'Ya'

        ");

        $nonaktifkan->execute();

    }

    /*
    |--------------------------------------------------------------------------
    | Simpan Tahun Pelajaran Baru
    |--------------------------------------------------------------------------
    */

    $simpan = $pdo->prepare("

        INSERT INTO tahun_ajaran
        (
            nama_tahun,
            aktif
        )
        VALUES
        (
            ?,
            ?
        )

    ");

    $simpan->execute([

        $nama_tahun,
        $aktif

    ]);

    /*
|--------------------------------------------------------------------------
| Ambil ID Tahun Pelajaran Baru
|--------------------------------------------------------------------------
*/

$idTahun = $pdo->lastInsertId();

/*
|--------------------------------------------------------------------------
| Ambil Konfigurasi Kelas Default
|--------------------------------------------------------------------------
*/

$kelasDefault = require "../../app/konfigurasi/kelas_default.php";

/*
|--------------------------------------------------------------------------
| Query Simpan Kelas
|--------------------------------------------------------------------------
*/

$simpanKelas = $pdo->prepare("

    INSERT INTO kelas
    (
        id_tahun,
        nama_kelas
    )
    VALUES
    (
        ?,
        ?
    )

");

/*
|--------------------------------------------------------------------------
| Generate Kelas Otomatis
|--------------------------------------------------------------------------
*/

foreach ($kelasDefault as $jenjang => $jumlah) {

    for ($i = 1; $i <= $jumlah; $i++) {

        $namaKelas = $jenjang . " " . $i;

        $simpanKelas->execute([

            $idTahun,

            $namaKelas

        ]);

    }

}

    /*
    |--------------------------------------------------------------------------
    | Commit
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

        /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['pesan'] = "Tahun Pelajaran berhasil ditambahkan.";

    $_SESSION['warna'] = "success";

    header("Location: index.php");

    exit;

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['pesan'] = "Terjadi kesalahan saat menyimpan data.";

    $_SESSION['warna'] = "danger";

    header("Location: tambah.php");

    exit;

}