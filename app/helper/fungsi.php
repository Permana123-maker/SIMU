<?php

/*
|--------------------------------------------------------------------------
| Fungsi Umum SIMU
|--------------------------------------------------------------------------
|
| Seluruh fungsi umum aplikasi diletakkan di sini.
|
*/

/*
|--------------------------------------------------------------------------
| Membersihkan Input
|--------------------------------------------------------------------------
*/

function bersihkan($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| Format Tanggal Indonesia
|--------------------------------------------------------------------------
|
| Contoh:
| echo tanggalIndonesia('2026-07-15');
| Hasil:
| 15 Juli 2026
|
*/

function tanggalIndonesia($tanggal)
{
    if (empty($tanggal)) {
        return "-";
    }

    $bulan = [

        1 => "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"

    ];

    $pecah = explode("-", substr($tanggal,0,10));

    return $pecah[2]." ".$bulan[(int)$pecah[1]]." ".$pecah[0];
}

/*
|--------------------------------------------------------------------------
| Format Tanggal + Jam
|--------------------------------------------------------------------------
*/

function tanggalJamIndonesia($datetime)
{
    if(empty($datetime)){
        return "-";
    }

    return tanggalIndonesia($datetime)." ".substr($datetime,11,5);
}

/*
|--------------------------------------------------------------------------
| Status Aktif
|--------------------------------------------------------------------------
*/

function badgeAktif($aktif)
{

    if($aktif=="Ya"){

        return '<span class="badge bg-success">Aktif</span>';

    }

    return '<span class="badge bg-secondary">Tidak Aktif</span>';

}

/*
|--------------------------------------------------------------------------
| Status Tahun Ajaran
|--------------------------------------------------------------------------
*/

function badgeStatus($status)
{

    switch($status){

        case "Berjalan":

            return '<span class="badge bg-success">Berjalan</span>';

        case "Akan Datang":

            return '<span class="badge bg-warning text-dark">Akan Datang</span>';

        case "Selesai":

            return '<span class="badge bg-secondary">Selesai</span>';

        default:

            return '<span class="badge bg-dark">'.$status.'</span>';

    }

}

/*
|--------------------------------------------------------------------------
| Membatasi Karakter
|--------------------------------------------------------------------------
*/

function potongTeks($teks,$jumlah=50)
{

    if(strlen($teks)<=$jumlah){

        return $teks;

    }

    return substr($teks,0,$jumlah)." ...";

}

/*
|--------------------------------------------------------------------------
| Generate Token
|--------------------------------------------------------------------------
|
| Contoh:
| echo generateToken(6);
|
*/

function generateToken($panjang=6)
{

    return substr(str_shuffle("123456789ABCDEFGHJKLMNPQRSTUVWXYZ"),0,$panjang);

}

/*
|--------------------------------------------------------------------------
| Generate Angka Acak
|--------------------------------------------------------------------------
*/

function angkaAcak($min=100000,$max=999999)
{

    return rand($min,$max);

}

/*
|--------------------------------------------------------------------------
| Format Nilai
|--------------------------------------------------------------------------
*/

function formatNilai($nilai)
{

    return number_format($nilai,2,",",".");

}

/*
|--------------------------------------------------------------------------
| Cek Request POST
|--------------------------------------------------------------------------
*/

function isPost()
{

    return $_SERVER['REQUEST_METHOD']=="POST";

}

/*
|--------------------------------------------------------------------------
| Cek Request GET
|--------------------------------------------------------------------------
*/

function isGet()
{

    return $_SERVER['REQUEST_METHOD']=="GET";

}