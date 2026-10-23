<?php

require_once "../../app/keamanan/session.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Ambil Hasil Import
|--------------------------------------------------------------------------
*/

$berhasil = $_SESSION['import_berhasil'] ?? 0;
$gagal    = $_SESSION['import_gagal'] ?? 0;
$error    = $_SESSION['import_error'] ?? [];

unset($_SESSION['import_berhasil']);
unset($_SESSION['import_gagal']);
unset($_SESSION['import_error']);

?>

<div class="wrapper">

<?php require_once "../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-success text-white">

<h4 class="mb-0">

📥 Hasil Import Soal

</h4>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<div class="card border-success mb-3">

<div class="card-body text-center">

<h1 class="text-success">

<?= $berhasil; ?>

</h1>

<h5>

Soal Berhasil Diimport

</h5>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card border-danger mb-3">

<div class="card-body text-center">

<h1 class="text-danger">

<?= $gagal; ?>

</h1>

<h5>

Soal Gagal Diimport

</h5>

</div>

</div>

</div>

</div>

<?php if(count($error)>0){ ?>

<div class="alert alert-warning">

<h5>

Daftar Kesalahan

</h5>

<hr>

<ol class="mb-0">

<?php foreach($error as $e){ ?>

<li>

<?= htmlspecialchars($e); ?>

</li>

<?php } ?>

</ol>

</div>

<?php }else{ ?>

<div class="alert alert-success">

Semua soal berhasil diimport.

</div>

<?php } ?>

<div class="d-flex justify-content-between">

<a

href="index.php"

class="btn btn-primary">

Import Lagi

</a>

<a

href="../soal/index.php"

class="btn btn-success">

Lihat Daftar Soal

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<?php require_once "../template/footer.php"; ?>