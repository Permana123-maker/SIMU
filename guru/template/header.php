<?php

/*
|--------------------------------------------------------------------------
| HEADER TEMPLATE SIMU
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';

if (!isset($_SESSION['guru'])) {
    header("Location: " . BASE_URL . "guru/login.php");
    exit;
}

$nama_guru = "Guru";

if (is_array($_SESSION['guru'])) {
    $nama_guru = $_SESSION['guru']['nama'] ?? "Guru";
} else {
    $nama_guru = $_SESSION['guru'];
}

$page_title = $page_title ?? "Dashboard";

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= htmlspecialchars($page_title) ?> | <?= NAMA_APLIKASI ?></title>

<!-- Bootstrap -->

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<!-- Bootstrap Icons -->

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- CSS SIMU -->

<link rel="stylesheet"
href="<?= BASE_URL ?>assets/css/style.css">

</head>

<body>

<div class="app">

<?php require_once __DIR__ . '/navbar.php'; ?>

<div class="app-body">

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main-wrapper">

<?php require_once __DIR__ . '/topbar.php'; ?>

<div class="content-wrapper">