<?php

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/tahun_ajaran.php";
require_once "../../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi ID Kelas
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id_kelas']) || !is_numeric($_GET['id_kelas'])) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Data kelas tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

$id_kelas = (int) $_GET['id_kelas'];

/*
|--------------------------------------------------------------------------
| Ambil Tahun Ajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Belum ada tahun ajaran aktif.";

    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Kelas
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id_kelas,
        nama_kelas
    FROM kelas
    WHERE
        id_kelas = ?
        AND id_tahun = ?
        AND dihapus_pada IS NULL
    LIMIT 1
");

$query->execute([
    $id_kelas,
    $tahun['id_tahun']
]);

$kelas = $query->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {

    $_SESSION['warna'] = "danger";
    $_SESSION['pesan'] = "Kelas tidak ditemukan.";

    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Pencarian
|--------------------------------------------------------------------------
*/

$cari = trim($_GET['cari'] ?? '');

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$batas = 10;

$halaman = isset($_GET['halaman'])
    ? (int) $_GET['halaman']
    : 1;

if ($halaman < 1) {

    $halaman = 1;

}

$mulai = ($halaman - 1) * $batas;

/*
|--------------------------------------------------------------------------
| Hitung Total Data
|--------------------------------------------------------------------------
*/

$sqlTotal = "
    SELECT
        COUNT(*) AS total
    FROM siswa
    WHERE
        id_kelas = :id_kelas
        AND id_tahun = :id_tahun
        AND dihapus_pada IS NULL
";

if ($cari != "") {

    $sqlTotal .= "
        AND
        (
            nama LIKE :cari
            OR nis LIKE :cari
            OR nomor_peserta LIKE :cari
        )
    ";

}

$query = $pdo->prepare($sqlTotal);

$query->bindValue(
    ':id_kelas',
    $id_kelas,
    PDO::PARAM_INT
);

$query->bindValue(
    ':id_tahun',
    $tahun['id_tahun'],
    PDO::PARAM_INT
);

if ($cari != "") {

    $query->bindValue(
        ':cari',
        "%{$cari}%"
    );

}

$query->execute();

$totalData = $query->fetch(PDO::FETCH_ASSOC)['total'];

$totalHalaman = ceil($totalData / $batas);

