<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Edit Tahun Pelajaran
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

$page_title = "Edit Tahun Pelajaran";

require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

$id_tahun = (int) ($_GET['id'] ?? 0);

if ($id_tahun <= 0) {

    $_SESSION['pesan'] = "Data Tahun Pelajaran tidak ditemukan.";

    $_SESSION['warna'] = "danger";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    SELECT

        id_tahun,
        nama_tahun,
        aktif

    FROM tahun_ajaran

    WHERE id_tahun = ?

    AND dihapus_pada IS NULL

    LIMIT 1

");

$stmt->execute([

    $id_tahun

]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Data Tidak Ada
|--------------------------------------------------------------------------
*/

if (!$data) {

    $_SESSION['pesan'] = "Data Tahun Pelajaran tidak ditemukan.";

    $_SESSION['warna'] = "danger";

    header("Location: index.php");

    exit;

}

?>

<div class="container-fluid">

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-warning text-dark">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h4 class="mb-1">

                        <i class="bi bi-pencil-square"></i>

                        Edit Tahun Pelajaran

                    </h4>

                    <small>

                        Perbarui data Tahun Pelajaran yang dipilih.

                    </small>

                </div>

                <a href="index.php"

                    class="btn btn-dark mt-3 mt-md-0">

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>

            </div>

        </div>

        <div class="card-body">

            <form
                action="proses_edit.php"
                method="POST"
                autocomplete="off">

                <input
                    type="hidden"
                    name="id_tahun"
                    value="<?= $data['id_tahun']; ?>">

                    <div class="row">

    <!-- ====================================================== -->
    <!-- Nama Tahun Pelajaran -->
    <!-- ====================================================== -->

    <div class="col-lg-6 mb-4">

        <label for="nama_tahun" class="form-label">

            Nama Tahun Pelajaran

            <span class="text-danger">*</span>

        </label>

        <input
            type="text"
            id="nama_tahun"
            name="nama_tahun"
            class="form-control"
            value="<?= htmlspecialchars($data['nama_tahun']); ?>"
            maxlength="9"
            pattern="^\d{4}/\d{4}$"
            required>

        <div class="form-text">

            Format penulisan:
            <strong>2026/2027</strong>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- Status Aktif -->
    <!-- ====================================================== -->

    <div class="col-lg-6 mb-4">

        <label for="aktif" class="form-label">

            Jadikan Tahun Pelajaran Aktif?

            <span class="text-danger">*</span>

        </label>

        <select
            id="aktif"
            name="aktif"
            class="form-select"
            required>

            <option value="Ya"
                <?= ($data['aktif'] == 'Ya') ? 'selected' : ''; ?>>

                Ya

            </option>

            <option value="Tidak"
                <?= ($data['aktif'] == 'Tidak') ? 'selected' : ''; ?>>

                Tidak

            </option>

        </select>

        <div class="form-text">

            Jika memilih <strong>Ya</strong>, maka Tahun Pelajaran lain akan dinonaktifkan secara otomatis.

        </div>

    </div>

</div>

<hr>

<!-- ====================================================== -->
<!-- TOMBOL AKSI -->
<!-- ====================================================== -->

<div class="d-flex justify-content-end gap-2 flex-wrap">

    <a href="index.php"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left-circle"></i>

        Kembali

    </a>

    <button
        type="reset"
        class="btn btn-warning">

        <i class="bi bi-arrow-counterclockwise"></i>

        Reset

    </button>

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

<?php

require_once "../template/footer.php";

?>