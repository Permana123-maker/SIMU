<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Login Siswa
|--------------------------------------------------------------------------
*/

session_start();

require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada Tahun Pelajaran yang aktif.";

    header("Location: login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$nis = trim($_POST['nis'] ?? "");
$password = $_POST['password'] ?? "";

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if ($nis === "" || $password === "") {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "NIS dan Password wajib diisi.";

    header("Location: login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Siswa
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        s.id_siswa,
        s.id_kelas,
        s.id_tahun,
        s.nis,
        s.nama AS nama_siswa,
        s.password,
        s.status,

        k.nama_kelas

    FROM siswa s

    INNER JOIN kelas k
        ON s.id_kelas = k.id_kelas

    WHERE
        s.nis = ?
        AND s.id_tahun = ?
        AND s.status = 'Aktif'
        AND s.dihapus_pada IS NULL

    LIMIT 1
");

$query->execute([
    $nis,
    $tahun['id_tahun']
]);

$siswa = $query->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Validasi Data Siswa
|--------------------------------------------------------------------------
*/

if (!$siswa) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "NIS atau Password salah.";

    header("Location: login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Password
|--------------------------------------------------------------------------
*/

if (
    empty($siswa['password']) ||
    !password_verify($password, $siswa['password'])
) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "NIS atau Password salah.";

    header("Location: login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Regenerasi Session ID
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

/*
|--------------------------------------------------------------------------
| Buat Session Login Siswa
|--------------------------------------------------------------------------
*/

$_SESSION['siswa'] = [

    'id_siswa'   => $siswa['id_siswa'],
    'id_kelas'   => $siswa['id_kelas'],
    'nis'        => $siswa['nis'],
    'nama_siswa' => $siswa['nama_siswa'],
    'nama_kelas' => $siswa['nama_kelas'],
    'id_tahun'   => $tahun['id_tahun']

];

/*
|--------------------------------------------------------------------------
| Pesan Login Berhasil
|--------------------------------------------------------------------------
*/

$_SESSION['warna'] = "success";

$_SESSION['pesan'] =
    "Selamat datang, " . $siswa['nama_siswa'] . ".";

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: ../dashboard/index.php");
exit;