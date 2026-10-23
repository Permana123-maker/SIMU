<?php

/*
|--------------------------------------------------------------------------
| Session, Database & Template
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Token
|--------------------------------------------------------------------------
*/

if (

    !isset($_GET['id_token']) ||
    !is_numeric($_GET['id_token'])

) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data token ujian tidak ditemukan.";

    header("Location: index.php");
    exit;

}

$id_token = (int) $_GET['id_token'];

/*
|--------------------------------------------------------------------------
| Ambil Tahun Ajaran Aktif
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
| Ambil Data Token
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

t.id_token,
t.id_ujian,
t.token,
t.status,

u.nama_ujian,

k.nama_kelas

FROM token_ujian t

INNER JOIN ujian u
ON t.id_ujian = u.id_ujian

INNER JOIN kelas k
ON u.id_kelas = k.id_kelas

WHERE

t.id_token = :id_token

AND t.dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_token',
    $id_token,
    PDO::PARAM_INT
);

$query->execute();

$data = $query->fetch(PDO::FETCH_ASSOC);

if (!$data) {

    $_SESSION['warna'] = "warning";
    $_SESSION['pesan'] = "Data token ujian tidak ditemukan.";

    header("Location: index.php");
    exit;

}

?>

<div class="wrapper">

<?php require_once "../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-warning text-dark">

<div class="d-flex justify-content-between align-items-center">

<div>

<h4 class="mb-1">

🔑 Edit Token Ujian

</h4>

<small>

Mengubah status token ujian

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
action="proses_edit.php"
autocomplete="off">

<input
type="hidden"
name="id_token"
value="<?= $data['id_token']; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Nama Ujian

</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($data['nama_ujian']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Kelas

</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($data['nama_kelas']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Token Ujian

</label>

<input
type="text"
class="form-control fw-bold text-center"
value="<?= htmlspecialchars($data['token']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status Token

<span class="text-danger">*</span>

</label>

<select
name="status"
class="form-select"
required>

<option
value="Aktif"
<?= $data['status'] == 'Aktif' ? 'selected' : ''; ?>>

Aktif

</option>

<option
value="Nonaktif"
<?= $data['status'] == 'Nonaktif' ? 'selected' : ''; ?>>

Nonaktif

</option>

</select>

<div class="form-text">

Hanya token dengan status <strong>Aktif</strong> yang dapat digunakan siswa untuk memulai ujian.

</div>

</div>

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
class="btn btn-warning">

<i class="bi bi-save"></i>

Simpan Perubahan

</button>

</div>

</form>

</div>

</div>

</div>

</div>

<?php require_once "../template/footer.php"; ?>