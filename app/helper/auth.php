<?php

/*
|--------------------------------------------------------------------------
| AUTH HELPER SIMU
|--------------------------------------------------------------------------
|
| Digunakan untuk mengecek login guru dan logout.
|
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/redirect.php";
require_once __DIR__ . "/flash.php";

/*
|--------------------------------------------------------------------------
| Cek Login Guru
|--------------------------------------------------------------------------
*/

function cekLoginGuru()
{
    if (!isset($_SESSION['guru'])) {

        redirectFlash(
            "../../login.php",
            "Silakan login terlebih dahulu.",
            "warning"
        );

    }
}

/*
|--------------------------------------------------------------------------
| Cek Jika Sudah Login
|--------------------------------------------------------------------------
|
| Digunakan di halaman login.
|
*/

function cekSudahLogin()
{
    if (isset($_SESSION['guru'])) {

        redirect("../../guru/dashboard.php");

    }
}

/*
|--------------------------------------------------------------------------
| Login Guru
|--------------------------------------------------------------------------
*/

function loginGuru(array $guru)
{
    $_SESSION['guru'] = [

        'id' => $guru['id_guru'],

        'nama' => $guru['nama_guru'],

        'username' => $guru['username']

    ];
}

/*
|--------------------------------------------------------------------------
| Data Guru Login
|--------------------------------------------------------------------------
*/

function guru()
{
    return $_SESSION['guru'] ?? null;
}

/*
|--------------------------------------------------------------------------
| Apakah Sudah Login
|--------------------------------------------------------------------------
*/

function sudahLogin()
{
    return isset($_SESSION['guru']);
}

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

function logoutGuru()
{

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(

            session_name(),

            '',

            time() - 42000,

            $params["path"],

            $params["domain"],

            $params["secure"],

            $params["httponly"]

        );

    }

    session_destroy();

    redirect("../../login.php");

}