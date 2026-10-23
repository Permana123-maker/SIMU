<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Edit Soal
|--------------------------------------------------------------------------
*/

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/tahun_ajaran.php";

$page_title = "Edit Soal";

require_once "../../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Soal
|--------------------------------------------------------------------------
*/

$id_soal = isset($_GET['id_soal'])
    ? (int) $_GET['id_soal']
    : 0;

if ($id_soal <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data soal tidak ditemukan.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Belum ada Tahun Pelajaran aktif.";

    header("Location: ../../dashboard/index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Soal
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    s.*,

    p.nama_paket,

    p.jenjang

FROM soal s

INNER JOIN paket_soal p

ON p.id_paket = s.id_paket

WHERE

    s.id_soal = :id_soal

AND

    p.id_tahun = :id_tahun

AND

    s.dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_soal',
    $id_soal,
    PDO::PARAM_INT
);

$query->bindValue(
    ':id_tahun',
    $tahun['id_tahun'],
    PDO::PARAM_INT
);

$query->execute();

$soal = $query->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if (!$soal) {

    $_SESSION['warna'] = "warning";

    $_SESSION['pesan'] = "Soal tidak ditemukan.";

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

<div class="card-header bg-warning text-dark">

<h4 class="mb-0">

<i class="bi bi-pencil-square"></i>

Edit Soal

</h4>

</div>

<div class="card-body">

<form
    action="proses_edit.php"
    method="POST">

<input
    type="hidden"
    name="id_soal"
    value="<?= $soal['id_soal']; ?>">

<input
    type="hidden"
    name="id_paket"
    value="<?= $soal['id_paket']; ?>">

<!-- ==========================================================
| Informasi Paket
=========================================================== -->

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Paket Soal

</label>

<input
    type="text"
    class="form-control"
    value="<?= htmlspecialchars($soal['nama_paket']); ?>"
    readonly>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Jenjang

</label>

<input
    type="text"
    class="form-control"
    value="<?= htmlspecialchars($soal['jenjang']); ?>"
    readonly>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Jenis Soal

</label>

<input
    type="text"
    class="form-control"
    value="<?= htmlspecialchars($soal['jenis']); ?>"
    readonly>

</div>

</div>

<!-- ==========================================================
| Nomor dan Kode Soal
=========================================================== -->

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Nomor Soal

</label>

<input
    type="text"
    class="form-control"
    value="<?= htmlspecialchars($soal['nomor']); ?>"
    readonly>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Kode Soal

</label>

<input
    type="text"
    class="form-control"
    value="<?= htmlspecialchars($soal['kode_soal']); ?>"
    readonly>

</div>

</div>

<!-- ==========================================================
| Pertanyaan
=========================================================== -->

<div class="mb-4">

<label class="form-label">

Pertanyaan

</label>

<textarea

    name="pertanyaan"

    id="pertanyaan"

    class="form-control"

    rows="7"

    required><?= htmlspecialchars($soal['pertanyaan']); ?></textarea>

<small class="text-muted">

Mendukung penulisan rumus menggunakan sintaks MathJax.

</small>

</div>

<!-- ==========================================================
| Preview Pertanyaan
=========================================================== -->

<div class="card border-primary mb-4">

<div class="card-header bg-primary text-white">

Preview Pertanyaan

</div>

<div
    class="card-body"
    id="previewPertanyaan">

<?= $soal['pertanyaan']; ?>

</div>

</div>

<?php

/*
|--------------------------------------------------------------------------
| Lanjut Bagian 3
|--------------------------------------------------------------------------
*/

?>

<?php

/*
|--------------------------------------------------------------------------
| Tombol Aksi
|--------------------------------------------------------------------------
*/
?>

<div class="d-flex justify-content-between mt-4">

    <a
        href="index.php?id_paket=<?= $soal['id_paket']; ?>"
        class="btn btn-secondary">

        <i class="bi bi-arrow-left"></i>

        Kembali

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="bi bi-save"></i>

        Simpan Perubahan

    </button>

</div>

</form>

</div>

</div>

</div>

</div>

<script>

/*
|--------------------------------------------------------------------------
| Preview Pertanyaan
|--------------------------------------------------------------------------
*/

const pertanyaan = document.getElementById("pertanyaan");

const preview = document.getElementById("previewPertanyaan");

function updatePreview() {

    preview.innerHTML = pertanyaan.value;

    if (window.MathJax) {

        MathJax.typesetPromise([preview]);

    }

}

pertanyaan.addEventListener("input", updatePreview);

updatePreview();

</script>

<?php require_once "../../template/footer.php"; ?>

