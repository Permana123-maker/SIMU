<?php

$page_title = "Daftar Peserta Ujian";

require_once "../template/header.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Ujian
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    header("Location:../ujian/index.php");
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

    header("Location:../ujian/index.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Data Peserta
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

SELECT

    siswa.*

FROM peserta_ujian

INNER JOIN siswa
ON peserta_ujian.id_siswa=siswa.id

WHERE peserta_ujian.id_ujian=?

ORDER BY siswa.nama ASC

");

$stmt->execute([$id_ujian]);

$peserta = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

<div class="card shadow-sm border-0">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="bi bi-people-fill"></i>

Daftar Peserta Ujian

</h5>

<div>

<a
href="peserta.php?id=<?= $id_ujian; ?>"
class="btn btn-light btn-sm">

<i class="bi bi-pencil-square"></i>

Kelola Peserta

</a>

</div>

</div>

<div class="card-body">

<h5>

<?= htmlspecialchars($ujian['nama_ujian']); ?>

</h5>

<p class="text-muted">

<?= htmlspecialchars($ujian['nama_mapel']); ?>

</p>

<hr>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th width="70">No</th>

<th>NIS</th>

<th>Nama Siswa</th>

<th>Kelas</th>

</tr>

</thead>

<tbody>

<?php

$no=1;

foreach($peserta as $row):

?>

<tr>

<td><?= $no++; ?></td>

<td><?= htmlspecialchars($row['nis']); ?></td>

<td><?= htmlspecialchars($row['nama']); ?></td>

<td><?= htmlspecialchars($row['kelas']); ?></td>

</tr>

<?php endforeach; ?>

<?php if(count($peserta)==0){ ?>

<tr>

<td colspan="4" class="text-center">

Belum ada peserta yang dipilih.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<a
href="../ujian/index.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

</div>

</div>

</div>

<?php

require_once "../template/footer.php";

?>