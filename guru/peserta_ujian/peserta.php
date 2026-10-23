<?php

$page_title = "Kelola Peserta Ujian";

require_once "../template/header.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Ujian
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    header("Location:index.php");
    exit;

}

$id_ujian = intval($_GET['id']);

/*
|--------------------------------------------------------------------------
| Data Ujian
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

SELECT

    ujian.*,

    mapel.nama_mapel

FROM ujian

LEFT JOIN mapel
ON ujian.id_mapel = mapel.id

WHERE ujian.id=?

");

$stmt->execute([$id_ujian]);

$ujian = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$ujian){

    header("Location:index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Data Siswa
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("

SELECT *

FROM siswa

ORDER BY nama ASC

");

$siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Peserta Terpilih
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

SELECT id_siswa

FROM peserta_ujian

WHERE id_ujian=?

");

$stmt->execute([$id_ujian]);

$peserta = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<div class="container-fluid">

<div class="card shadow-sm">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="bi bi-people-fill"></i>

Kelola Peserta

</h5>

</div>

<div class="card-body">

<h5>

<?= htmlspecialchars($ujian['nama_ujian']); ?>

</h5>

<p>

<?= htmlspecialchars($ujian['nama_mapel']); ?>

</p>

<hr>

<form action="simpan_peserta.php" method="POST">

<input
type="hidden"
name="id_ujian"
value="<?= $id_ujian; ?>">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th width="60">

<input
type="checkbox"
id="checkAll">

</th>

<th>NIS</th>

<th>Nama Siswa</th>

<th>Kelas</th>

</tr>

</thead>

<tbody>

<?php foreach($siswa as $row): ?>

<tr>

<td>

<input
type="checkbox"
class="check-item"
name="peserta[]"
value="<?= $row['id']; ?>"

<?= in_array($row['id'],$peserta) ? "checked" : ""; ?>

>

</td>

<td>

<?= $row['nis']; ?>

</td>

<td>

<?= htmlspecialchars($row['nama']); ?>

</td>

<td>

<?= htmlspecialchars($row['kelas']); ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<button
class="btn btn-success">

<i class="bi bi-save"></i>

Simpan Peserta

</button>

<a
href="index.php"
class="btn btn-secondary">

Kembali

</a>

</form>

</div>

</div>

</div>

<?php

require_once "../template/footer.php";

?>