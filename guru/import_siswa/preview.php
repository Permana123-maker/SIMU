<?php

/*
|--------------------------------------------------------------------------
| PREVIEW IMPORT SISWA
|--------------------------------------------------------------------------
| SIMU - Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
| Menampilkan hasil pembacaan file Excel sebelum proses import
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../../app/keamanan/session.php';
require_once __DIR__ . '/../../app/database/database.php';

$page_title = "Preview Import Siswa";

require_once __DIR__ . '/../template/header.php';
require_once __DIR__ . '/../template/sidebar.php';
require_once __DIR__ . '/../template/topbar.php';

/*
|--------------------------------------------------------------------------
| VALIDASI SESSION
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['import_siswa']) ||
    empty($_SESSION['import_siswa'])
) {

    $_SESSION['error'] = "Tidak ada data yang akan dipreview.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| AMBIL DATA SESSION
|--------------------------------------------------------------------------
*/

$dataImport = $_SESSION['import_siswa'];

$idTahun = $_SESSION['import_id_tahun'] ?? 0;

$idKelas = $_SESSION['import_id_kelas'] ?? 0;

$namaKelas = $_SESSION['import_nama_kelas'] ?? '-';

/*
|--------------------------------------------------------------------------
| AMBIL NAMA TAHUN PELAJARAN
|--------------------------------------------------------------------------
*/

try {

    $sql = "
        SELECT
            nama_tahun
        FROM tahun_ajaran
        WHERE id_tahun = :id_tahun
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ':id_tahun',
        $idTahun,
        PDO::PARAM_INT
    );

    $stmt->execute();

    $tahun = $stmt->fetch(PDO::FETCH_ASSOC);

    $namaTahun = $tahun['nama_tahun'] ?? '-';

} catch (PDOException $e) {

    die($e->getMessage());

}

/*
|--------------------------------------------------------------------------
| HITUNG STATISTIK
|--------------------------------------------------------------------------
*/

$totalData = count($dataImport);

$totalValid = 0;

$totalError = 0;

foreach ($dataImport as $row) {

    if ($row['status'] === "VALID") {

        $totalValid++;

    } else {

        $totalError++;

    }

}

?>

