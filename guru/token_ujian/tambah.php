<?php

/*
|--------------------------------------------------------------------------
| Session & Database
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Tahun Ajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun ajaran aktif.";

    header("Location: ../dashboard/index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Daftar Ujian
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

u.id_ujian,
u.nama_ujian,
k.nama_kelas

FROM ujian u

INNER JOIN kelas k
ON u.id_kelas = k.id_kelas

WHERE

u.id_tahun = :id_tahun

AND u.dihapus_pada IS NULL

ORDER BY

k.nama_kelas ASC,
u.nama_ujian ASC

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_tahun',
    $tahun['id_tahun'],
    PDO::PARAM_INT
);

$query->execute();

$dataUjian = $query->fetchAll(PDO::FETCH_ASSOC);

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

🔑 Tambah Token Ujian

</h4>

<small>

Membuat token ujian baru

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

<div class="mb-3">

<label class="form-label">

Pilih Ujian

<span class="text-danger">*</span>

</label>

<select
name="id_ujian"
class="form-select"
required>

<option value="">

-- Pilih Ujian --

</option>

<?php foreach ($dataUjian as $ujian) { ?>

<option value="<?= $ujian['id_ujian']; ?>">

<?= htmlspecialchars($ujian['nama_kelas']); ?>

-

<?= htmlspecialchars($ujian['nama_ujian']); ?>

</option>

<?php } ?>

</select>

<div class="form-text">

Pilih ujian yang akan diberikan token.

</div>

</div>

<div class="mb-3">

<label class="form-label">

Status Token

</label>

<select
name="status"
class="form-select">

<option value="Nonaktif" selected>

Nonaktif

</option>

<option value="Aktif">

Aktif

</option>

</select>

<div class="form-text">

Token dapat diaktifkan sekarang atau nanti.

</div>

</div>

<hr>

<div class="alert alert-info">

<i class="bi bi-info-circle-fill"></i>

<strong>Informasi</strong>

<ul class="mb-0 mt-2">

<li>Token ujian akan dibuat otomatis oleh sistem.</li>

<li>Setiap token bersifat unik.</li>

<li>Token digunakan siswa sebelum memulai ujian.</li>

</ul>

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

Simpan Token

</button>

</div>

</form>

</div>

</div>

</div>

</div>

<?php require_once "../template/footer.php"; ?>