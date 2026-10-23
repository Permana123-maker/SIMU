<?php

date_default_timezone_set('Asia/Jakarta');

$host = "localhost";
$database = "cbt_matematika";
$user = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    die("Koneksi Database Gagal : " . $e->getMessage());

}