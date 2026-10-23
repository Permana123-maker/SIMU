<?php

/*
|--------------------------------------------------------------------------
| VALIDASI SIMU
|--------------------------------------------------------------------------
|
| Kumpulan fungsi validasi yang digunakan di seluruh aplikasi.
|
*/

/*
|--------------------------------------------------------------------------
| Wajib Diisi
|--------------------------------------------------------------------------
*/

function wajib($nilai)
{
    return trim($nilai) !== "";
}

/*
|--------------------------------------------------------------------------
| Minimal Karakter
|--------------------------------------------------------------------------
*/

function minimalKarakter($nilai, $minimal)
{
    return strlen(trim($nilai)) >= $minimal;
}

/*
|--------------------------------------------------------------------------
| Maksimal Karakter
|--------------------------------------------------------------------------
*/

function maksimalKarakter($nilai, $maksimal)
{
    return strlen(trim($nilai)) <= $maksimal;
}

/*
|--------------------------------------------------------------------------
| Harus Angka
|--------------------------------------------------------------------------
*/

function harusAngka($nilai)
{
    return is_numeric($nilai);
}

/*
|--------------------------------------------------------------------------
| Bilangan Bulat
|--------------------------------------------------------------------------
*/

function harusInteger($nilai)
{
    return filter_var($nilai, FILTER_VALIDATE_INT) !== false;
}

/*
|--------------------------------------------------------------------------
| Email Valid
|--------------------------------------------------------------------------
*/

function emailValid($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/*
|--------------------------------------------------------------------------
| Username Valid
|--------------------------------------------------------------------------
|
| Huruf, angka dan underscore
|
*/

function usernameValid($username)
{
    return preg_match('/^[a-zA-Z0-9_]+$/', $username);
}

/*
|--------------------------------------------------------------------------
| Panjang Password
|--------------------------------------------------------------------------
*/

function passwordValid($password)
{
    return strlen($password) >= 6;
}

/*
|--------------------------------------------------------------------------
| Enum
|--------------------------------------------------------------------------
*/

function dalamPilihan($nilai, array $pilihan)
{
    return in_array($nilai, $pilihan);
}

/*
|--------------------------------------------------------------------------
| Sama
|--------------------------------------------------------------------------
*/

function sama($nilai1, $nilai2)
{
    return $nilai1 === $nilai2;
}

/*
|--------------------------------------------------------------------------
| Tanggal Valid
|--------------------------------------------------------------------------
*/

function tanggalValid($tanggal)
{
    if (empty($tanggal)) {
        return false;
    }

    $pecah = explode("-", $tanggal);

    if (count($pecah) != 3) {
        return false;
    }

    return checkdate(
        (int)$pecah[1],
        (int)$pecah[2],
        (int)$pecah[0]
    );
}

/*
|--------------------------------------------------------------------------
| Tahun Ajaran
|--------------------------------------------------------------------------
|
| Contoh:
| 2026/2027
|
*/

function tahunAjaranValid($tahun)
{
    return preg_match('/^[0-9]{4}\/[0-9]{4}$/', $tahun);
}

/*
|--------------------------------------------------------------------------
| Panjang Tepat
|--------------------------------------------------------------------------
*/

function panjangTepat($nilai, $panjang)
{
    return strlen(trim($nilai)) == $panjang;
}

/*
|--------------------------------------------------------------------------
| Nomor HP Indonesia
|--------------------------------------------------------------------------
*/

function nomorHpValid($nomor)
{
    return preg_match('/^(08|\+628)[0-9]{8,13}$/', $nomor);
}

/*
|--------------------------------------------------------------------------
| Nilai 0 - 100
|--------------------------------------------------------------------------
*/

function nilaiValid($nilai)
{
    if (!is_numeric($nilai)) {
        return false;
    }

    return $nilai >= 0 && $nilai <= 100;
}

/*
|--------------------------------------------------------------------------
| Upload Gambar
|--------------------------------------------------------------------------
*/

function gambarValid($namaFile)
{
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    $izin = [

        "jpg",
        "jpeg",
        "png",
        "webp"

    ];

    return in_array($ext, $izin);
}

/*
|--------------------------------------------------------------------------
| Upload Excel
|--------------------------------------------------------------------------
*/

function excelValid($namaFile)
{
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    $izin = [

        "xls",
        "xlsx"

    ];

    return in_array($ext, $izin);
}

/*
|--------------------------------------------------------------------------
| Upload PDF
|--------------------------------------------------------------------------
*/

function pdfValid($namaFile)
{
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    return $ext == "pdf";
}