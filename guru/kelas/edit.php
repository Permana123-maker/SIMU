<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: index.php");
    exit;
}

$id_kelas = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| Mengambil Tahun Pelajaran
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (empty($tahun)) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Tahun pelajaran tidak ditemukan.";

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Mengambil Data Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_kelas,
        nama_kelas
    FROM kelas
    WHERE
        id_kelas = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([$id_kelas]);

$data = $query->fetch(PDO::FETCH_ASSOC);

if (!$data) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

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

                    <h4 class="mb-1">

                        ✏ Edit Kelas

                    </h4>

                    <small>

                        Ubah data kelas pada tahun pelajaran yang sedang digunakan.

                    </small>

                </div>

                <div class="card-body">

                    <form action="proses_edit.php" method="POST">

                        <input
                            type="hidden"
                            name="id_kelas"
                            value="<?= $data['id_kelas']; ?>">

                        <input
                            type="hidden"
                            name="id_tahun"
                            value="<?= $tahun['id_tahun']; ?>">

                        <div class="mb-3">

                            <label class="form-label">

                                Tahun Pelajaran

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($tahun['nama_tahun']); ?>"
                                readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Nama Kelas
                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="nama_kelas"
                                class="form-control"
                                maxlength="10"
                                value="<?= htmlspecialchars($data['nama_kelas']); ?>"
                                autocomplete="off"
                                required>

                            <small class="text-muted">

                                Contoh:
                                <strong>VII 1</strong>,
                                <strong>VIII 5</strong>,
                                <strong>IX 3</strong>

                            </small>

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

                                <i class="bi bi-floppy"></i>

                                Simpan Perubahan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once "../template/footer.php"; ?>