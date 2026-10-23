<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Mengambil Tahun Pelajaran
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (empty($tahun)) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun pelajaran yang tersedia.";

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

                <div class="card-header bg-primary text-white">

                    <h4 class="mb-1">

                        🏫 Tambah Kelas

                    </h4>

                    <small>

                        Tambahkan kelas baru pada tahun pelajaran yang sedang digunakan.

                    </small>

                </div>

                <div class="card-body">

                    <form action="proses_tambah.php" method="POST">

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
                                placeholder="Contoh : VII 1"
                                autocomplete="off"
                                required>

                            <small class="text-muted">

                                Gunakan format:
                                <strong>VII 1</strong>,
                                <strong>VII 2</strong>,
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
                                class="btn btn-primary">

                                <i class="bi bi-floppy"></i>

                                Simpan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once "../template/footer.php"; ?>