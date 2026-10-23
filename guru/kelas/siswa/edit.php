<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/tahun_ajaran.php";
require_once "../../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Siswa
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id_siswa']) || !is_numeric($_GET['id_siswa'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data siswa tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

$id_siswa = (int) $_GET['id_siswa'];

/*
|--------------------------------------------------------------------------
| Ambil Tahun Ajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun ajaran aktif.";

    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Siswa
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        s.id_siswa,
        s.id_kelas,
        s.id_tahun,
        s.nis,
        s.nomor_peserta,
        s.nama,
        s.jk,
        s.status,
        k.nama_kelas
    FROM siswa s
    INNER JOIN kelas k
        ON k.id_kelas = s.id_kelas
    WHERE
        s.id_siswa = ?
        AND s.id_tahun = ?
        AND s.dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_siswa,
    $tahun['id_tahun']
]);

$siswa = $query->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data siswa tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_kelas,
        nama_kelas
    FROM kelas
    WHERE
        id_tahun = ?
        AND dihapus_pada IS NULL
    ORDER BY
        CASE
            WHEN nama_kelas LIKE 'VII%' THEN 1
            WHEN nama_kelas LIKE 'VIII%' THEN 2
            WHEN nama_kelas LIKE 'IX%' THEN 3
            ELSE 4
        END,
        nama_kelas ASC
");

$query->execute([
    $tahun['id_tahun']
]);

$daftarKelas = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="wrapper">

<?php require_once "../../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-warning">

<div class="d-flex justify-content-between align-items-center">

<div>

<h4 class="mb-1">

✏ Edit Siswa

</h4>

<small>

Mengubah data siswa

<strong><?= htmlspecialchars($siswa['nama']); ?></strong>

</small>

</div>

<a
href="index.php?id_kelas=<?= $siswa['id_kelas']; ?>"
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
        name="id_siswa"
        value="<?= $siswa['id_siswa']; ?>">

    <input
        type="hidden"
        name="id_kelas"
        value="<?= $siswa['id_kelas']; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

NIS

<span class="text-danger">*</span>

</label>

<input
type="text"
name="nis"
class="form-control"
maxlength="20"
value="<?= htmlspecialchars($siswa['nis']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Nomor Peserta

</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($siswa['nomor_peserta']); ?>"
readonly>

<small class="text-muted">

Nomor peserta tidak dapat diubah.

</small>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Nama Siswa

<span class="text-danger">*</span>

</label>

<input
type="text"
name="nama"
class="form-control"
maxlength="100"
value="<?= htmlspecialchars($siswa['nama']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Jenis Kelamin

<span class="text-danger">*</span>

</label>

<select
name="jk"
class="form-select"
required>

<option value="L" <?= $siswa['jk'] == 'L' ? 'selected' : ''; ?>>

Laki-laki

</option>

<option value="P" <?= $siswa['jk'] == 'P' ? 'selected' : ''; ?>>

Perempuan

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Kelas

<span class="text-danger">*</span>

</label>

<select
name="id_kelas"
class="form-select"
required>

<?php foreach ($daftarKelas as $kelas) : ?>

<option
value="<?= $kelas['id_kelas']; ?>"
<?= $kelas['id_kelas'] == $siswa['id_kelas'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($kelas['nama_kelas']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

<span class="text-danger">*</span>

</label>

<select
name="status"
class="form-select"
required>

<option
value="Aktif"
<?= $siswa['status'] == "Aktif" ? "selected" : ""; ?>>

Aktif

</option>

<option
value="Tidak Aktif"
<?= $siswa['status'] == "Tidak Aktif" ? "selected" : ""; ?>>

Tidak Aktif

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Tahun Pelajaran

</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($tahun['nama_tahun']); ?>"
readonly>

<div class="col-md-6 mb-3">

    <label class="form-label">
        Password Baru
    </label>

    <input
        type="password"
        name="password"
        class="form-control"
        minlength="6"
        autocomplete="new-password"
        placeholder="Kosongkan jika tidak ingin mengubah">

    <small class="text-muted">
        Isi hanya jika ingin mengganti password siswa.
        Minimal 6 karakter.
    </small>

</div>


</div>

<hr>

<div class="d-flex justify-content-between">

<a
href="index.php?id_kelas=<?= $siswa['id_kelas']; ?>"
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

<?php require_once "../../template/footer.php"; ?>