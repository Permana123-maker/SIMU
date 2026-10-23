<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/nomor_peserta.php";

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

$nis  = trim($_POST['nis'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$jk   = trim($_POST['jk'] ?? '');

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if (

    $id_kelas <= 0 ||
    $id_tahun <= 0 ||
    $nis == "" ||
    $nama == "" ||
    $jk == ""

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Semua data wajib diisi.";

    header("Location: tambah.php?id_kelas=".$id_kelas);
    exit;
}

if (!in_array($jk, ['L','P'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Jenis kelamin tidak valid.";

    header("Location: tambah.php?id_kelas=".$id_kelas);
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
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([$nis]);

if ($query->fetch()) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "NIS sudah digunakan.";

    header("Location: tambah.php?id_kelas=".$id_kelas);
    exit;
}

/*
|--------------------------------------------------------------------------
| Generate Nomor Peserta
|--------------------------------------------------------------------------
*/

$nomor_peserta = generateNomorPeserta(
    $pdo,
    $id_tahun
);

/*
|--------------------------------------------------------------------------
| Password Awal
|--------------------------------------------------------------------------
|
| Username Login :
| Nomor Peserta
|
| Password Awal :
| Nomor Peserta
|--------------------------------------------------------------------------
*/

$password = password_hash(

    $nomor_peserta,

    PASSWORD_DEFAULT

);

/*
|--------------------------------------------------------------------------
| Simpan Data
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

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
    $query->bindValue(':nomor_peserta', $nomor_peserta);
    $query->bindValue(':password', $password);

    $query->execute();

    $pdo->commit();

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] =
        "Siswa berhasil ditambahkan.<br><br>

        <strong>Username Login :</strong> {$nomor_peserta}<br>

        <strong>Password Awal :</strong> {$nomor_peserta}<br><br>

        Silakan berikan akun tersebut kepada siswa.";

} catch (PDOException $e) {

    $pdo->rollBack();

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] =
        "Terjadi kesalahan saat menyimpan data siswa.<br>" .
        $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php?id_kelas=".$id_kelas);
exit;