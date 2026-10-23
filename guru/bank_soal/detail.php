<?php

$page_title = "Detail Soal";

require_once "../template/header.php";
require_once "../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    header("Location:index.php");
    exit;

}

$id = intval($_GET['id']);

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    SELECT
        soal.*,
        mapel.nama_mapel

    FROM soal

    LEFT JOIN mapel
        ON soal.id_mapel = mapel.id

    WHERE soal.id = ?

");

$stmt->execute([$id]);

$soal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$soal) {

    header("Location:index.php");
    exit;

}

?>

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-eye"></i>

                Detail Soal

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>

                    <th width="200">
                        Mata Pelajaran
                    </th>

                    <td>

                        <?= htmlspecialchars($soal['nama_mapel']); ?>

                    </td>

                </tr>

                <tr>

                    <th>
                        Pertanyaan
                    </th>

                    <td>

                        <?= nl2br(htmlspecialchars($soal['pertanyaan'])); ?>

                    </td>

                </tr>

                <tr>

                    <th>
                        Pilihan A
                    </th>

                    <td>

                        <?= htmlspecialchars($soal['pilihan_a']); ?>

                    </td>

                </tr>

                <tr>

                    <th>
                        Pilihan B
                    </th>

                    <td>

                        <?= htmlspecialchars($soal['pilihan_b']); ?>

                    </td>

                </tr>

                <tr>

                    <th>
                        Pilihan C
                    </th>

                    <td>

                        <?= htmlspecialchars($soal['pilihan_c']); ?>

                    </td>

                </tr>

                <tr>

                    <th>
                        Pilihan D
                    </th>

                    <td>

                        <?= htmlspecialchars($soal['pilihan_d']); ?>

                    </td>

                </tr>

                <tr>

                    <th>
                        Pilihan E
                    </th>

                    <td>

                        <?= htmlspecialchars($soal['pilihan_e']); ?>

                    </td>

                </tr>

                <tr>

                    <th>
                        Jawaban
                    </th>

                    <td>

                        <span class="badge bg-success">

                            <?= $soal['jawaban']; ?>

                        </span>

                    </td>

                </tr>

            </table>

            <a href="index.php"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left"></i>

                Kembali

            </a>

            <a href="edit.php?id=<?= $soal['id']; ?>"
               class="btn btn-warning">

                <i class="bi bi-pencil"></i>

                Edit

            </a>

        </div>

    </div>

</div>

<?php

require_once "../template/footer.php";

?>