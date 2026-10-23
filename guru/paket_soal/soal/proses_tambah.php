<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Proses Tambah Soal
|--------------------------------------------------------------------------
*/

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";
require_once "../../../app/helper/kode_soal.php";

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

$id_paket = isset($_POST['id_paket'])
    ? (int) $_POST['id_paket']
    : 0;

$jenis = trim($_POST['jenis'] ?? '');

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
| Validasi Paket Soal
|--------------------------------------------------------------------------
*/

if ($id_paket <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Paket soal tidak valid.";

    header("Location: ../index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Paket
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    id_paket

FROM paket_soal

WHERE

    id_paket = :id_paket

AND

    dihapus_pada IS NULL

LIMIT 1

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_paket',
    $id_paket,
    PDO::PARAM_INT
);

$query->execute();

$paket = $query->fetch(PDO::FETCH_ASSOC);

if (!$paket) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Paket soal tidak ditemukan.";

    header("Location: ../index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| Validasi Data
|--------------------------------------------------------------------------
*/

if ($pertanyaan === '') {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Pertanyaan tidak boleh kosong.";

    header("Location: tambah.php?id_paket=" . $id_paket);

    exit;

}

if (!in_array($jenis, ['PG', 'ISIAN', 'URAIAN'])) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Jenis soal tidak valid.";

    header("Location: tambah.php?id_paket=" . $id_paket);

    exit;

}

if (!is_numeric($bobot) || $bobot <= 0) {

    $_SESSION['warna'] = "danger";

    $_SESSION['pesan'] = "Bobot soal tidak valid.";

    header("Location: tambah.php?id_paket=" . $id_paket);

    exit;

}

/*
|--------------------------------------------------------------------------
| Nomor Soal Otomatis
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    COALESCE(MAX(nomor),0)+1 AS nomor

FROM soal

WHERE

    id_paket = :id_paket

AND

    dihapus_pada IS NULL

";

$query = $pdo->prepare($sql);

$query->bindValue(
    ':id_paket',
    $id_paket,
    PDO::PARAM_INT
);

$query->execute();

$nomor = (int) $query->fetchColumn();

/*
|--------------------------------------------------------------------------
| Generate Kode Soal
|--------------------------------------------------------------------------
*/

$kode_soal = generateKodeSoal($nomor);

/*
|--------------------------------------------------------------------------
| Validasi Berdasarkan Jenis Soal
|--------------------------------------------------------------------------
*/

switch ($jenis) {

    case "PG":

        if (

            $pilihan_a === "" ||

            $pilihan_b === "" ||

            $pilihan_c === "" ||

            $pilihan_d === ""

        ) {

            $_SESSION['warna'] = "danger";

            $_SESSION['pesan'] = "Seluruh pilihan jawaban A sampai D wajib diisi.";

            header("Location: tambah.php?id_paket=" . $id_paket);

            exit;

        }

        if (!in_array($kunci_jawaban, ['A', 'B', 'C', 'D'])) {

            $_SESSION['warna'] = "danger";

            $_SESSION['pesan'] = "Kunci jawaban tidak valid.";

            header("Location: tambah.php?id_paket=" . $id_paket);

            exit;

        }

        $jawaban_uraian = null;

    break;

    case "ISIAN":

        if ($kunci_jawaban === "") {

            $_SESSION['warna'] = "danger";

            $_SESSION['pesan'] = "Jawaban isian wajib diisi.";

            header("Location: tambah.php?id_paket=" . $id_paket);

            exit;

        }

        $pilihan_a = null;
        $pilihan_b = null;
        $pilihan_c = null;
        $pilihan_d = null;

        $jawaban_uraian = null;

    break;

    case "URAIAN":

        if ($jawaban_uraian === "") {

            $_SESSION['warna'] = "danger";

            $_SESSION['pesan'] = "Jawaban uraian wajib diisi.";

            header("Location: tambah.php?id_paket=" . $id_paket);

            exit;

        }

        $pilihan_a = null;
        $pilihan_b = null;
        $pilihan_c = null;
        $pilihan_d = null;

        $kunci_jawaban = null;

    break;

}

/*
|--------------------------------------------------------------------------
| Simpan Data
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

        INSERT INTO soal (

            id_paket,
            kode_soal,
            nomor,
            jenis,
            pertanyaan,
            pilihan_a,
            pilihan_b,
            pilihan_c,
            pilihan_d,
            kunci_jawaban,
            jawaban_uraian,
            bobot

        )

        VALUES (

            :id_paket,
            :kode_soal,
            :nomor,
            :jenis,
            :pertanyaan,
            :pilihan_a,
            :pilihan_b,
            :pilihan_c,
            :pilihan_d,
            :kunci_jawaban,
            NULL,
            :bobot

        )

        ";

        $query = $pdo->prepare($sql);

        $query->execute([

            ':id_paket'        => $id_paket,
            ':kode_soal'       => $kode_soal,
            ':nomor'           => $nomor,
            ':jenis'           => $jenis,
            ':pertanyaan'      => $pertanyaan,
            ':pilihan_a'       => $pilihan_a,
            ':pilihan_b'       => $pilihan_b,
            ':pilihan_c'       => $pilihan_c,
            ':pilihan_d'       => $pilihan_d,
            ':kunci_jawaban'   => $kunci_jawaban,
            ':bobot'           => $bobot

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Soal Isian
    |--------------------------------------------------------------------------
    */

    elseif ($jenis === "ISIAN") {

        $sql = "

        INSERT INTO soal (

            id_paket,
            kode_soal,
            nomor,
            jenis,
            pertanyaan,
            kunci_jawaban,
            jawaban_uraian,
            bobot

        )

        VALUES (

            :id_paket,
            :kode_soal,
            :nomor,
            :jenis,
            :pertanyaan,
            :kunci_jawaban,
            NULL,
            :bobot

        )

        ";

        $query = $pdo->prepare($sql);

        $query->execute([

            ':id_paket'       => $id_paket,
            ':kode_soal'      => $kode_soal,
            ':nomor'          => $nomor,
            ':jenis'          => $jenis,
            ':pertanyaan'     => $pertanyaan,
            ':kunci_jawaban'  => $kunci_jawaban,
            ':bobot'          => $bobot

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Soal Uraian
    |--------------------------------------------------------------------------
    */

    elseif ($jenis === "URAIAN") {

        $sql = "

        INSERT INTO soal (

            id_paket,
            kode_soal,
            nomor,
            jenis,
            pertanyaan,
            jawaban_uraian,
            bobot

        )

        VALUES (

            :id_paket,
            :kode_soal,
            :nomor,
            :jenis,
            :pertanyaan,
            :jawaban_uraian,
            :bobot

        )

        ";

        $query = $pdo->prepare($sql);

        $query->execute([

            ':id_paket'         => $id_paket,
            ':kode_soal'        => $kode_soal,
            ':nomor'            => $nomor,
            ':jenis'            => $jenis,
            ':pertanyaan'       => $pertanyaan,
            ':jawaban_uraian'   => $jawaban_uraian,
            ':bobot'            => $bobot

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Lanjut Bagian 4
    |--------------------------------------------------------------------------
    */

        /*
    |--------------------------------------------------------------------------
    | Commit Transaction
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['warna'] = "success";

    $_SESSION['pesan'] = "Soal berhasil ditambahkan.";

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

    $_SESSION['pesan'] = "Terjadi kesalahan saat menambahkan soal.";

    /*
    |--------------------------------------------------------------------------
    | Aktifkan saat proses debugging
    |--------------------------------------------------------------------------
    |
    | echo $e->getMessage();
    | exit;
    |
    */

    header("Location: tambah.php?id_paket=" . $id_paket);

    exit;

}