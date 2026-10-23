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

FROM token_ujian t

INNER JOIN ujian u
    ON t.id_ujian = u.id_ujian

WHERE

u.id_tahun = :id_tahun

AND t.dihapus_pada IS NULL

AND u.dihapus_pada IS NULL

";

if ($cari != "") {

    $sqlTotal .= "

    AND
    (
        u.nama_ujian LIKE :cari
        OR t.token LIKE :cari
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
| Ambil Data Token
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

t.id_token,
t.token,
t.status,
t.dibuat_pada,

u.id_ujian,
u.nama_ujian,

k.nama_kelas

FROM token_ujian t

INNER JOIN ujian u
ON t.id_ujian = u.id_ujian

INNER JOIN kelas k
ON u.id_kelas = k.id_kelas

WHERE

u.id_tahun = :id_tahun

AND t.dihapus_pada IS NULL

AND u.dihapus_pada IS NULL

";

if ($cari != "") {

    $sql .= "

    AND
    (
        u.nama_ujian LIKE :cari
        OR t.token LIKE :cari
    )

    ";

}

$sql .= "

ORDER BY

t.id_token DESC

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

$dataToken = $query->fetchAll(PDO::FETCH_ASSOC);

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

🔑 Token Ujian

</h4>

<small>

Tahun Pelajaran

<strong>

<?= htmlspecialchars($tahun['nama_tahun']); ?>

</strong>

</small>

</div>

<div>

<a
href="tambah.php"
class="btn btn-warning">

<i class="bi bi-plus-circle"></i>

Tambah Token

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
placeholder="Cari nama ujian atau token..."
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

Nama Ujian

</th>

<th width="150">

Kelas

</th>

<th width="170">

Token

</th>

<th width="120">

Status

</th>

<th width="180">

Dibuat Pada

</th>

<th width="240">

Aksi

</th>

</tr>

</thead>

<tbody>

<?php

if (count($dataToken) > 0) {

    $no = $mulai + 1;

    foreach ($dataToken as $token) {

?>

<tr>

<td class="text-center">

<?= $no++; ?>

</td>

<td>

<strong>

<?= htmlspecialchars($token['nama_ujian']); ?>

</strong>

</td>

<td class="text-center">

<?= htmlspecialchars($token['nama_kelas']); ?>

</td>

<td class="text-center">

<span class="badge bg-dark fs-6">

<?= htmlspecialchars($token['token']); ?>

</span>

</td>

<td class="text-center">

<?php if ($token['status'] == 'Aktif') { ?>

<span class="badge bg-success">

Aktif

</span>

<?php } else { ?>

<span class="badge bg-secondary">

Nonaktif

</span>

<?php } ?>

</td>

<td class="text-center">

<?= date('d-m-Y H:i', strtotime($token['dibuat_pada'])); ?>

</td>

<td>

<div class="d-flex gap-2">

<a
href="detail.php?id_token=<?= $token['id_token']; ?>"
class="btn btn-info btn-sm">

<i class="bi bi-eye"></i>

Detail

</a>

<a
href="edit.php?id_token=<?= $token['id_token']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

Edit

</a>

<a
href="hapus.php?id_token=<?= $token['id_token']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin ingin menghapus token ujian ini?')">

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
colspan="7"
class="text-center text-muted py-4">

Belum ada data token ujian.

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