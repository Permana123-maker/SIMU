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

$id_paket = isset($_POST['id_paket'])
    ? (int) $_POST['id_paket']
    : 0;

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

    $id_paket <= 0 ||
    $id_tahun <= 0 ||
    $jenjang == "" ||
    $nama_paket == ""

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Semua data wajib diisi.";

    header("Location: edit.php?id_paket=".$id_paket);
    exit;
}

if (!in_array($jenjang, ['VII', 'VIII', 'IX'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Jenjang tidak valid.";

    header("Location: edit.php?id_paket=".$id_paket);
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Paket Soal
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_paket
    FROM paket_soal
    WHERE
        id_paket = ?
        AND id_tahun = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_paket,
    $id_tahun
]);

if (!$query->fetch()) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Paket soal tidak ditemukan.";

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Duplikasi
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
        AND id_paket <> ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_tahun,
    $jenjang,
    $nama_paket,
    $id_paket
]);

if ($query->fetch()) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] =
        "Nama paket soal sudah digunakan.";

    header("Location: edit.php?id_paket=".$id_paket);
    exit;
}

/*
|--------------------------------------------------------------------------
| Update Data
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $query = $pdo->prepare("
        UPDATE paket_soal
        SET
            jenjang = :jenjang,
            nama_paket = :nama_paket,
            keterangan = :keterangan,
            diubah_pada = NOW()
        WHERE
            id_paket = :id_paket
    ");

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

    $query->bindValue(
        ':id_paket',
        $id_paket,
        PDO::PARAM_INT
    );

    $query->execute();

    $pdo->commit();

    $_SESSION['warna'] = "success";
    $_SESSION['pesan'] =
        "Paket soal berhasil diperbarui.";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Terjadi kesalahan saat memperbarui data.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;