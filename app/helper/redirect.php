<?php

/*
|--------------------------------------------------------------------------
| Redirect Halaman
|--------------------------------------------------------------------------
|
| Contoh:
|
| redirect("index.php");
| redirect("../dashboard.php");
|
*/

function redirect($url)
{
    header("Location: " . $url);
    exit;
}

/*
|--------------------------------------------------------------------------
| Redirect + Flash Message
|--------------------------------------------------------------------------
|
| Contoh:
|
| redirectFlash(
|     "index.php",
|     "Data berhasil disimpan.",
|     "success"
| );
|
*/

function redirectFlash($url, $pesan, $tipe = "success")
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash'] = [

        'pesan' => $pesan,

        'tipe'  => $tipe

    ];

    header("Location: " . $url);
    exit;
}