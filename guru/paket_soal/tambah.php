<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Ambil Tahun Ajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun ajaran aktif.";

    header("Location: ../dashboard/index.php");
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

<div class="d-flex justify-content-between align-items-center">

<div>

<h4 class="mb-1">

📚 Tambah Paket Soal

</h4>

<small>

Menambahkan paket soal baru

</small>

</div>

<a
href="index.php"
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

<form
method="POST"
action="proses_tambah.php"
autocomplete="off">

<input
type="hidden"
name="id_tahun"
value="<?= $tahun['id_tahun']; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Jenjang

<span class="text-danger">*</span>

</label>

<select
name="jenjang"
class="form-select"
required>

<option value="">

-- Pilih Jenjang --

</option>

<option value="VII">

VII

</option>

<option value="VIII">

VIII

</option>

<option value="IX">

IX

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Tahun Pelajaran

</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($tahun['nama_tahun']); ?>"
readonly>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Nama Paket

<span class="text-danger">*</span>

</label>

<input
type="text"
name="nama_paket"
class="form-control"
maxlength="100"
placeholder="Contoh : PTS Semester Ganjil"
autofocus
required>

<div class="form-text">

Contoh:
PTS Semester Ganjil,
PAS Semester Genap,
Latihan AKM.

</div>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Keterangan

</label>

<textarea
name="keterangan"
class="form-control"
rows="4"
maxlength="1000"
placeholder="Tambahkan keterangan paket soal (Opsional)"></textarea>

</div>

<hr>

<div class="d-flex justify-content-between">

<a
href="index.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

<button
type="submit"
class="btn btn-primary">

<i class="bi bi-save"></i>

Simpan Paket

</button>

</div>

</form>

</div>

</div>

</div>

</div>

<?php require_once "../template/footer.php"; ?>