/*
|--------------------------------------------------------------------------
| Ambil Data Siswa
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id_siswa,
        nis,
        nomor_peserta,
        nama,
        jk,
        status
    FROM siswa
    WHERE
        id_kelas = :id_kelas
        AND id_tahun = :id_tahun
        AND dihapus_pada IS NULL
";

if ($cari != "") {

    $sql .= "
        AND
        (
            nama LIKE :cari
            OR nis LIKE :cari
            OR nomor_peserta LIKE :cari
        )
    ";

}

$sql .= "
    ORDER BY
        nama ASC
    LIMIT :mulai, :batas
";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_kelas',
    $id_kelas,
    PDO::PARAM_INT
);

$query->bindValue(
    ':id_tahun',
    $tahun['id_tahun'],
    PDO::PARAM_INT
);

if ($cari != "") {

    $query->bindValue(
        ':cari',
        "%{$cari}%"
    );

}

$query->bindValue(
    ':mulai',
    $mulai,
    PDO::PARAM_INT
);

$query->bindValue(
    ':batas',
    $batas,
    PDO::PARAM_INT
);

$query->execute();

$dataSiswa = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="wrapper">

<?php require_once "../../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

<div>

<h4 class="mb-1">

👨‍🎓 Data Siswa

</h4>

<small>

Kelas
<strong><?= htmlspecialchars($kelas['nama_kelas']); ?></strong>

|

Tahun Pelajaran

<strong><?= htmlspecialchars($tahun['nama_tahun']); ?></strong>

</small>

</div>

<div>

<a
href="../index.php"
class="btn btn-light">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

</div>

</div>

</div>

<div class="card-body">

<?php

if (isset($_SESSION['pesan'])) {

?>

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

}

?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

<div>

<a
href="tambah.php?id_kelas=<?= $id_kelas; ?>"
class="btn btn-primary">

<i class="bi bi-person-plus-fill"></i>

Tambah Siswa

</a>

<a
href="import.php?id_kelas=<?= $id_kelas; ?>"
class="btn btn-success">

<i class="bi bi-file-earmark-excel-fill"></i>

Import Excel

</a>

</div>

<form
method="GET"
class="d-flex">

<input
type="hidden"
name="id_kelas"
value="<?= $id_kelas; ?>">

<input
type="text"
name="cari"
class="form-control me-2"
placeholder="Cari siswa..."
value="<?= htmlspecialchars($cari); ?>">

<button
type="submit"
class="btn btn-outline-primary">

<i class="bi bi-search"></i>

</button>

</form>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-light">

<tr>

<th width="60">No</th>

<th>NIS</th>

<th>Nomor Peserta</th>

<th>Nama</th>

<th width="90">JK</th>

<th width="120">Status</th>

<th width="180">Aksi</th>

</tr>

</thead>

<tbody>

<?php

$no = $mulai + 1;

if (count($dataSiswa) > 0) :
?>
    <?php foreach ($dataSiswa as $siswa) : ?>

    <tr>

        <td class="text-center">

            <?= $no++; ?>

        </td>

        <td>

            <?= htmlspecialchars($siswa['nis']); ?>

        </td>

        <td>

            <strong>

                <?= htmlspecialchars($siswa['nomor_peserta']); ?>

            </strong>

        </td>

        <td>

            <?= htmlspecialchars($siswa['nama']); ?>

        </td>

        <td class="text-center">

            <?= $siswa['jk'] == "L" ? "L" : "P"; ?>

        </td>

        <td class="text-center">

            <?php if ($siswa['status'] == "Aktif") : ?>

                <span class="badge bg-success">

                    Aktif

                </span>

            <?php else : ?>

                <span class="badge bg-secondary">

                    <?= htmlspecialchars($siswa['status']); ?>

                </span>

            <?php endif; ?>

        </td>

        <td class="text-center">

            <a
                href="edit.php?id_siswa=<?= $siswa['id_siswa']; ?>"
                class="btn btn-warning btn-sm">

                <i class="bi bi-pencil-square"></i>

                Edit

            </a>

            <a
                href="hapus.php?id_siswa=<?= $siswa['id_siswa']; ?>"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Yakin ingin menghapus siswa ini?');">

                <i class="bi bi-trash"></i>

                Hapus

            </a>

        </td>

    </tr>

    <?php endforeach; ?>

<?php else : ?>

<tr>

    <td
        colspan="7"
        class="text-center text-muted py-4">

        Belum ada data siswa.

    </td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<?php if ($totalData > 0) : ?>

<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">

<div>

    Menampilkan

    <strong>

        <?= $totalData == 0 ? 0 : $mulai + 1; ?>

    </strong>

    -

    <strong>

        <?= min($mulai + $batas, $totalData); ?>

    </strong>

    dari

    <strong>

        <?= $totalData; ?>

    </strong>

    siswa

</div>

<nav>

<ul class="pagination mb-0">

<?php if ($halaman > 1) : ?>

<li class="page-item">

<a
class="page-link"
href="?id_kelas=<?= $id_kelas; ?>&cari=<?= urlencode($cari); ?>&halaman=<?= $halaman - 1; ?>">

&laquo;

</a>

</li>

<?php endif; ?>

<?php for ($i = 1; $i <= $totalHalaman; $i++) : ?>

<li class="page-item <?= ($i == $halaman) ? 'active' : ''; ?>">

<a
class="page-link"
href="?id_kelas=<?= $id_kelas; ?>&cari=<?= urlencode($cari); ?>&halaman=<?= $i; ?>">

<?= $i; ?>

</a>

</li>

<?php endfor; ?>

<?php if ($halaman < $totalHalaman) : ?>

<li class="page-item">

<a
class="page-link"
href="?id_kelas=<?= $id_kelas; ?>&cari=<?= urlencode($cari); ?>&halaman=<?= $halaman + 1; ?>">

&raquo;

</a>

</li>

<?php endif; ?>

</ul>

</nav>

</div>

<?php endif; ?>

</div>

</div>

</div>

</div>

<?php require_once "../../template/footer.php"; ?>