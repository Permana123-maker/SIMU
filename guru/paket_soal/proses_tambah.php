<?php

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

$id_tahun = isset($_POST['id_tahun'])
    ? (int) $_POST['id_tahun']
    : 0;

$jenjang = trim($_POST['jenjang'] ?? '');

$nama_paket = trim($_POST['nama_paket'] ?? '');

$keterangan = trim($_POST['keterangan'] ?? '');

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if (

    $id_tahun <= 0 ||
    $jenjang == "" ||
    $nama_paket == ""

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Semua data wajib diisi.";

    header("Location: tambah.php");
    exit;
}

if (!in_array($jenjang, ['VII', 'VIII', 'IX'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Jenjang tidak valid.";

    header("Location: tambah.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Tahun Ajaran
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_tahun
    FROM tahun_ajaran
    WHERE
        id_tahun = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_tahun
]);

if (!$query->fetch()) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Tahun ajaran tidak ditemukan.";

    header("Location: tambah.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Duplikasi Paket
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_paket
    FROM paket_soal
    WHERE
        id_tahun = ?
        AND jenjang = ?
        AND nama_paket = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([

    $id_tahun,
    $jenjang,
    $nama_paket

]);

if ($query->fetch()) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] =
        "Paket soal dengan nama tersebut sudah ada.";

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

    $query = $pdo->prepare("
        INSERT INTO paket_soal
        (
            id_tahun,
            jenjang,
            nama_paket,
            keterangan
        )
        VALUES
        (
            :id_tahun,
            :jenjang,
            :nama_paket,
            :keterangan
        )
    ");

    $query->bindValue(
        ':id_tahun',
        $id_tahun,
        PDO::PARAM_INT
    );

    $query->bindValue(
        ':jenjang',
        $jenjang
    );

    $query->bindValue(
        ':nama_paket',
        $nama_paket
    );

    $query->bindValue(
        ':keterangan',
        $keterangan == "" ? null : $keterangan
    );

    $query->execute();

    $pdo->commit();

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] =
        "Paket soal berhasil ditambahkan.";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Terjadi kesalahan saat menyimpan data.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;