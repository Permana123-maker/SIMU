<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Tambah Soal
|--------------------------------------------------------------------------
*/

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Paket
|--------------------------------------------------------------------------
*/

$id_paket = isset($_GET['id_paket'])
    ? (int) $_GET['id_paket']
    : 0;

if ($id_paket <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Paket soal tidak valid.";

    header("Location: ../index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Paket
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    id_paket,
    nama_paket,
    jenjang

FROM paket_soal

WHERE

    id_paket = :id_paket

AND

    dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_paket',
    $id_paket,
    PDO::PARAM_INT
);

$query->execute();

$paket = $query->fetch(PDO::FETCH_ASSOC);

if (!$paket) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Paket soal tidak ditemukan.";

    header("Location: ../index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Template
|--------------------------------------------------------------------------
*/

$page_title = "Tambah Soal";

require_once "../../template/header.php";

?>

<div class="wrapper">

<?php require_once "../../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between align-items-center">

<div>

<h4 class="mb-1">

<i class="bi bi-plus-circle"></i>

Tambah Soal

</h4>

<small>

<?= htmlspecialchars($paket['jenjang']); ?>

—

<?= htmlspecialchars($paket['nama_paket']); ?>

</small>

</div>

<a
href="index.php?id_paket=<?= $id_paket; ?>"
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
action="proses_tambah.php"
method="POST">

<input
type="hidden"
name="id_paket"
value="<?= $id_paket; ?>">

<!-- ====================================================== -->
<!-- Jenis Soal -->
<!-- ====================================================== -->

<div class="mb-4">

    <label class="form-label fw-semibold">

        Jenis Soal

    </label>

    <select
        name="jenis"
        id="jenis_soal"
        class="form-select"
        required>

        <option value="">

            -- Pilih Jenis Soal --

        </option>

        <option value="PG">

            Pilihan Ganda

        </option>

        <option value="ISIAN">

            Isian Singkat

        </option>

        <option value="URAIAN">

            Uraian

        </option>

    </select>

</div>


<!-- ====================================================== -->
<!-- Pertanyaan -->
<!-- ====================================================== -->

<div class="mb-4">

    <label class="form-label fw-semibold">

        Pertanyaan

    </label>

    <textarea

        name="pertanyaan"

        id="pertanyaan"

        class="form-control"

        rows="8"

        placeholder="Tulis pertanyaan di sini. Gunakan sintaks LaTeX bila diperlukan."

        required></textarea>

</div>


<!-- ====================================================== -->
<!-- Preview Pertanyaan -->
<!-- ====================================================== -->

<div class="card border-info mb-4">

    <div class="card-header bg-info text-white">

        <strong>

            Preview Pertanyaan

        </strong>

    </div>

    <div
        class="card-body"
        id="previewPertanyaan">

        <span class="text-muted">

            Preview akan muncul di sini...

        </span>

    </div>

</div>


<!-- ====================================================== -->
<!-- Pilihan Ganda -->
<!-- ====================================================== -->

<div
    id="area_pg"
    style="display:none;">

    <div class="row">

        <div class="col-md-6 mb-3">

            <label class="form-label">

                Pilihan A

            </label>

            <input

                type="text"

                name="pilihan_a"

                id="pilihan_a"

                class="form-control">

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label">

                Pilihan B

            </label>

            <input

                type="text"

                name="pilihan_b"

                id="pilihan_b"

                class="form-control">

        </div>

    </div>

    <div class="row">

        <div class="col-md-6 mb-3">

            <label class="form-label">

                Pilihan C

            </label>

            <input

                type="text"

                name="pilihan_c"

                id="pilihan_c"

                class="form-control">

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label">

                Pilihan D

            </label>

            <input

                type="text"

                name="pilihan_d"

                id="pilihan_d"

                class="form-control">

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- ISIAN -->
<!-- ====================================================== -->

<div
    id="area_isian"
    style="display:none;">

    <div class="mb-4">

        <label class="form-label fw-semibold">

            Kunci Jawaban

        </label>

        <input

            type="text"

            name="kunci_jawaban"

            id="kunci_isian"

            class="form-control"

            placeholder="Masukkan jawaban isian">

    </div>

</div>


<!-- ====================================================== -->
<!-- URAIAN -->
<!-- ====================================================== -->

<div
    id="area_uraian"
    style="display:none;">

    <div class="mb-4">

        <label class="form-label fw-semibold">

            Pedoman Jawaban

        </label>

        <textarea

            name="jawaban_uraian"

            id="jawaban_uraian"

            class="form-control"

            rows="6"

            placeholder="Tuliskan pedoman jawaban uraian..."></textarea>

    </div>

</div>


<!-- ====================================================== -->
<!-- Kunci Jawaban PG -->
<!-- ====================================================== -->

<div
    id="area_kunci_pg"
    style="display:none;">

    <div class="mb-4">

        <label class="form-label fw-semibold">

            Kunci Jawaban

        </label>

        <select

            name="kunci_jawaban"

            id="kunci_pg"

            class="form-select">

            <option value="">

                -- Pilih Kunci Jawaban --

            </option>

            <option value="A">A</option>

            <option value="B">B</option>

            <option value="C">C</option>

            <option value="D">D</option>

        </select>

    </div>

</div>


<!-- ====================================================== -->
<!-- Bobot -->
<!-- ====================================================== -->

<div class="mb-4">

    <label class="form-label fw-semibold">

        Bobot Soal

    </label>

    <input

        type="number"

        name="bobot"

        class="form-control"

        min="1"

        value="1"

        required>

</div>


<!-- ====================================================== -->
<!-- Tombol -->
<!-- ====================================================== -->

<div class="d-flex justify-content-end gap-2">

    <a

        href="index.php?id_paket=<?= $id_paket; ?>"

        class="btn btn-secondary">

        <i class="bi bi-arrow-left"></i>

        Kembali

    </a>

    <button

        type="reset"

        class="btn btn-warning">

        <i class="bi bi-arrow-clockwise"></i>

        Reset

    </button>

    <button

        type="submit"

        class="btn btn-primary">

        <i class="bi bi-save"></i>

        Simpan Soal

    </button>

</div>

</form>

</div>

</div>

</div>

</div>

<?php require_once "../../template/footer.php"; ?>

<script>

/*
|--------------------------------------------------------------------------
| Preview MathJax
|--------------------------------------------------------------------------
*/

const pertanyaan = document.getElementById("pertanyaan");
const preview = document.getElementById("previewPertanyaan");

function updatePreview() {

    if (pertanyaan.value.trim() === "") {

        preview.innerHTML =
            '<span class="text-muted">Preview akan muncul di sini...</span>';

    } else {

        preview.innerHTML = pertanyaan.value;

    }

    if (window.MathJax) {

        MathJax.typesetPromise([preview]);

    }

}

pertanyaan.addEventListener("input", updatePreview);


/*
|--------------------------------------------------------------------------
| Tampilkan Form Berdasarkan Jenis Soal
|--------------------------------------------------------------------------
*/

const jenis = document.getElementById("jenis_soal");

const areaPG = document.getElementById("area_pg");
const areaIsian = document.getElementById("area_isian");
const areaUraian = document.getElementById("area_uraian");
const areaKunciPG = document.getElementById("area_kunci_pg");


const pilihanA = document.getElementById("pilihan_a");
const pilihanB = document.getElementById("pilihan_b");
const pilihanC = document.getElementById("pilihan_c");
const pilihanD = document.getElementById("pilihan_d");

const kunciPG = document.getElementById("kunci_pg");
const kunciIsian = document.getElementById("kunci_isian");
const jawabanUraian = document.getElementById("jawaban_uraian");


function resetRequired() {

    pilihanA.required = false;
    pilihanB.required = false;
    pilihanC.required = false;
    pilihanD.required = false;

    kunciPG.required = false;

    kunciIsian.required = false;

    jawabanUraian.required = false;

}


function tampilkanForm() {

    areaPG.style.display = "none";
    areaIsian.style.display = "none";
    areaUraian.style.display = "none";
    areaKunciPG.style.display = "none";

    resetRequired();

    switch (jenis.value) {

        case "PG":

            areaPG.style.display = "block";

            areaKunciPG.style.display = "block";

            pilihanA.required = true;
            pilihanB.required = true;
            pilihanC.required = true;
            pilihanD.required = true;

            kunciPG.required = true;

        break;

        case "ISIAN":

            areaIsian.style.display = "block";

            kunciIsian.required = true;

        break;

        case "URAIAN":

            areaUraian.style.display = "block";

            jawabanUraian.required = true;

        break;

    }

}

jenis.addEventListener("change", tampilkanForm);

updatePreview();

tampilkanForm();

</script>

