<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/tahun_ajaran.php";
require_once "../../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Kelas
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id_kelas']) || !is_numeric($_GET['id_kelas'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

$id_kelas = (int) $_GET['id_kelas'];

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
| Ambil Data Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_kelas,
        nama_kelas
    FROM kelas
    WHERE
        id_kelas = ?
        AND id_tahun = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_kelas,
    $tahun['id_tahun']
]);

$kelas = $query->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Kelas tidak ditemukan.";

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

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between align-items-center">

<div>

<h4 class="mb-1">

👨‍🎓 Tambah Siswa

</h4>

<small>

Menambahkan siswa baru (pindahan) ke kelas
<strong><?= htmlspecialchars($kelas['nama_kelas']); ?></strong>

</small>

</div>

<a
href="index.php?id_kelas=<?= $kelas['id_kelas']; ?>"
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
action="proses_tambah.php"
autocomplete="off">

<input
type="hidden"
name="id_kelas"
value="<?= $kelas['id_kelas']; ?>">

<input
type="hidden"
name="id_tahun"
value="<?= $tahun['id_tahun']; ?>">

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

<option value="">-- Pilih --</option>

<option value="L">

Laki-laki

</option>

<option value="P">

Perempuan

</option>

</select>

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
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Kelas

</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($kelas['nama_kelas']); ?>"
readonly>

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

</div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Status

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Aktif"
                                readonly>

                            <small class="text-muted">

                                Nomor peserta dan password akan dibuat otomatis oleh sistem.

                            </small>

                        </div>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <a
                            href="index.php?id_kelas=<?= $kelas['id_kelas']; ?>"
                            class="btn btn-secondary">

                            <i class="bi bi-arrow-left"></i>

                            Kembali

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-save"></i>

                            Simpan Siswa

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<?php require_once "../../template/footer.php"; ?>