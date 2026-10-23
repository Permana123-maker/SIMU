<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Input Token Ujian
|--------------------------------------------------------------------------
*/

session_start();

require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['siswa'])) {

    header("Location: ../login/login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    die("Belum ada Tahun Pelajaran aktif.");

}

/*
|--------------------------------------------------------------------------
| Validasi ID Ujian
|--------------------------------------------------------------------------
*/

$id_ujian = isset($_GET['id_ujian'])
    ? (int) $_GET['id_ujian']
    : 0;

if ($id_ujian <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Ujian tidak ditemukan.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Data Session
|--------------------------------------------------------------------------
*/

$id_kelas = $_SESSION['siswa']['id_kelas'];

/*
|--------------------------------------------------------------------------
| Ambil Data Ujian
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    u.id_ujian,
    u.nama_ujian,
    u.durasi,
    u.jenis_penilaian,

    p.nama_paket

FROM ujian u

INNER JOIN paket_soal p
    ON u.id_paket = p.id_paket

WHERE

    u.id_ujian = ?

AND

    u.id_tahun = ?

AND

    u.id_kelas = ?

AND

    u.status = 'Aktif'

AND

    u.dihapus_pada IS NULL

LIMIT 1

");

$query->execute([

    $id_ujian,
    $tahun['id_tahun'],
    $id_kelas

]);

$ujian = $query->fetch(PDO::FETCH_ASSOC);

if (!$ujian) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Ujian tidak tersedia.";

    header("Location: index.php");

    exit;

}

?>

<div class="wrapper">

<?php require_once "../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

🔑 Masukkan Token Ujian

</h4>

</div>

<div class="card-body">

<?php

if (isset($_SESSION['pesan'])) {

?>

<div class="alert alert-<?= $_SESSION['warna']; ?> alert-dismissible fade show">

<?= $_SESSION['pesan']; ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

<?php

unset($_SESSION['pesan']);
unset($_SESSION['warna']);

}

?>

<div class="row mb-4">

<div class="col-md-6">

<table class="table table-borderless">

<tr>

<th width="180">

Nama Ujian

</th>

<td>

:

</td>

<td>

<strong>

<?= htmlspecialchars($ujian['nama_ujian']); ?>

</strong>

</td>

</tr>

<tr>

<th>

Paket Soal

</th>

<td>

:

</td>

<td>

<?= htmlspecialchars($ujian['nama_paket']); ?>

</td>

</tr>

<tr>

<th>

Durasi

</th>

<td>

:

</td>

<td>

<?= (int)$ujian['durasi']; ?> Menit

</td>

</tr>

<tr>

<th>

Jenis Penilaian

</th>

<td>

:

</td>

<td>

<?= htmlspecialchars($ujian['jenis_penilaian']); ?>

</td>

</tr>

</table>

</div>

</div>

<form
action="proses_token.php"
method="POST"
autocomplete="off">

<input
type="hidden"
name="id_ujian"
value="<?= $ujian['id_ujian']; ?>">

<div class="mb-3">

<label class="form-label">

Token Ujian

<span class="text-danger">*</span>

</label>

<input
type="text"
name="token"
class="form-control form-control-lg text-center"
maxlength="10"
placeholder="Masukkan Token"
style="text-transform:uppercase;"
required>

<div class="form-text">

Masukkan token yang diberikan oleh guru.

</div>

</div>

<div class="d-flex justify-content-between">

<a
href="index.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

<button
type="submit"
class="btn btn-success">

<i class="bi bi-key-fill"></i>

Validasi Token

</button>

</div>

</form>

</div>

</div>

