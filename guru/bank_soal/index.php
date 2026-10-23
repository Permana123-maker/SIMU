<?php

$page_title = "Bank Soal";

require_once "../template/header.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Ambil Data Soal
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("

    SELECT
        soal.*,
        mapel.nama_mapel

    FROM soal

    LEFT JOIN mapel
        ON soal.id_mapel = mapel.id

    ORDER BY soal.id DESC

");

$soal = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-journal-text"></i>

                Bank Soal

            </h5>

            <div>

                <a href="../import_soal/index.php"
                   class="btn btn-light btn-sm">

                    <i class="bi bi-upload"></i>

                    Import

                </a>

            </div>

        </div>

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-4">

                    <input
                        type="text"
                        id="tableSearch"
                        class="form-control"
                        placeholder="Cari soal...">

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th width="60">No</th>

                            <th>Mata Pelajaran</th>

                            <th>Pertanyaan</th>

                            <th width="100">Jawaban</th>

                            <th width="180">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    $no = 1;

                    foreach($soal as $row):

                    ?>

                        <tr>

                            <td><?= $no++; ?></td>

                            <td>

                                <?= htmlspecialchars($row['nama_mapel']); ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($row['pertanyaan']); ?>

                            </td>

                            <td>

                                <span class="badge bg-success">

                                    <?= $row['jawaban']; ?>

                                </span>

                            </td>

                            <td>

                                <a href="detail.php?id=<?= $row['id']; ?>"
                                   class="btn btn-info btn-sm text-white">

                                    <i class="bi bi-eye"></i>

                                </a>

                                <a href="edit.php?id=<?= $row['id']; ?>"
                                   class="btn btn-warning btn-sm">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <a href="hapus.php?id=<?= $row['id']; ?>"
                                   class="btn btn-danger btn-sm btn-delete">

                                    <i class="bi bi-trash"></i>

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../template/footer.php";

?>