<?php

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Mengambil Data Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        k.id_kelas,
        k.nama_kelas,
        ta.nama_tahun,
        COUNT(s.id_siswa) AS jumlah_siswa

    FROM kelas k

    INNER JOIN tahun_ajaran ta
        ON ta.id_tahun = k.id_tahun

    LEFT JOIN siswa s
        ON s.id_kelas = k.id_kelas
        AND s.dihapus_pada IS NULL

    WHERE
        k.dihapus_pada IS NULL

    GROUP BY
        k.id_kelas,
        k.nama_kelas,
        ta.nama_tahun

    ORDER BY

        CASE

            WHEN k.nama_kelas LIKE 'VII %' THEN 1
            WHEN k.nama_kelas LIKE 'VIII %' THEN 2
            WHEN k.nama_kelas LIKE 'IX %' THEN 3

            ELSE 4

        END,

        CAST(
            TRIM(
                SUBSTRING(
                    k.nama_kelas,
                    LOCATE(' ', k.nama_kelas) + 1
                )
            ) AS UNSIGNED
        ),

        k.nama_kelas ASC
");

$query->execute();

$dataKelas = $query->fetchAll(PDO::FETCH_ASSOC);

$totalData = count($dataKelas);

?>

<div class="wrapper">

    <?php require_once "../template/sidebar.php"; ?>

    <div class="content">

        <?php require_once "../template/topbar.php"; ?>

        <div class="container-fluid mt-4">

            <div class="card shadow border-0">

                <div class="card-header bg-primary text-white">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div>

                            <h4 class="mb-1">

                                🏫 Data Kelas

                            </h4>

                            <small>

                                Kelola data kelas dan siswa pada tahun pelajaran aktif.

                            </small>

                        </div>

                        <div class="mt-2 mt-md-0">

                            <a href="tambah.php" class="btn btn-light me-2">

                                <i class="bi bi-plus-circle"></i>

                                Tambah Kelas

                            </a>

                            <a href="siswa/import.php" class="btn btn-success">

                                <i class="bi bi-file-earmark-excel"></i>

                                Import Siswa

                            </a>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <?php if (isset($_SESSION['pesan'])) : ?>

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

                    endif;

                    ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover table-striped align-middle">

                            <thead class="table-primary text-center">

                                <tr>

                                    <th width="70">

                                        No

                                    </th>

                                    <th>

                                        Nama Kelas

                                    </th>

                                    <th width="200">

                                        Tahun Pelajaran

                                    </th>

                                    <th width="150">

                                        Jumlah Siswa

                                    </th>

                                    <th width="320">

                                        Aksi

                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php

                                $no = 1;

                                if (!empty($dataKelas)) :

                                    foreach ($dataKelas as $data) :

                                ?>

                                                                <tr>

                                    <td class="text-center">

                                        <?= $no++; ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars($data['nama_kelas']); ?>

                                    </td>

                                    <td class="text-center">

                                        <?= htmlspecialchars($data['nama_tahun']); ?>

                                    </td>

                                    <td class="text-center">

                                        <span class="badge bg-success">

                                            <?= $data['jumlah_siswa']; ?> Siswa

                                        </span>

                                    </td>

                                    <td class="text-center">

                                        <a
                                            href="edit.php?id=<?= $data['id_kelas']; ?>"
                                            class="btn btn-warning btn-sm">

                                            <i class="bi bi-pencil-square"></i>
                                            Edit

                                        </a>

                                        <a
                                            href="siswa/index.php?id_kelas=<?= $data['id_kelas']; ?>"
                                            class="btn btn-info btn-sm text-white">

                                            <i class="bi bi-people-fill"></i>
                                            Data Siswa

                                        </a>

                                        <a
                                            href="hapus.php?id=<?= $data['id_kelas']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus kelas <?= htmlspecialchars($data['nama_kelas']); ?> ?')">

                                            <i class="bi bi-trash"></i>
                                            Hapus

                                        </a>

                                    </td>

                                </tr>

                                <?php

                                    endforeach;

                                else :

                                ?>

                                <tr>

                                    <td colspan="5" class="text-center py-4">

                                        <div class="alert alert-warning mb-0">

                                            <i class="bi bi-exclamation-circle"></i>

                                            Belum ada data kelas.

                                        </div>

                                    </td>

                                </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div class="text-muted">

                            Total Data :

                            <strong>

                                <?= $totalData; ?>

                            </strong>

                            Kelas

                        </div>

                        <div>

                            <small class="text-secondary">

                                SIMU © <?= date('Y'); ?>

                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once "../template/footer.php"; ?>