<div class="container-fluid">

    <!-- ====================================================== -->
    <!-- HEADER -->
    <!-- ====================================================== -->

    <div class="row mb-4">

        <div class="col-lg-12">

            <h2 class="fw-bold">

                <i class="bi bi-table"></i>

                Preview Import Data Siswa

            </h2>

            <p class="text-muted mb-0">

                Periksa data sebelum disimpan ke database.

            </p>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- FLASH MESSAGE -->
    <!-- ====================================================== -->

    <?php if (isset($_SESSION['success'])) : ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?= htmlspecialchars($_SESSION['success']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <?= htmlspecialchars($_SESSION['error']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>

    <!-- ====================================================== -->
    <!-- LANJUT BAGIAN 2 -->
    <!-- ====================================================== -->

    <div class="row mb-4">

    <!-- ====================================================== -->
    <!-- INFORMASI TAHUN PELAJARAN -->
    <!-- ====================================================== -->

    <div class="col-md-6 mb-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <small class="text-muted d-block">

                    Tahun Pelajaran

                </small>

                <h4 class="fw-bold text-primary mb-0">

                    <?= htmlspecialchars($namaTahun); ?>

                </h4>

            </div>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- INFORMASI KELAS -->
    <!-- ====================================================== -->

    <div class="col-md-6 mb-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <small class="text-muted d-block">

                    Kelas Tujuan

                </small>

                <h4 class="fw-bold text-success mb-0">

                    <?= htmlspecialchars($namaKelas); ?>

                </h4>

            </div>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- STATISTIK -->
<!-- ====================================================== -->

<div class="row mb-4">

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body text-center">

                <small class="text-muted d-block">

                    Total Data

                </small>

                <h2 class="fw-bold text-primary mb-0">

                    <?= number_format($totalData); ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body text-center">

                <small class="text-muted d-block">

                    Data Valid

                </small>

                <h2 class="fw-bold text-success mb-0">

                    <?= number_format($totalValid); ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body text-center">

                <small class="text-muted d-block">

                    Data Error

                </small>

                <h2 class="fw-bold text-danger mb-0">

                    <?= number_format($totalError); ?>

                </h2>

            </div>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- TABEL PREVIEW -->
<!-- ====================================================== -->

<div class="card border-0 shadow-sm">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <i class="bi bi-file-earmark-spreadsheet"></i>

            Preview Data Siswa

        </h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th width="60">No</th>

                        <th width="80">Baris</th>

                        <th width="140">NIS</th>

                        <th>Nama Lengkap</th>

                        <th width="90">JK</th>

                        <th width="120">Status</th>

                        <th>Keterangan</th>

                    </tr>

                </thead>

                <tbody>

<!-- ====================================================== -->
<!-- LANJUT BAGIAN 3 -->
<!-- ====================================================== -->

<?php

$no = 1;

foreach ($dataImport as $row) :

?>

<tr>

    <td class="text-center">

        <?= $no++; ?>

    </td>

    <td class="text-center">

        <?= $row['baris']; ?>

    </td>

    <td>

        <?= htmlspecialchars($row['nis']); ?>

    </td>

    <td>

        <?= htmlspecialchars($row['nama']); ?>

    </td>

    <td class="text-center">

        <?= htmlspecialchars($row['jk']); ?>

    </td>

    <td class="text-center">

        <?php if ($row['status'] === "VALID") : ?>

            <span class="badge bg-success">

                VALID

            </span>

        <?php else : ?>

            <span class="badge bg-danger">

                ERROR

            </span>

        <?php endif; ?>

    </td>

    <td>

        <?php if ($row['status'] === "VALID") : ?>

            <span class="text-success">

                Siap diimport

            </span>

        <?php else : ?>

            <span class="text-danger">

                <?= htmlspecialchars($row['keterangan']); ?>

            </span>

        <?php endif; ?>

    </td>

</tr>

<?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <!-- ====================================================== -->
        <!-- KETERANGAN -->
        <!-- ====================================================== -->

        <div class="alert alert-info mt-4 mb-0">

            <h6 class="fw-bold">

                Keterangan

            </h6>

            <ul class="mb-0">

                <li>

                    <span class="badge bg-success">

                        VALID

                    </span>

                    berarti data siap diimport.

                </li>

                <li>

                    <span class="badge bg-danger">

                        ERROR

                    </span>

                    berarti data harus diperbaiki terlebih dahulu.

                </li>

                <li>

                    Data yang berstatus
                    <strong>ERROR</strong>
                    tidak akan diproses saat import.

                </li>

            </ul>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- LANJUT BAGIAN 4 -->
<!-- ====================================================== -->

<div class="card border-0 shadow-sm mt-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center">

            <!-- Tombol Kembali -->

            <a
                href="index.php"
                class="btn btn-secondary">

                <i class="bi bi-arrow-left"></i>

                Kembali

            </a>

            <!-- Tombol Import -->

            <?php if ($totalError > 0) : ?>

                <div class="text-end">

                    <div class="alert alert-warning py-2 px-3 mb-3">

                        <i class="bi bi-exclamation-triangle-fill"></i>

                        Masih terdapat
                        <strong><?= number_format($totalError); ?></strong>
                        data yang bermasalah.

                        <br>

                        Perbaiki file Excel terlebih dahulu sebelum melakukan import.

                    </div>

                    <button
                        type="button"
                        class="btn btn-success"
                        disabled>

                        <i class="bi bi-database-fill-add"></i>

                        Import Data

                    </button>

                </div>

            <?php else : ?>

                <form
                    action="proses_import.php"
                    method="POST">

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-database-fill-add"></i>

                        Import <?= number_format($totalValid); ?> Data

                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>

</div>

</div>

<?php

require_once __DIR__ . '/../template/footer.php';

?>