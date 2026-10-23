<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Modul Tahun Pelajaran
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

$page_title = "Tahun Pelajaran";

require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Ambil Data Tahun Pelajaran
|--------------------------------------------------------------------------
*/

try {

    $sql = "

        SELECT

            id_tahun,
            nama_tahun,
            aktif

        FROM tahun_ajaran

        WHERE dihapus_pada IS NULL

        ORDER BY id_tahun DESC

    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    $tahun_pelajaran = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_data = count($tahun_pelajaran);

} catch (PDOException $e) {

    $tahun_pelajaran = [];

    $total_data = 0;

}

/*
|--------------------------------------------------------------------------
| Notifikasi
|--------------------------------------------------------------------------
*/

$pesan = $_SESSION['pesan'] ?? null;

$warna = $_SESSION['warna'] ?? "success";

unset($_SESSION['pesan']);

unset($_SESSION['warna']);

?>

<div class="container-fluid">

    <!-- ====================================================== -->
    <!-- HEADER -->
    <!-- ====================================================== -->

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h4 class="mb-1">

                        <i class="bi bi-calendar3"></i>

                        Tahun Pelajaran

                    </h4>

                    <small>

                        Kelola data Tahun Pelajaran yang digunakan pada SIMU.

                    </small>

                </div>

                <a href="tambah.php"

                    class="btn btn-light mt-3 mt-md-0">

                    <i class="bi bi-plus-circle"></i>

                    Tambah Tahun Pelajaran

                </a>

            </div>

        </div>

        <div class="card-body">

        <!-- ====================================================== -->
<!-- NOTIFIKASI -->
<!-- ====================================================== -->

<?php if ($pesan): ?>

    <div class="alert alert-<?= htmlspecialchars($warna) ?> alert-dismissible fade show mb-4">

        <i class="bi bi-check-circle-fill me-2"></i>

        <?= htmlspecialchars($pesan) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>

<!-- ====================================================== -->
<!-- SEARCH -->
<!-- ====================================================== -->

<div class="row mb-4">

    <div class="col-lg-4 col-md-6">

        <div class="input-group">

            <span class="input-group-text">

                <i class="bi bi-search"></i>

            </span>

            <input
                type="text"
                id="searchTable"
                class="form-control"
                placeholder="Cari Tahun Pelajaran...">

        </div>

    </div>

    <div class="col-lg-8 col-md-6 text-md-end mt-3 mt-md-0">

        <span class="badge bg-primary">

            Total Data :
            <?= number_format($total_data) ?>

        </span>

    </div>

</div>

<!-- ====================================================== -->
<!-- TABEL -->
<!-- ====================================================== -->

<div class="table-responsive">

<table
    class="table table-hover table-bordered align-middle">

    <thead class="table-primary">

        <tr>

            <th
                width="70"
                class="text-center">

                No

            </th>

            <th>

                Tahun Pelajaran

            </th>

            <th
                width="150"
                class="text-center">

                Status

            </th>

            <th
                width="220"
                class="text-center">

                Aksi

            </th>

        </tr>

    </thead>

    <tbody>

    <?php if ($total_data > 0): ?>

    <?php $no = 1; ?>

    <?php foreach ($tahun_pelajaran as $data): ?>

        <tr>

            <td class="text-center">

                <?= $no++; ?>

            </td>

            <td>

                <strong>

                    <?= htmlspecialchars($data['nama_tahun']); ?>

                </strong>

            </td>

            <td class="text-center">

                <?php if ($data['aktif'] == "Ya"): ?>

                    <span class="badge bg-success">

                        <i class="bi bi-check-circle-fill"></i>

                        Aktif

                    </span>

                <?php else: ?>

                    <span class="badge bg-secondary">

                        <i class="bi bi-pause-circle-fill"></i>

                        Nonaktif

                    </span>

                <?php endif; ?>

            </td>

            <td class="text-center">

            <!-- Tombol Edit -->

<a href="edit.php?id=<?= $data['id_tahun']; ?>"
   class="btn btn-warning btn-sm mb-1">

    <i class="bi bi-pencil-square"></i>

    Edit

</a>

<?php if ($data['aktif'] == "Tidak"): ?>

    <!-- Tombol Aktifkan -->

    <a href="aktifkan.php?id=<?= $data['id_tahun']; ?>"
       class="btn btn-success btn-sm mb-1"
       onclick="return confirm('Yakin ingin mengaktifkan Tahun Pelajaran ini? Tahun Pelajaran yang sedang aktif akan dinonaktifkan secara otomatis.');">

        <i class="bi bi-check-circle"></i>

        Aktifkan

    </a>

    <!-- Tombol Hapus -->

    <a href="hapus.php?id=<?= $data['id_tahun']; ?>"
       class="btn btn-danger btn-sm mb-1"
       onclick="return confirm('Data yang dihapus tidak dapat digunakan kembali. Lanjutkan?');">

        <i class="bi bi-trash"></i>

        Hapus

    </a>

<?php endif; ?>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>

        <td colspan="4" class="text-center py-5">

            <i class="bi bi-calendar-x display-4 text-secondary"></i>

            <h5 class="mt-3">

                Belum Ada Data Tahun Pelajaran

            </h5>

            <p class="text-muted mb-3">

                Silakan tambahkan Tahun Pelajaran terlebih dahulu.

            </p>

            <a href="tambah.php"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>

                Tambah Tahun Pelajaran

            </a>

        </td>

    </tr>

<?php endif; ?>

    </tbody>

</table>

</div>

<!-- ====================================================== -->
<!-- FOOTER -->
<!-- ====================================================== -->

<div class="d-flex justify-content-between align-items-center flex-wrap mt-4">

    <small class="text-muted">

        Total Data :

        <strong>

            <?= number_format($total_data); ?>

        </strong>

        Tahun Pelajaran

    </small>

    <small class="text-muted">

        <?= NAMA_APLIKASI; ?>

        |

        Versi <?= VERSI_APLIKASI; ?>

    </small>

</div>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- PENCARIAN TABEL -->
<!-- ====================================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const search = document.getElementById("searchTable");

    const tbody = document.querySelector("tbody");

    if (!search || !tbody) return;

    search.addEventListener("keyup", function () {

        const keyword = this.value.toLowerCase();

        tbody.querySelectorAll("tr").forEach(function (row) {

            row.style.display = row.innerText.toLowerCase().includes(keyword)

                ? ""

                : "none";

        });

    });

});

</script>

<?php

require_once "../template/footer.php";

?>