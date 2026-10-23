<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/tahun_ajaran.php";
require_once "../../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Paket
|--------------------------------------------------------------------------
*/

if (

    !isset($_GET['id_paket']) ||
    !is_numeric($_GET['id_paket'])

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Paket soal tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

$id_paket = (int) $_GET['id_paket'];

/*
|--------------------------------------------------------------------------
| Tahun Ajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun ajaran aktif.";

    header("Location: ../../dashboard/index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Paket
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_paket,
        id_tahun,
        jenjang,
        nama_paket,
        keterangan
    FROM paket_soal
    WHERE
        id_paket = ?
        AND id_tahun = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_paket,
    $tahun['id_tahun']
]);

$paket = $query->fetch(PDO::FETCH_ASSOC);

if (!$paket) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Data paket soal tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Pencarian
|--------------------------------------------------------------------------
*/

$cari = trim($_GET['cari'] ?? '');

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
    FROM soal
    WHERE
        id_paket = :id_paket
        AND dihapus_pada IS NULL
";

if ($cari != '') {

    $sqlTotal .= "
        AND
        (
            kode_soal LIKE :cari
            OR pertanyaan LIKE :cari
            OR jenis LIKE :cari
        )
    ";
}

$queryTotal = $pdo->prepare($sqlTotal);

$queryTotal->bindValue(
    ':id_paket',
    $id_paket,
    PDO::PARAM_INT
);

if ($cari != '') {

    $queryTotal->bindValue(
        ':cari',
        "%{$cari}%"
    );
}

$queryTotal->execute();

$totalData = (int) $queryTotal
    ->fetch(PDO::FETCH_ASSOC)['total'];

$totalHalaman = ceil(
    $totalData / $batas
);

/*
|--------------------------------------------------------------------------
| Ambil Data Soal
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id_soal,
        kode_soal,
        nomor,
        jenis,
        pertanyaan,
        bobot
    FROM soal
    WHERE
        id_paket = :id_paket
        AND dihapus_pada IS NULL
";

if ($cari != '') {

    $sql .= "
        AND
        (
            kode_soal LIKE :cari
            OR pertanyaan LIKE :cari
            OR jenis LIKE :cari
        )
    ";
}

$sql .= "
    ORDER BY nomor ASC
    LIMIT :mulai, :batas
";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_paket',
    $id_paket,
    PDO::PARAM_INT
);

if ($cari != '') {

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

$dataSoal = $query->fetchAll(
    PDO::FETCH_ASSOC
);

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

📝 Data Soal

</h4>

<small>

Paket :

<strong>

<?= htmlspecialchars($paket['nama_paket']); ?>

</strong>

|

Jenjang

<?= htmlspecialchars($paket['jenjang']); ?>

</small>

</div>

<a
href="../index.php"
class="btn btn-light">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

</div>

</div>

<div class="card-body">

<?php

/*
|--------------------------------------------------------------------------
| Flash Message
|--------------------------------------------------------------------------
*/

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

<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

    <div class="d-flex gap-2 flex-wrap">

        <a
            href="tambah.php?id_paket=<?= $paket['id_paket']; ?>"
            class="btn btn-primary">

            <i class="bi bi-plus-circle"></i>

            Tambah Soal

        </a>

        <a
            href="import.php?id_paket=<?= $paket['id_paket']; ?>"
            class="btn btn-success">

            <i class="bi bi-file-earmark-excel"></i>

            Import Excel

        </a>

        <a
            href="template.php"
            class="btn btn-info text-white">

            <i class="bi bi-download"></i>

            Download Template

        </a>

    </div>

</div>

<form
    method="GET"
    class="row mb-4">

    <input
        type="hidden"
        name="id_paket"
        value="<?= $paket['id_paket']; ?>">

    <div class="col-md-5">

        <input
            type="text"
            name="cari"
            class="form-control"
            placeholder="Cari kode soal, jenis atau pertanyaan..."
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
            href="index.php?id_paket=<?= $paket['id_paket']; ?>"
            class="btn btn-secondary">

            Reset

        </a>

    </div>

</form>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-light">

<tr>

<th width="60" class="text-center">

No

</th>

<th width="100" class="text-center">

Kode

</th>

<th width="90" class="text-center">

Nomor

</th>

<th width="120" class="text-center">

Jenis

</th>

<th>

Pertanyaan

</th>

<th width="90" class="text-center">

Bobot

</th>

<th width="170" class="text-center">

Aksi

</th>

</tr>

</thead>

<tbody>

<?php

if (count($dataSoal) > 0) {

    $no = $mulai + 1;

    foreach ($dataSoal as $soal) {

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Pertanyaan
        |--------------------------------------------------------------------------
        */

        $ringkasan = strip_tags($soal['pertanyaan']);

        $ringkasan = html_entity_decode(
            $ringkasan,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        if (mb_strlen($ringkasan) > 120) {

            $ringkasan =
                mb_substr($ringkasan, 0, 120) . "...";

        }

?>

<tr>

<td class="text-center">

<?= $no++; ?>

</td>

<td class="text-center">

<span class="badge bg-secondary">

<?= htmlspecialchars($soal['kode_soal']); ?>

</span>

</td>

<td class="text-center">

<?= (int) $soal['nomor']; ?>

</td>

<td class="text-center">

<?php

switch ($soal['jenis']) {

    case 'PG':

        echo '<span class="badge bg-primary">PG</span>';

        break;

    case 'ISIAN':

        echo '<span class="badge bg-warning text-dark">ISIAN</span>';

        break;

    case 'URAIAN':

        echo '<span class="badge bg-success">URAIAN</span>';

        break;

    default:

        echo '<span class="badge bg-secondary">-</span>';

        break;

}

?>

</td>

<td>

<?= nl2br(htmlspecialchars($ringkasan)); ?>

</td>

<td class="text-center">

<?= rtrim(rtrim($soal['bobot'], '0'), '.'); ?>

</td>

<td>

<div class="d-flex justify-content-center gap-2">

<a
href="edit.php?id=<?= $soal['id_soal']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

Edit

</a>

<a
href="hapus.php?id=<?= $soal['id_soal']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin ingin menghapus soal ini?')">

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
class="text-center py-5">

<div class="text-muted">

<i class="bi bi-journal-x fs-1 d-block mb-3"></i>

<h5>

Belum Ada Soal

</h5>

<p class="mb-0">

Silakan tambahkan soal secara manual atau melalui import Excel.

</p>

</div>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

/*
|--------------------------------------------------------------------------
| Informasi Data
|--------------------------------------------------------------------------
*/

?>

<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">

<div class="text-muted">

Menampilkan

<strong>

<?= count($dataSoal); ?>

</strong>

dari

<strong>

<?= $totalData; ?>

</strong>

soal.

</div>

<?php

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

if ($totalHalaman > 1) {

?>

<nav>

<ul class="pagination mb-0">

<li class="page-item <?= $halaman <= 1 ? 'disabled' : ''; ?>">

<a
class="page-link"
href="?id_paket=<?= $paket['id_paket']; ?>&halaman=<?= $halaman - 1; ?>&cari=<?= urlencode($cari); ?>">

&laquo;

</a>

</li>

<?php

for ($i = 1; $i <= $totalHalaman; $i++) {

?>

<li class="page-item <?= $halaman == $i ? 'active' : ''; ?>">

<a
class="page-link"
href="?id_paket=<?= $paket['id_paket']; ?>&halaman=<?= $i; ?>&cari=<?= urlencode($cari); ?>">

<?= $i; ?>

</a>

</li>

<?php

}

?>

<li class="page-item <?= $halaman >= $totalHalaman ? 'disabled' : ''; ?>">

<a
class="page-link"
href="?id_paket=<?= $paket['id_paket']; ?>&halaman=<?= $halaman + 1; ?>&cari=<?= urlencode($cari); ?>">

&raquo;

</a>

</li>

</ul>

</nav>

<?php

}

?>

</div>

</div>

</div>

</div>

<?php require_once "../../template/footer.php"; ?>