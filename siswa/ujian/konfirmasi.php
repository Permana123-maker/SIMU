<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Konfirmasi Ujian
|--------------------------------------------------------------------------
*/

session_start();

require_once "../../app/database/database.php";
require_once "../../app/helper/tahun_ajaran.php";
require_once "../template/header.php";

/*
|--------------------------------------------------------------------------
| Validasi Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['siswa'])) {

    header("Location: ../login/login.php");
    exit;

}

/*
|--------------------------------------------------------------------------
| Tahun Pelajaran Aktif
|--------------------------------------------------------------------------
*/

$tahun = getTahunAjaran($pdo);

if (!$tahun) {

    die("Belum ada Tahun Pelajaran aktif.");

}

/*
|--------------------------------------------------------------------------
| Validasi ID Ujian
|--------------------------------------------------------------------------
*/

$id_ujian = isset($_GET['id_ujian'])
    ? (int) $_GET['id_ujian']
    : 0;

if ($id_ujian <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Session Token
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['token_valid'][$id_ujian])) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Silakan masukkan token ujian terlebih dahulu.";

    header("Location: token.php?id_ujian=".$id_ujian);

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Pemilik Token
|--------------------------------------------------------------------------
*/

if (

    $_SESSION['token_valid'][$id_ujian]['id_siswa']
    !=
    $_SESSION['siswa']['id_siswa']

) {

    unset($_SESSION['token_valid'][$id_ujian]);

    session_destroy();

    header("Location: ../login/login.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Masa Berlaku Token (5 Menit)
|--------------------------------------------------------------------------
*/

if (

    (time() - $_SESSION['token_valid'][$id_ujian]['waktu'])

    >

    300

) {

    unset($_SESSION['token_valid'][$id_ujian]);

    $_SESSION['warna'] = "warning";

    $_SESSION['pesan'] = "Token telah kedaluwarsa. Silakan masukkan kembali.";

    header("Location: token.php?id_ujian=".$id_ujian);

    exit;

}

/*
|--------------------------------------------------------------------------
| Data Session
|--------------------------------------------------------------------------
*/

$id_siswa = $_SESSION['siswa']['id_siswa'];

$id_kelas = $_SESSION['siswa']['id_kelas'];

/*
|--------------------------------------------------------------------------
| Ambil Data Ujian
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("

SELECT

    u.id_ujian,
    u.nama_ujian,
    u.durasi,
    u.jenis_penilaian,
    u.bobot_pg,
    u.bobot_uraian,
    u.fullscreen,
    u.anti_cheat,
    u.maksimal_pelanggaran,

    p.nama_paket,

    (
        SELECT COUNT(*)
        FROM soal
        WHERE id_paket = u.id_paket
        AND dihapus_pada IS NULL
    ) AS jumlah_soal

FROM ujian u

INNER JOIN paket_soal p
    ON u.id_paket = p.id_paket

WHERE

    u.id_ujian = ?

AND

    u.id_tahun = ?

AND

    u.id_kelas = ?

AND

    u.status = 'Aktif'

AND

    u.dihapus_pada IS NULL

LIMIT 1

");

$query->execute([

    $id_ujian,

    $tahun['id_tahun'],

    $id_kelas

]);

$ujian = $query->fetch(PDO::FETCH_ASSOC);

if (!$ujian) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data ujian tidak ditemukan.";

    header("Location: index.php");

    exit;

}

?>

<div class="wrapper">

<?php require_once "../template/sidebar.php"; ?>

<div class="content">

<?php require_once "../template/topbar.php"; ?>

<div class="container-fluid mt-4">

<div class="card shadow border-0 mb-4">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

📝 Konfirmasi Ujian

</h4>

</div>

<div class="card-body">

<div class="alert alert-info">

<i class="bi bi-info-circle-fill"></i>

Pastikan informasi ujian di bawah ini sudah benar sebelum Anda memulai ujian.

</div>

<table class="table table-bordered align-middle">

<tr>

<th width="250">

Nama Ujian

</th>

<td>

<?= htmlspecialchars($ujian['nama_ujian']); ?>

</td>

</tr>

<tr>

<th>

Paket Soal

</th>

<td>

<?= htmlspecialchars($ujian['nama_paket']); ?>

</td>

</tr>

<tr>

<th>

Jumlah Soal

</th>

<td>

<?= $ujian['jumlah_soal']; ?> Soal

</td>

</tr>

<tr>

<th>

Durasi

</th>

<td>

<?= $ujian['durasi']; ?> Menit

</td>

</tr>

<tr>

<th>

Jenis Penilaian

</th>

<td>

<?= htmlspecialchars($ujian['jenis_penilaian']); ?>

</td>

</tr>

<tr>

<th>

Bobot Pilihan Ganda

</th>

<td>

<?= $ujian['bobot_pg']; ?> %

</td>

</tr>

<tr>

<th>

Bobot Uraian

</th>

<td>

<?= $ujian['bobot_uraian']; ?> %

</td>

</tr>

<tr>

<th>

Mode Fullscreen

</th>

<td>

<?php if ($ujian['fullscreen'] == 'Ya') { ?>

<span class="badge bg-success">

Wajib

</span>

<?php } else { ?>

<span class="badge bg-secondary">

Tidak Wajib

</span>

<?php } ?>

</td>

</tr>

<tr>

<th>

Anti Cheat

</th>

<td>

<?php if ($ujian['anti_cheat'] == 'Ya') { ?>

<span class="badge bg-success">

Aktif

</span>

<?php } else { ?>

<span class="badge bg-secondary">

Tidak Aktif

</span>

<?php } ?>

</td>

</tr>

<tr>

<th>

Maksimal Pelanggaran

</th>

<td>

<?= $ujian['maksimal_pelanggaran']; ?> Kali

</td>

</tr>

</table>

</div>

</div>

<form
action="mulai_ujian.php"
method="POST">

<input
type="hidden"
name="id_ujian"
value="<?= $ujian['id_ujian']; ?>">

<div class="card shadow border-0 mb-4">

<div class="card-header bg-warning">

<h5 class="mb-0">

📌 Tata Tertib Ujian

</h5>

</div>

<div class="card-body">

<ul class="mb-4">

<li>

Pastikan perangkat memiliki daya baterai yang cukup.

</li>

<li>

Pastikan koneksi internet stabil selama ujian berlangsung.

</li>

<li>

Kerjakan soal secara mandiri dan jujur.

</li>

<li>

Jangan menutup browser atau me-refresh halaman ujian.

</li>

<li>

Jangan membuka tab atau aplikasi lain selama ujian berlangsung.

</li>

<li>

Apabila mode <strong>Fullscreen</strong> diwajibkan, siswa tidak diperbolehkan keluar dari mode tersebut.

</li>

<li>

Setiap pelanggaran akan tercatat secara otomatis oleh sistem.

</li>

<li>

Apabila jumlah pelanggaran melebihi batas yang ditentukan, ujian akan diakhiri secara otomatis.

</li>

<li>

Timer ujian dimulai setelah tombol <strong>Mulai Ujian</strong> ditekan.

</li>

<li>

Setelah waktu habis, ujian akan diselesaikan secara otomatis oleh sistem.

</li>

</ul>

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
id="persetujuan"
name="persetujuan"
required>

<label
class="form-check-label"
for="persetujuan">

Saya telah membaca, memahami, dan bersedia mematuhi seluruh tata tertib ujian yang berlaku.

</label>

</div>

</div>

</div>

<div class="d-flex justify-content-between">

<a
href="index.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

<button
type="submit"
class="btn btn-success">

<i class="bi bi-play-circle"></i>

Mulai Ujian

</button>

</div>

</form>

</div>

</div>

<?php require_once "../template/footer.php"; ?>

</body>

</html>