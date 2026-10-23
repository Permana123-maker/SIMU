<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/tahun_ajaran.php";
require_once "../../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Kelas
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id_kelas']) || !is_numeric($_GET['id_kelas'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

$id_kelas = (int) $_GET['id_kelas'];

/*
|--------------------------------------------------------------------------
| Ambil Tahun Ajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun ajaran aktif.";

    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_kelas,
        nama_kelas
    FROM kelas
    WHERE
        id_kelas = ?
        AND id_tahun = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_kelas,
    $tahun['id_tahun']
]);

$kelas = $query->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

?>

<div class="wrapper">

<?php require_once "../../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-success text-white">

<div class="d-flex justify-content-between align-items-center">

<div>

<h4 class="mb-1">

📥 Import Data Siswa

</h4>

<small>

Import siswa ke kelas

<strong><?= htmlspecialchars($kelas['nama_kelas']); ?></strong>

</small>

</div>

<a
href="index.php?id_kelas=<?= $kelas['id_kelas']; ?>"
class="btn btn-light">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

</div>

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

<div class="alert alert-info">

<h6>

<i class="bi bi-info-circle"></i>

Petunjuk Import

</h6>

<ol class="mb-0">

<li>Unduh template Excel.</li>

<li>Isi data sesuai format.</li>

<li>Kolom <strong>JK</strong> hanya berisi <strong>L</strong> atau <strong>P</strong>.</li>

<li>Nomor Peserta dibuat otomatis.</li>

<li>Password awal = Nomor Peserta.</li>

<li>Status otomatis = Aktif.</li>

</ol>

</div>

<div class="mb-4">

<a
href="template_import.xlsx"
class="btn btn-outline-success">

<i class="bi bi-download"></i>

Download Template Excel

</a>

</div>

<form
action="proses_import.php"
method="POST"
enctype="multipart/form-data">

<input
type="hidden"
name="id_kelas"
value="<?= $kelas['id_kelas']; ?>">

<input
type="hidden"
name="id_tahun"
value="<?= $tahun['id_tahun']; ?>">

<div class="mb-3">

<label class="form-label">

File Excel

<span class="text-danger">*</span>

</label>

<input
type="file"
name="file_excel"
class="form-control"
accept=".xlsx,.xls"
required>

<small class="text-muted">

Format yang didukung:
.xls dan .xlsx

</small>

</div>

<hr>

<div class="d-flex justify-content-between">

<a
href="index.php?id_kelas=<?= $kelas['id_kelas']; ?>"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

<button
type="submit"
class="btn btn-success">

<i class="bi bi-upload"></i>

Import Data

</button>

</div>

</form>

</div>

</div>

<div class="card shadow border-0 mt-4">

<div class="card-header">

<h5 class="mb-0">

Format Template Excel

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead class="table-light">

<tr>

<th width="120">

NIS

</th>

<th>

Nama

</th>

<th width="120">

JK

</th>

</tr>

</thead>

<tbody>

<tr>

<td>

240001

</td>

<td>

Ahmad Fauzi

</td>

<td>

L

</td>

</tr>

<tr>

<td>

240002

</td>

<td>

Siti Aisyah

</td>

<td>

P

</td>

</tr>

</tbody>

</table>

</div>

<div class="alert alert-warning mt-3 mb-0">

<ul class="mb-0">

<li>Baris pertama wajib berisi <strong>header</strong>.</li>

<li>Urutan kolom harus <strong>NIS, Nama, JK</strong>.</li>

<li>Kolom JK hanya boleh berisi <strong>L</strong> atau <strong>P</strong>.</li>

<li>Data akan dimasukkan ke kelas <strong><?= htmlspecialchars($kelas['nama_kelas']); ?></strong>.</li>

<li>Nomor Peserta dibuat otomatis oleh sistem.</li>

<li>Password awal sama dengan Nomor Peserta.</li>

<li>Status siswa otomatis <strong>Aktif</strong>.</li>

</ul>

</div>

</div>

</div>

</div>

</div>

<?php require_once "../../template/footer.php"; ?>