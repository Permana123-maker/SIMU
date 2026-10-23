<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Edit Tahun Pelajaran
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

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

$id_tahun   = (int) ($_POST['id_tahun'] ?? 0);
$nama_tahun = trim($_POST['nama_tahun'] ?? '');
$aktif      = trim($_POST['aktif'] ?? 'Tidak');

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if ($id_tahun <= 0) {

    $_SESSION['pesan'] = "Data Tahun Pelajaran tidak ditemukan.";

    $_SESSION['warna'] = "danger";

    header("Location: index.php");

    exit;

}

if ($nama_tahun === '') {

    $_SESSION['pesan'] = "Nama Tahun Pelajaran wajib diisi.";

    $_SESSION['warna'] = "danger";

    header("Location: edit.php?id=" . $id_tahun);

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

    header("Location: edit.php?id=" . $id_tahun);

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

    header("Location: edit.php?id=" . $id_tahun);

    exit;

}

/*
|--------------------------------------------------------------------------
| Cek Duplikasi
|--------------------------------------------------------------------------
*/

$cek = $pdo->prepare("

    SELECT COUNT(*)

    FROM tahun_ajaran

    WHERE nama_tahun = ?

    AND id_tahun <> ?

    AND dihapus_pada IS NULL

");

$cek->execute([

    $nama_tahun,
    $id_tahun

]);

if ($cek->fetchColumn() > 0) {

    $_SESSION['pesan'] = "Nama Tahun Pelajaran sudah digunakan.";

    $_SESSION['warna'] = "warning";

    header("Location: edit.php?id=" . $id_tahun);

    exit;

}

/*
|--------------------------------------------------------------------------
| Proses Update Data
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Jika Tahun Pelajaran Akan Diaktifkan
    |--------------------------------------------------------------------------
    */

    if ($aktif === "Ya") {

        $nonaktifkan = $pdo->prepare("

            UPDATE tahun_ajaran

            SET aktif = 'Tidak'

            WHERE aktif = 'Ya'

            AND dihapus_pada IS NULL

            AND id_tahun <> ?

        ");

        $nonaktifkan->execute([

            $id_tahun

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Update Tahun Pelajaran
    |--------------------------------------------------------------------------
    */

    $update = $pdo->prepare("

        UPDATE tahun_ajaran

        SET

            nama_tahun = ?,
            aktif = ?

        WHERE

            id_tahun = ?

        AND

            dihapus_pada IS NULL

    ");

    $update->execute([

        $nama_tahun,
        $aktif,
        $id_tahun

    ]);

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

    $_SESSION['pesan'] = "Tahun Pelajaran berhasil diperbarui.";

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

    /*
    |--------------------------------------------------------------------------
    | Gagal
    |--------------------------------------------------------------------------
    */

    $_SESSION['pesan'] = "Terjadi kesalahan saat memperbarui Tahun Pelajaran.";

    $_SESSION['warna'] = "danger";

    header("Location: edit.php?id=" . $id_tahun);

    exit;

}