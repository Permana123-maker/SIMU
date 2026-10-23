<?php

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
| Pencarian
|--------------------------------------------------------------------------
*/

$cari = trim($_GET['cari'] ?? "");

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$batas = 10;

$halaman = isset($_GET['halaman'])
    ? max(1, (int) $_GET['halaman'])
    : 1;

$mulai = ($halaman - 1) * $batas;

/*
|--------------------------------------------------------------------------
| Hitung Total Data
|--------------------------------------------------------------------------
*/

$sqlTotal = "

SELECT
COUNT(*) AS total

FROM paket_soal

WHERE

id_tahun = :id_tahun

AND dihapus_pada IS NULL

";

if ($cari != "") {

    $sqlTotal .= "

    AND
    (
        nama_paket LIKE :cari
        OR jenjang LIKE :cari
    )

    ";

}

$queryTotal = $pdo->prepare($sqlTotal);

$queryTotal->bindValue(
    ':id_tahun',
    $tahun['id_tahun'],
    PDO::PARAM_INT
);

if ($cari != "") {

    $queryTotal->bindValue(
        ':cari',
        "%{$cari}%"
    );

}

$queryTotal->execute();

$totalData = $queryTotal->fetch(PDO::FETCH_ASSOC)['total'];

$totalHalaman = ceil($totalData / $batas);

/*
|--------------------------------------------------------------------------
| Ambil Data Paket
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

p.id_paket,
p.nama_paket,
p.jenjang,
p.keterangan,
p.dibuat_pada,

(
SELECT COUNT(*)

FROM soal s

WHERE

s.id_paket = p.id_paket

AND s.dihapus_pada IS NULL

) AS jumlah_soal

FROM paket_soal p

WHERE

p.id_tahun = :id_tahun

AND p.dihapus_pada IS NULL

";

if ($cari != "") {

    $sql .= "

    AND
    (
        p.nama_paket LIKE :cari
        OR p.jenjang LIKE :cari
    )

    ";

}

$sql .= "

ORDER BY

FIELD(
p.jenjang,
'VII',
'VIII',
'IX'
),

p.nama_paket ASC

LIMIT :mulai,:batas

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_tahun',
    $tahun['id_tahun'],
    PDO::PARAM_INT
);

if ($cari != "") {

    $query->bindValue(
        ':cari',
        "%{$cari}%"
    );

}

$query->bindValue(
    ':mulai',
    $mulai,
    PDO::PARAM_INT
);

$query->bindValue(
    ':batas',
    $batas,
    PDO::PARAM_INT
);

$query->execute();

$dataPaket = $query->fetchAll(PDO::FETCH_ASSOC);

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

📚 Paket Soal

</h4>

<small>

Tahun Pelajaran

<strong>

<?= htmlspecialchars($tahun['nama_tahun']); ?>

</strong>

</small>

</div>

<div class="d-flex gap-2">

<a
href="../import_soal/index.php"
class="btn btn-light">

<i class="bi bi-upload"></i>

Import Soal

</a>

<a
href="tambah.php"
class="btn btn-warning">

<i class="bi bi-plus-circle"></i>

Tambah Paket

</a>

</div>

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
method="GET"
class="row mb-4">

<div class="col-md-4">

<input
type="text"
name="cari"
class="form-control"
placeholder="Cari nama paket..."
value="<?= htmlspecialchars($cari); ?>">

</div>

<div class="col-md-auto">

<button
class="btn btn-primary">

<i class="bi bi-search"></i>

Cari

</button>

</div>

<div class="col-md-auto">

<a
href="index.php"
class="btn btn-secondary">

Reset

</a>

</div>

</form>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-light">

<tr>

<th width="60">

No

</th>

<th>

Nama Paket

</th>

<th width="120">

Jenjang

</th>

<th width="120">

Jumlah Soal

</th>

<th width="250">

Dibuat Pada

</th>

<th width="250">

Aksi

</th>

</tr>

</thead>

<tbody>

<?php

if (count($dataPaket) > 0) {

    $no = $mulai + 1;

    foreach ($dataPaket as $paket) {

?>

<tr>

<td class="text-center">

<?= $no++; ?>

</td>

<td>

<strong>

<?= htmlspecialchars($paket['nama_paket']); ?>

</strong>

<?php

if (!empty($paket['keterangan'])) {

?>

<br>

<small class="text-muted">

<?= htmlspecialchars($paket['keterangan']); ?>

</small>

<?php

}

?>

</td>

<td class="text-center">

<span class="badge bg-primary">

<?= htmlspecialchars($paket['jenjang']); ?>

</span>

</td>

<td class="text-center">

<span class="badge bg-success">

<?= (int) $paket['jumlah_soal']; ?>

Soal

</span>

</td>

<td class="text-center">

<?= date('d-m-Y H:i', strtotime($paket['dibuat_pada'])); ?>

</td>

<td>

<div class="d-flex gap-2">

<a
href="soal/index.php?id_paket=<?= $paket['id_paket']; ?>"
class="btn btn-success btn-sm">

<i class="bi bi-journal-text"></i>

Kelola Soal

</a>

<a
href="edit.php?id_paket=<?= $paket['id_paket']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

Edit

</a>

<a
href="hapus.php?id_paket=<?= $paket['id_paket']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin ingin menghapus paket soal ini?')">

<i class="bi bi-trash"></i>

Hapus

</a>

</div>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td
colspan="6"
class="text-center text-muted py-4">

Belum ada data paket soal.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

if ($totalHalaman > 1) {

?>

<nav class="mt-4">

<ul class="pagination justify-content-center">

<?php

for ($i = 1; $i <= $totalHalaman; $i++) {

?>

<li class="page-item <?= $i == $halaman ? 'active' : ''; ?>">

<a
class="page-link"
href="?halaman=<?= $i; ?>&cari=<?= urlencode($cari); ?>">

<?= $i; ?>

</a>

</li>

<?php

}

?>

</ul>

</nav>

<?php

}

?>

</div>

</div>

</div>

</div>

<?php require_once "../template/footer.php"; ?>