<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Login Siswa
|--------------------------------------------------------------------------
*/

session_start();

require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";

/*
|--------------------------------------------------------------------------
| Jika Sudah Login
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['siswa'])) {

    header("Location: ../dashboard/index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    die("Belum ada Tahun Pelajaran yang aktif.");

}

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Login Siswa | SIMU

</title>

<?php require_once "../template/header.php"; ?>

</head>

<body class="bg-light">

<div class="container">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow mt-5 border-0">

<div class="card-header bg-primary text-white text-center">

<h3 class="mb-1">

SIMU

</h3>

<small>

Sistem Informasi Manajemen Ujian Matematika

</small>

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
action="proses_login.php"
method="POST">

<input
type="hidden"
name="id_tahun"
value="<?= $tahun['id_tahun']; ?>">

<div class="mb-3">

<label class="form-label">

NIS

<span class="text-danger">*</span>

</label>

<input
type="text"
name="nis"
class="form-control"
maxlength="30"
autocomplete="username"
placeholder="Masukkan NIS"
required>

</div>

<div class="mb-3">

<label class="form-label">

Password

<span class="text-danger">*</span>

</label>

<input
type="password"
name="password"
class="form-control"
autocomplete="current-password"
placeholder="Masukkan Password"
required>

</div>

<div class="d-grid">

<button
type="submit"
class="btn btn-primary">

<i class="bi bi-box-arrow-in-right"></i>

Masuk

</button>

</div>

<hr>

<div class="text-center">

<small class="text-muted">

Tahun Pelajaran Aktif

<br>

<strong>

<?= htmlspecialchars($tahun['nama_tahun']); ?>

</strong>

</small>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php require_once "../template/footer.php"; ?>

</body>

</html>