<?php

/*
|--------------------------------------------------------------------------
| IMPORT DATA SISWA
|--------------------------------------------------------------------------
| SIMU - Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
| Versi Final
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../../app/keamanan/session.php';
require_once __DIR__ . '/../../app/database/database.php';

$page_title = "Import Data Siswa";

require_once __DIR__ . '/../template/header.php';
require_once __DIR__ . '/../template/sidebar.php';
require_once __DIR__ . '/../template/topbar.php';

/*
|--------------------------------------------------------------------------
| AMBIL TAHUN PELAJARAN AKTIF
|--------------------------------------------------------------------------
*/

try {

    $sql = "
        SELECT
            id_tahun,
            nama_tahun
        FROM tahun_ajaran
        WHERE aktif = 'Ya'
        AND dihapus_pada IS NULL
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $tahunAktif = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tahunAktif) {

        throw new Exception(
            "Belum ada Tahun Pelajaran yang aktif."
        );

    }

} catch (Exception $e) {

    die($e->getMessage());

}

/*
|--------------------------------------------------------------------------
| AMBIL DAFTAR KELAS
|--------------------------------------------------------------------------
*/

try {

    $sql = "
        SELECT
            id_kelas,
            nama_kelas
        FROM kelas
        WHERE id_tahun = :id_tahun
        AND dihapus_pada IS NULL
        ORDER BY nama_kelas ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ':id_tahun',
        $tahunAktif['id_tahun'],
        PDO::PARAM_INT
    );

    $stmt->execute();

    $daftarKelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die($e->getMessage());

}

?>

<div class="container-fluid">

    <!-- ====================================================== -->
    <!-- HEADER -->
    <!-- ====================================================== -->

    <div class="row mb-4">

        <div class="col-lg-12">

            <h2 class="fw-bold">

                <i class="bi bi-person-lines-fill"></i>

                Import Data Siswa

            </h2>

            <p class="text-muted mb-0">

                Import data siswa berdasarkan kelas menggunakan file Excel.

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

   <div class="card border-0 shadow-sm">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <i class="bi bi-upload"></i>

            Form Import Data Siswa

        </h5>

    </div>

    <div class="card-body">

        <form
            action="upload.php"
            method="POST"
            enctype="multipart/form-data">

            <!-- ====================================================== -->
            <!-- TAHUN PELAJARAN -->
            <!-- ====================================================== -->

            <div class="mb-3">

                <label class="form-label fw-semibold">

                    Tahun Pelajaran Aktif

                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($tahunAktif['nama_tahun']); ?>"
                    readonly>

                <input
                    type="hidden"
                    name="id_tahun"
                    value="<?= $tahunAktif['id_tahun']; ?>">

            </div>

            <!-- ====================================================== -->
            <!-- PILIH KELAS -->
            <!-- ====================================================== -->

            <div class="mb-3">

                <label
                    for="id_kelas"
                    class="form-label fw-semibold">

                    Pilih Kelas

                </label>

                <select
                    name="id_kelas"
                    id="id_kelas"
                    class="form-select"
                    required>

                    <option value="">

                        -- Pilih Kelas --

                    </option>

                    <?php foreach ($daftarKelas as $kelas) : ?>

                        <option
                            value="<?= $kelas['id_kelas']; ?>">

                            <?= htmlspecialchars($kelas['nama_kelas']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <small class="text-muted">

                    Pilih kelas tujuan sebelum melakukan upload data siswa.

                </small>

            </div>

            <!-- ====================================================== -->
            <!-- TEMPLATE -->
            <!-- ====================================================== -->

            <div class="mb-3">

                <label class="form-label fw-semibold">

                    Template Excel

                </label>

                <div>

                    <a
                        href="template.php"
                        class="btn btn-success">

                        <i class="bi bi-download"></i>

                        Download Template

                    </a>

                </div>

                <small class="text-muted">

                    Gunakan template resmi SIMU agar format data sesuai.

                </small>

            </div>

            <!-- ====================================================== -->
            <!-- FILE EXCEL -->
            <!-- ====================================================== -->

            <div class="mb-4">

                <label
                    for="file_excel"
                    class="form-label fw-semibold">

                    File Excel

                </label>

                <input
                    type="file"
                    class="form-control"
                    id="file_excel"
                    name="file_excel"
                    accept=".xlsx,.xls"
                    required>

                <small class="text-muted">

                    Format file yang didukung adalah
                    <strong>.xlsx</strong>
                    dan
                    <strong>.xls</strong>.

                </small>

            </div>

            <!-- ====================================================== -->
            <!-- TOMBOL -->
            <!-- ====================================================== -->

            <div class="d-flex justify-content-between">

                <a
                    href="../dashboard/index.php"
                    class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-upload"></i>

                    Upload Data

                </button>

            </div>

        </form>

    </div>

</div>

<!-- ====================================================== -->
<!-- LANJUT BAGIAN 3 -->
<!-- ====================================================== -->
 
<div class="card border-0 shadow-sm mt-4">

    <div class="card-header bg-info text-white">

        <h5 class="mb-0">

            <i class="bi bi-info-circle"></i>

            Petunjuk Import Data Siswa

        </h5>

    </div>

    <div class="card-body">

        <ol class="mb-3">

            <li>
                Pastikan <strong>Tahun Pelajaran Aktif</strong> sudah benar.
            </li>

            <li>
                Pilih <strong>kelas tujuan</strong> sebelum mengunggah file Excel.
            </li>

            <li>
                Klik tombol <strong>Download Template</strong> dan isi data siswa menggunakan template tersebut.
            </li>

            <li>
                Template Excel hanya terdiri dari kolom:
                <ul class="mt-2">
                    <li>No</li>
                    <li>NIS</li>
                    <li>Nama Lengkap</li>
                    <li>Jenis Kelamin (L/P)</li>
                </ul>
            </li>

            <li>
                Jangan mengubah urutan kolom pada template Excel.
            </li>

            <li>
                Simpan file dalam format <strong>.xlsx</strong> atau <strong>.xls</strong>.
            </li>

            <li>
                Klik <strong>Upload Data</strong> untuk melihat preview sebelum data disimpan ke database.
            </li>

        </ol>

        <div class="alert alert-warning mb-0">

            <i class="bi bi-exclamation-triangle-fill"></i>

            <strong>Perhatian!</strong>

            Semua siswa pada file Excel akan dimasukkan ke
            <strong>kelas yang dipilih</strong>.
            Pastikan kelas sudah sesuai sebelum melanjutkan proses upload.

        </div>

    </div>

</div>

</div>

<?php

require_once __DIR__ . '/../template/footer.php';

?>

