<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| PREVIEW IMPORT SOAL
|--------------------------------------------------------------------------
| Menampilkan hasil validasi sebelum data disimpan
|--------------------------------------------------------------------------
*/

$page_title = "Preview Import Soal";

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';

require_once __DIR__ . '/../../app/keamanan/session.php';

require_once __DIR__ . '/../../app/database/database.php';

require_once __DIR__ . '/../template/header.php';

require_once __DIR__ . '/../template/sidebar.php';

require_once __DIR__ . '/../template/topbar.php';

/*
|--------------------------------------------------------------------------
| VALIDASI SESSION
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['import_soal']) ||
    !isset($_SESSION['import_soal_info'])
) {

    $_SESSION['error'] = "Tidak ada data yang dapat dipreview.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| AMBIL DATA SESSION
|--------------------------------------------------------------------------
*/

$dataImport = $_SESSION['import_soal'];

$info = $_SESSION['import_soal_info'];

$jumlahData = $info['jumlah_data'];

$jumlahValid = $info['jumlah_valid'];

$jumlahError = $info['jumlah_error'];

$idTahun = $info['id_tahun'];

$namaTahun = $info['nama_tahun'];

$jenjang = $info['jenjang'];

$namaFile = $info['nama_file'];

$tanggalImport = $info['tanggal_import'];

?>

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-search"></i>

                Preview Import Soal

            </h5>

        </div>

        <div class="card-body">

        <div class="row mb-4">

    <div class="col-md-3">

        <div class="alert alert-primary mb-0">

            <strong>Tahun Pelajaran</strong>

            <br>

            <?= htmlspecialchars($namaTahun); ?>

        </div>

    </div>

    <div class="col-md-2">

        <div class="alert alert-info mb-0">

            <strong>Jenjang</strong>

            <br>

            <?= htmlspecialchars($jenjang); ?>

        </div>

    </div>

    <div class="col-md-2">

        <div class="alert alert-success mb-0">

            <strong>Total Soal</strong>

            <br>

            <?= $jumlahData; ?>

        </div>

    </div>

    <div class="col-md-2">

        <div class="alert alert-success mb-0">

            <strong>Valid</strong>

            <br>

            <?= $jumlahValid; ?>

        </div>

    </div>

    <div class="col-md-3">

        <div class="alert <?= $jumlahError > 0 ? 'alert-danger' : 'alert-success'; ?> mb-0">

            <strong>Error</strong>

            <br>

            <?= $jumlahError; ?>

        </div>

    </div>

</div>

<div class="mb-4">

    <table class="table table-bordered">

        <tr>

            <th width="220">

                Nama File

            </th>

            <td>

                <?= htmlspecialchars($namaFile); ?>

            </td>

        </tr>

        <tr>

            <th>

                Waktu Import

            </th>

            <td>

                <?= htmlspecialchars($tanggalImport); ?>

            </td>

        </tr>

    </table>

</div>

<hr>

<div class="table-responsive">

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-primary text-center">

            <tr>

                <th width="60">No</th>

                <th width="90">Jenis</th>

                <th>Pertanyaan</th>

                <th width="80">A</th>

                <th width="80">B</th>

                <th width="80">C</th>

                <th width="80">D</th>

                <th width="80">Kunci</th>

                <th width="80">Bobot</th>

                <th width="100">Status</th>

                <th width="260">Keterangan</th>

            </tr>

        </thead>

        <tbody>

        <?php foreach ($dataImport as $soal): ?>

            <tr>

                <td class="text-center">

                    <?= $soal['nomor']; ?>

                </td>

                <td class="text-center">

                    <?= htmlspecialchars($soal['jenis']); ?>

                </td>

                <td>

                    <?= htmlspecialchars($soal['pertanyaan']); ?>

                </td>

                <td>

                    <?= htmlspecialchars($soal['pilihan_a']); ?>

                </td>

                <td>

                    <?= htmlspecialchars($soal['pilihan_b']); ?>

                </td>

                <td>

                    <?= htmlspecialchars($soal['pilihan_c']); ?>

                </td>

                <td>

                    <?= htmlspecialchars($soal['pilihan_d']); ?>

                </td>

                <td class="text-center">

                    <?= htmlspecialchars($soal['kunci']); ?>

                </td>

                <td class="text-center">

                    <?= $soal['bobot']; ?>

                </td>

                <td class="text-center">

                    <?php if ($soal['status'] == 'VALID'): ?>

                        <span class="badge bg-success">

                            VALID

                        </span>

                    <?php else: ?>

                        <span class="badge bg-danger">

                            ERROR

                        </span>

                    <?php endif; ?>

                </td>

                <td>

                    <?= htmlspecialchars($soal['keterangan']); ?>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<hr>

<form action="proses_import.php" method="POST">

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">

                <i class="bi bi-folder-plus"></i>

                Informasi Paket Soal

            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Kode Paket <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="kode_paket"
                        class="form-control"
                        maxlength="20"
                        placeholder="Contoh : VII-PTS-01"
                        required>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Nama Paket Soal <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama_paket"
                        class="form-control"
                        maxlength="150"
                        placeholder="Contoh : PTS Semester Ganjil"
                        required>

                </div>

            </div>

            <div class="row">

                <div class="col-md-3 mb-3">

                    <label class="form-label">

                        Jenjang

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($jenjang); ?>"
                        readonly>

                    <input
                        type="hidden"
                        name="jenjang"
                        value="<?= htmlspecialchars($jenjang); ?>">

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Tahun Pelajaran

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($namaTahun); ?>"
                        readonly>

                    <input
                        type="hidden"
                        name="id_tahun"
                        value="<?= $idTahun; ?>">

                </div>

                <div class="col-md-5 mb-3">

                    <label class="form-label">

                        Keterangan

                    </label>

                    <input
                        type="text"
                        name="keterangan"
                        class="form-control"
                        maxlength="255"
                        placeholder="Opsional">

                </div>

            </div>

        </div>

    </div>

            <div class="card-footer text-end">

            <a href="index.php"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left"></i>

                Kembali

            </a>

            <?php if ($jumlahError > 0): ?>

                <button
                    type="button"
                    class="btn btn-danger"
                    disabled>

                    <i class="bi bi-x-circle"></i>

                    Perbaiki File Excel Terlebih Dahulu

                </button>

            <?php else: ?>

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="bi bi-check-circle"></i>

                    Simpan Import

                </button>

            <?php endif; ?>

        </div>

    </div>

</form>

</div>

<?php

require_once __DIR__ . '/../template/footer.php';

?>