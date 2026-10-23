<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";

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

$id_siswa = isset($_POST['id_siswa'])
    ? (int) $_POST['id_siswa']
    : 0;

$id_kelas = isset($_POST['id_kelas'])
    ? (int) $_POST['id_kelas']
    : 0;

$nis = trim($_POST['nis'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$jk = trim($_POST['jk'] ?? '');
$status = trim($_POST['status'] ?? '');

$password = $_POST['password'] ?? '';

/*
|--------------------------------------------------------------------------
| Validasi Data Utama
|--------------------------------------------------------------------------
*/

if (
    $id_siswa <= 0 ||
    $id_kelas <= 0 ||
    $nis == "" ||
    $nama == "" ||
    $jk == "" ||
    $status == ""
) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Semua data wajib diisi.";

    header(
        "Location: edit.php?id_siswa=" . $id_siswa
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi Jenis Kelamin
|--------------------------------------------------------------------------
*/

if (!in_array($jk, ['L', 'P'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Jenis kelamin tidak valid.";

    header(
        "Location: edit.php?id_siswa=" . $id_siswa
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi Status
|--------------------------------------------------------------------------
*/

if (!in_array($status, ['Aktif', 'Tidak Aktif'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Status tidak valid.";

    header(
        "Location: edit.php?id_siswa=" . $id_siswa
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi Password
|--------------------------------------------------------------------------
|
| Password boleh dikosongkan.
| Jika kosong → password lama tetap digunakan.
| Jika diisi → password akan diganti.
|
*/

if ($password !== '' && strlen($password) < 6) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Password baru minimal 6 karakter.";

    header(
        "Location: edit.php?id_siswa=" . $id_siswa
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Lama
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_siswa,
        id_kelas,
        id_tahun,
        password
    FROM siswa
    WHERE
        id_siswa = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_siswa
]);

$dataLama = $query->fetch(PDO::FETCH_ASSOC);

if (!$dataLama) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Data siswa tidak ditemukan.";

    header("Location: ../index.php");

    exit;
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
        AND id_siswa <> ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $nis,
    $dataLama['id_tahun'],
    $id_siswa
]);

if ($query->fetch()) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] =
        "NIS sudah digunakan.";

    header(
        "Location: edit.php?id_siswa=" . $id_siswa
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_kelas
    FROM kelas
    WHERE
        id_kelas = ?
        AND id_tahun = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_kelas,
    $dataLama['id_tahun']
]);

if (!$query->fetch()) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] =
        "Kelas tidak ditemukan.";

    header(
        "Location: edit.php?id_siswa=" . $id_siswa
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Update Data
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------
    | Jika password diisi → update password
    | Jika kosong → password lama tetap
    |--------------------------------------------------------------
    */

    if ($password !== '') {

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $query = $pdo->prepare("
            UPDATE siswa
            SET
                nis = :nis,
                nama = :nama,
                jk = :jk,
                id_kelas = :id_kelas,
                status = :status,
                password = :password
            WHERE
                id_siswa = :id_siswa
        ");

        $query->bindValue(
            ':password',
            $passwordHash
        );

    } else {

        $query = $pdo->prepare("
            UPDATE siswa
            SET
                nis = :nis,
                nama = :nama,
                jk = :jk,
                id_kelas = :id_kelas,
                status = :status
            WHERE
                id_siswa = :id_siswa
        ");
    }

    $query->bindValue(
        ':nis',
        $nis
    );

    $query->bindValue(
        ':nama',
        $nama
    );

    $query->bindValue(
        ':jk',
        $jk
    );

    $query->bindValue(
        ':id_kelas',
        $id_kelas,
        PDO::PARAM_INT
    );

    $query->bindValue(
        ':status',
        $status
    );

    $query->bindValue(
        ':id_siswa',
        $id_siswa,
        PDO::PARAM_INT
    );

    $query->execute();

    $pdo->commit();

    /*
    |--------------------------------------------------------------
    | Pesan berhasil
    |--------------------------------------------------------------
    */

    if ($password !== '') {

        $_SESSION['warna'] = "success";
        $_SESSION['pesan'] =
            "Data siswa dan password berhasil diperbarui.";

    } else {

        $_SESSION['warna'] = "success";
        $_SESSION['pesan'] =
            "Data siswa berhasil diperbarui.";
    }

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Terjadi kesalahan saat memperbarui data.";
}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header(
    "Location: index.php?id_kelas=" . $id_kelas
);

exit;