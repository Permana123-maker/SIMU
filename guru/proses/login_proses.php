<?php

/*
|--------------------------------------------------------------------------
| LOGIN GURU - SIMU
|--------------------------------------------------------------------------
*/

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../app/database/database.php';

/*
|--------------------------------------------------------------------------
| Validasi Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: ../login.php');
    exit;

}

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if ($username === '' || $password === '') {

    header('Location: ../login.php?pesan=kosong');
    exit;

}

try {

    /*
    |--------------------------------------------------------------------------
    | Cari Guru
    |--------------------------------------------------------------------------
    */

    $sql = "

        SELECT
            id_guru,
            nama,
            username,
            password
        FROM guru
        WHERE username = ?
        AND dihapus_pada IS NULL
        LIMIT 1

    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$username]);

    $guru = $stmt->fetch();

    /*
    |--------------------------------------------------------------------------
    | Username / Password Salah
    |--------------------------------------------------------------------------
    */

    if (!$guru) {

        header('Location: ../login.php?pesan=gagal');
        exit;

    }

    if (!password_verify($password, $guru['password'])) {

        header('Location: ../login.php?pesan=gagal');
        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | Regenerasi Session
    |--------------------------------------------------------------------------
    */

    session_regenerate_id(true);

    /*
    |--------------------------------------------------------------------------
    | Simpan Session
    |--------------------------------------------------------------------------
    */

    $_SESSION['guru'] = [

        'id_guru'   => $guru['id_guru'],
        'nama'      => $guru['nama'],
        'username'  => $guru['username']

    ];

    /*
    |--------------------------------------------------------------------------
    | Update Login Terakhir
    |--------------------------------------------------------------------------
    */

    $update = $pdo->prepare("

        UPDATE guru

        SET terakhir_login = NOW()

        WHERE id_guru = ?

    ");

    $update->execute([

        $guru['id_guru']

    ]);

    /*
    |--------------------------------------------------------------------------
    | Redirect Dashboard
    |--------------------------------------------------------------------------
    */

    header('Location: ../dashboard.php');
    exit;

} catch (PDOException $e) {

    die('Kesalahan Database : ' . $e->getMessage());

}