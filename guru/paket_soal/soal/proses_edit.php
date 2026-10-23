<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Edit Soal
|--------------------------------------------------------------------------
*/

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$id_soal = isset($_POST['id_soal'])
    ? (int) $_POST['id_soal']
    : 0;

$id_paket = isset($_POST['id_paket'])
    ? (int) $_POST['id_paket']
    : 0;

$pertanyaan = trim($_POST['pertanyaan'] ?? '');

$pilihan_a = trim($_POST['pilihan_a'] ?? '');

$pilihan_b = trim($_POST['pilihan_b'] ?? '');

$pilihan_c = trim($_POST['pilihan_c'] ?? '');

$pilihan_d = trim($_POST['pilihan_d'] ?? '');

$kunci_jawaban = trim($_POST['kunci_jawaban'] ?? '');

$jawaban_uraian = trim($_POST['jawaban_uraian'] ?? '');

$bobot = trim($_POST['bobot'] ?? '1');

/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

if ($id_soal <= 0 || $id_paket <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Data soal tidak valid.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Jenis Soal
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    jenis

FROM soal

WHERE

    id_soal = :id_soal

AND

    dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_soal',
    $id_soal,
    PDO::PARAM_INT
);

$query->execute();

$soal = $query->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if (!$soal) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Soal tidak ditemukan.";

    header("Location: index.php?id_paket=" . $id_paket);

    exit;

}

$jenis = $soal['jenis'];

/*
|--------------------------------------------------------------------------
| Validasi Data Berdasarkan Jenis Soal
|--------------------------------------------------------------------------
*/

if ($pertanyaan === '') {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Pertanyaan tidak boleh kosong.";

    header("Location: edit.php?id_soal=" . $id_soal);

    exit;

}

if (!is_numeric($bobot) || $bobot <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Bobot soal tidak valid.";

    header("Location: edit.php?id_soal=" . $id_soal);

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Soal Pilihan Ganda
|--------------------------------------------------------------------------
*/

if ($jenis === "PG") {

    if (

        $pilihan_a === "" ||

        $pilihan_b === "" ||

        $pilihan_c === "" ||

        $pilihan_d === ""

    ) {

        $_SESSION['warna'] = "danger";

        $_SESSION['pesan'] = "Seluruh pilihan jawaban A sampai D wajib diisi.";

        header("Location: edit.php?id_soal=" . $id_soal);

        exit;

    }

    if (

        !in_array(

            $kunci_jawaban,

            ['A', 'B', 'C', 'D']

        )

    ) {

        $_SESSION['warna'] = "danger";

        $_SESSION['pesan'] = "Kunci jawaban tidak valid.";

        header("Location: edit.php?id_soal=" . $id_soal);

        exit;

    }

}

/*
|--------------------------------------------------------------------------
| Validasi Soal Isian
|--------------------------------------------------------------------------
*/

elseif ($jenis === "ISIAN") {

    if ($kunci_jawaban === "") {

        $_SESSION['warna'] = "danger";

        $_SESSION['pesan'] = "Jawaban isian wajib diisi.";

        header("Location: edit.php?id_soal=" . $id_soal);

        exit;

    }

}

/*
|--------------------------------------------------------------------------
| Validasi Soal Uraian
|--------------------------------------------------------------------------
*/

elseif ($jenis === "URAIAN") {

    if ($jawaban_uraian === "") {

        $_SESSION['warna'] = "danger";

        $_SESSION['pesan'] = "Jawaban uraian wajib diisi.";

        header("Location: edit.php?id_soal=" . $id_soal);

        exit;

    }

}

/*
|--------------------------------------------------------------------------
| Lanjut Bagian 3
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Simpan Perubahan
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Soal Pilihan Ganda
    |--------------------------------------------------------------------------
    */

    if ($jenis === "PG") {

        $sql = "

        UPDATE soal

        SET

            pertanyaan     = :pertanyaan,
            pilihan_a      = :pilihan_a,
            pilihan_b      = :pilihan_b,
            pilihan_c      = :pilihan_c,
            pilihan_d      = :pilihan_d,
            kunci_jawaban  = :kunci_jawaban,
            jawaban_uraian = NULL,
            bobot          = :bobot,
            diubah_pada    = NOW()

        WHERE

            id_soal = :id_soal

        ";

        $query = $pdo->prepare($sql);

        $query->execute([

            ':pertanyaan'      => $pertanyaan,
            ':pilihan_a'       => $pilihan_a,
            ':pilihan_b'       => $pilihan_b,
            ':pilihan_c'       => $pilihan_c,
            ':pilihan_d'       => $pilihan_d,
            ':kunci_jawaban'   => $kunci_jawaban,
            ':bobot'           => $bobot,
            ':id_soal'         => $id_soal

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Soal Isian
    |--------------------------------------------------------------------------
    */

    elseif ($jenis === "ISIAN") {

        $sql = "

        UPDATE soal

        SET

            pertanyaan     = :pertanyaan,
            pilihan_a      = NULL,
            pilihan_b      = NULL,
            pilihan_c      = NULL,
            pilihan_d      = NULL,
            kunci_jawaban  = :kunci_jawaban,
            jawaban_uraian = NULL,
            bobot          = :bobot,
            diubah_pada    = NOW()

        WHERE

            id_soal = :id_soal

        ";

        $query = $pdo->prepare($sql);

        $query->execute([

            ':pertanyaan'     => $pertanyaan,
            ':kunci_jawaban'  => $kunci_jawaban,
            ':bobot'          => $bobot,
            ':id_soal'        => $id_soal

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Soal Uraian
    |--------------------------------------------------------------------------
    */

    elseif ($jenis === "URAIAN") {

        $sql = "

        UPDATE soal

        SET

            pertanyaan     = :pertanyaan,
            pilihan_a      = NULL,
            pilihan_b      = NULL,
            pilihan_c      = NULL,
            pilihan_d      = NULL,
            pilihan_e      = NULL,
            kunci_jawaban  = NULL,
            jawaban_uraian = :jawaban_uraian,
            bobot          = :bobot,
            diubah_pada    = NOW()

        WHERE

            id_soal = :id_soal

        ";

        $query = $pdo->prepare($sql);

        $query->execute([

            ':pertanyaan'      => $pertanyaan,
            ':jawaban_uraian'  => $jawaban_uraian,
            ':bobot'           => $bobot,
            ':id_soal'         => $id_soal

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Commit
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | Lanjut Bagian 4
    |--------------------------------------------------------------------------
    */

        /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] = "Soal berhasil diperbarui.";

    header("Location: index.php?id_paket=" . $id_paket);

    exit;

}

/*
|--------------------------------------------------------------------------
| Gagal
|--------------------------------------------------------------------------
*/

catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    /*
    |--------------------------------------------------------------------------
    | Pesan Error
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Terjadi kesalahan saat memperbarui soal.";

    /*
    |--------------------------------------------------------------------------
    | Aktifkan saat proses debugging
    |--------------------------------------------------------------------------
    |
    | echo $e->getMessage();
    | exit;
    |
    */

    header("Location: edit.php?id_soal=" . $id_soal);

    exit;

}