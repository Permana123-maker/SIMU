```php
<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Data Ujian
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun pelajaran aktif.";

    header("Location: ../dashboard/index.php");
    exit;

}

$id_tahun = (int) $tahun['id_tahun'];

/*
|--------------------------------------------------------------------------
| Pencarian
|--------------------------------------------------------------------------
*/

$cari = trim($_GET['cari'] ?? "");

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$batas = 10;

$halaman = isset($_GET['halaman'])
    ? max(1, (int) $_GET['halaman'])
    : 1;

$mulai = ($halaman - 1) * $batas;

/*
|--------------------------------------------------------------------------
| Hitung Total Data
|--------------------------------------------------------------------------
*/

$sqlTotal = "

    SELECT
        COUNT(u.id_ujian) AS total

    FROM ujian u

    INNER JOIN kelas k
        ON u.id_kelas = k.id_kelas

    INNER JOIN paket_soal ps
        ON u.id_paket = ps.id_paket

    WHERE
        u.id_tahun = :id_tahun

    AND
        u.dihapus_pada IS NULL

";

if ($cari !== "") {

    $sqlTotal .= "

        AND (

            u.nama_ujian LIKE :cari

            OR k.nama_kelas LIKE :cari

            OR ps.nama_paket LIKE :cari

        )

    ";

}

$queryTotal = $pdo->prepare($sqlTotal);

$queryTotal->bindValue(
    ":id_tahun",
    $id_tahun,
    PDO::PARAM_INT
);

if ($cari !== "") {

    $queryTotal->bindValue(
        ":cari",
        "%" . $cari . "%"
    );

}

$queryTotal->execute();

$totalData = (int) (
    $queryTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0
);

$totalHalaman = $totalData > 0
    ? (int) ceil($totalData / $batas)
    : 1;

/*
|--------------------------------------------------------------------------
| Koreksi Halaman
|--------------------------------------------------------------------------
*/

if ($halaman > $totalHalaman) {

    $halaman = $totalHalaman;

    $mulai = ($halaman - 1) * $batas;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Ujian
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT

        u.id_ujian,
        u.nama_ujian,
        u.durasi,
        u.jenis_penilaian,
        u.bobot_pg,
        u.bobot_uraian,
        u.status,

        u.acak_soal,
        u.acak_pilihan,
        u.fullscreen,
        u.anti_cheat,
        u.maksimal_pelanggaran,

        k.nama_kelas,

        ps.nama_paket

    FROM ujian u

    INNER JOIN kelas k
        ON u.id_kelas = k.id_kelas

    INNER JOIN paket_soal ps
        ON u.id_paket = ps.id_paket

    WHERE

        u.id_tahun = :id_tahun

    AND

        u.dihapus_pada IS NULL

";

if ($cari !== "") {

    $sql .= "

        AND (

            u.nama_ujian LIKE :cari

            OR k.nama_kelas LIKE :cari

            OR ps.nama_paket LIKE :cari

        )

    ";

}

$sql .= "

    ORDER BY

        u.id_ujian DESC

    LIMIT :mulai, :batas

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ":id_tahun",
    $id_tahun,
    PDO::PARAM_INT
);

if ($cari !== "") {

    $query->bindValue(
        ":cari",
        "%" . $cari . "%"
    );

}

$query->bindValue(
    ":mulai",
    $mulai,
    PDO::PARAM_INT
);

$query->bindValue(
    ":batas",
    $batas,
    PDO::PARAM_INT
);

$query->execute();

$dataUjian = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="wrapper">

    <?php require_once "../template/sidebar.php"; ?>

    <div class="content">

        <?php require_once "../template/topbar.php"; ?>

        <div class="container-fluid mt-4">

            <div class="card shadow border-0">

                <!-- ======================================================
                     HEADER
                ======================================================= -->

                <div class="card-header bg-primary text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h4 class="mb-1">
                                📋 Data Ujian
                            </h4>

                            <small>

                                Tahun Pelajaran

                                <strong>
                                    <?= htmlspecialchars($tahun['nama_tahun']); ?>
                                </strong>

                            </small>

                        </div>

                        <div>

                            <a
                                href="tambah.php"
                                class="btn btn-light">

                                <i class="bi bi-plus-circle"></i>

                                Tambah Ujian

                            </a>

                        </div>

                    </div>

                </div>

                <!-- ======================================================
                     BODY
                ======================================================= -->

                <div class="card-body">

                    <!-- ==================================================
                         PESAN
                    =================================================== -->

                    <?php if (isset($_SESSION['pesan'])) { ?>

                        <div
                            class="alert alert-<?= htmlspecialchars($_SESSION['warna'] ?? 'info'); ?> alert-dismissible fade show">

                            <?= htmlspecialchars($_SESSION['pesan']); ?>

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

                    <!-- ==================================================
                         PENCARIAN
                    =================================================== -->

                    <form
                        method="GET"
                        class="row g-2 mb-4">

                        <div class="col-md-5">

                            <input
                                type="text"
                                name="cari"
                                class="form-control"
                                placeholder="Cari nama ujian, kelas, atau paket soal..."
                                value="<?= htmlspecialchars($cari); ?>">

                        </div>

                        <div class="col-md-auto">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-search"></i>

                                Cari

                            </button>

                        </div>

                        <div class="col-md-auto">

                            <a
                                href="index.php"
                                class="btn btn-secondary">

                                Reset

                            </a>

                        </div>

                    </form>

                    <!-- ==================================================
                         INFORMASI
                    =================================================== -->

                    <div class="alert alert-info">

                        <i class="bi bi-info-circle"></i>

                        <strong>Informasi:</strong>

                        Ujian berstatus
                        <strong>Aktif</strong>
                        dapat dikerjakan oleh siswa.

                        Ujian berstatus
                        <strong>Nonaktif</strong>
                        tidak dapat dimulai oleh siswa.

                    </div>

                    <!-- ==================================================
                         TABLE
                    =================================================== -->

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th width="60">
                                        No
                                    </th>

                                    <th>
                                        Nama Ujian
                                    </th>

                                    <th width="120">
                                        Kelas
                                    </th>

                                    <th>
                                        Paket Soal
                                    </th>

                                    <th width="90">
                                        Durasi
                                    </th>

                                    <th width="130">
                                        Jenis
                                    </th>

                                    <th width="110">
                                        Status
                                    </th>

                                    <th width="250">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (count($dataUjian) > 0) { ?>

                                    <?php

                                    $no = $mulai + 1;

                                    foreach ($dataUjian as $ujian) {

                                    ?>

                                        <tr>

                                            <!-- NOMOR -->

                                            <td class="text-center">

                                                <?= $no++; ?>

                                            </td>

                                            <!-- NAMA UJIAN -->

                                            <td>

                                                <strong>

                                                    <?= htmlspecialchars(
                                                        $ujian['nama_ujian']
                                                    ); ?>

                                                </strong>

                                            </td>

                                            <!-- KELAS -->

                                            <td class="text-center">

                                                <?= htmlspecialchars(
                                                    $ujian['nama_kelas']
                                                ); ?>

                                            </td>

                                            <!-- PAKET SOAL -->

                                            <td>

                                                <?= htmlspecialchars(
                                                    $ujian['nama_paket']
                                                ); ?>

                                            </td>

                                            <!-- DURASI -->

                                            <td class="text-center">

                                                <?= (int) $ujian['durasi']; ?>

                                                Menit

                                            </td>

                                            <!-- JENIS -->

                                            <td class="text-center">

                                                <?= htmlspecialchars(
                                                    $ujian['jenis_penilaian']
                                                ); ?>

                                                <br>

                                                <small class="text-muted">

                                                    PG:
                                                    <?= (float) $ujian['bobot_pg']; ?>%

                                                    <?php if (
                                                        (float) $ujian['bobot_uraian'] > 0
                                                    ) { ?>

                                                        <br>

                                                        Uraian:
                                                        <?= (float) $ujian['bobot_uraian']; ?>%

                                                    <?php } ?>

                                                </small>

                                            </td>

                                            <!-- STATUS -->

                                            <td class="text-center">

                                                <?php if (
                                                    $ujian['status'] === "Aktif"
                                                ) { ?>

                                                    <span class="badge bg-success">

                                                        <i class="bi bi-check-circle"></i>

                                                        Aktif

                                                    </span>

                                                <?php } else { ?>

                                                    <span class="badge bg-secondary">

                                                        <i class="bi bi-pause-circle"></i>

                                                        Nonaktif

                                                    </span>

                                                <?php } ?>

                                            </td>

                                            <!-- AKSI -->

                                            <td>

                                                <div class="d-flex flex-wrap gap-1 justify-content-center">

                                                    <!-- DETAIL -->

                                                    <a
                                                        href="detail.php?id_ujian=<?= (int) $ujian['id_ujian']; ?>"
                                                        class="btn btn-info btn-sm"
                                                        title="Detail">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                    <!-- EDIT -->

                                                    <a
                                                        href="edit.php?id_ujian=<?= (int) $ujian['id_ujian']; ?>"
                                                        class="btn btn-warning btn-sm"
                                                        title="Edit">

                                                        <i class="bi bi-pencil-square"></i>

                                                    </a>

                                                    <!-- AKTIF / NONAKTIF -->

                                                    <?php if (
                                                        $ujian['status'] === "Aktif"
                                                    ) { ?>

                                                        <a
                                                            href="ubah_status.php?id_ujian=<?= (int) $ujian['id_ujian']; ?>&status=Nonaktif"
                                                            class="btn btn-secondary btn-sm"
                                                            title="Nonaktifkan"
                                                            onclick="return confirm('Yakin ingin menonaktifkan ujian ini? Siswa tidak dapat memulai ujian ini.')">

                                                            <i class="bi bi-pause-circle"></i>

                                                        </a>

                                                    <?php } else { ?>

                                                        <a
                                                            href="ubah_status.php?id_ujian=<?= (int) $ujian['id_ujian']; ?>&status=Aktif"
                                                            class="btn btn-success btn-sm"
                                                            title="Aktifkan"
                                                            onclick="return confirm('Yakin ingin mengaktifkan ujian ini? Siswa dapat memulai ujian ini.')">

                                                            <i class="bi bi-play-circle"></i>

                                                        </a>

                                                    <?php } ?>

                                                    <!-- HAPUS -->

                                                    <a
                                                        href="hapus.php?id_ujian=<?= (int) $ujian['id_ujian']; ?>"
                                                        class="btn btn-danger btn-sm"
                                                        title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus ujian ini?')">

                                                        <i class="bi bi-trash"></i>

                                                    </a>

                                                </div>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                <?php } else { ?>

                                    <tr>

                                        <td
                                            colspan="8"
                                            class="text-center text-muted py-4">

                                            <i class="bi bi-inbox fs-3"></i>

                                            <br>

                                            Belum ada data ujian.

                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                    <!-- ==================================================
                         PAGINATION
                    =================================================== -->

                    <?php if ($totalHalaman > 1) { ?>

                        <nav class="mt-4">

                            <ul class="pagination justify-content-center">

                                <!-- SEBELUMNYA -->

                                <li
                                    class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">

                                    <a
                                        class="page-link"
                                        href="?halaman=<?= max(1, $halaman - 1); ?>&cari=<?= urlencode($cari); ?>">

                                        &laquo;

                                    </a>

                                </li>

                                <!-- NOMOR HALAMAN -->

                                <?php for (
                                    $i = 1;
                                    $i <= $totalHalaman;
                                    $i++
                                ) { ?>

                                    <li
                                        class="page-item <?= ($i == $halaman) ? 'active' : ''; ?>">

                                        <a
                                            class="page-link"
                                            href="?halaman=<?= $i; ?>&cari=<?= urlencode($cari); ?>">

                                            <?= $i; ?>

                                        </a>

                                    </li>

                                <?php } ?>

                                <!-- BERIKUTNYA -->

                                <li
                                    class="page-item <?= ($halaman >= $totalHalaman) ? 'disabled' : ''; ?>">

                                    <a
                                        class="page-link"
                                        href="?halaman=<?= min($totalHalaman, $halaman + 1); ?>&cari=<?= urlencode($cari); ?>">

                                        &raquo;

                                    </a>

                                </li>

                            </ul>

                        </nav>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once "../template/footer.php"; ?>

</body>

</html>
```
