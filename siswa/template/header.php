```php
<?php
/*
|--------------------------------------------------------------------------
| HEADER TEMPLATE SIMU
| Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
*/

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="SIMU - Sistem Informasi Manajemen Ujian Matematika SMP">

    <title>
        <?= isset($title) ? htmlspecialchars($title) . ' - SIMU' : 'SIMU' ?>
    </title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            color: #333;
            line-height: 1.5;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        /* =====================================================
           CONTAINER
           ===================================================== */

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* =====================================================
           HEADER UTAMA
           ===================================================== */

        .simu-header {
            width: 100%;
            background: #1e3a8a;
            color: white;
            padding: 15px 20px;
        }

        .simu-header-inner {
            max-width: 1200px;
            margin: auto;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .simu-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .simu-logo {
            width: 42px;
            height: 42px;
            background: white;
            color: #1e3a8a;
            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
            font-weight: bold;
        }

        .simu-brand-text h1 {
            font-size: 20px;
            margin: 0;
        }

        .simu-brand-text p {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 2px;
        }

        /* =====================================================
           CONTENT
           ===================================================== */

        .page-content {
            width: 100%;
            min-height: calc(100vh - 80px);
        }

        /* =====================================================
           CARD
           ===================================================== */

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        /* =====================================================
           FORM
           ===================================================== */

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 11px 13px;

            border: 1px solid #d1d5db;
            border-radius: 7px;

            font-size: 14px;
            outline: none;

            background: white;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        /* =====================================================
           BUTTON
           ===================================================== */

        .btn {
            display: inline-block;

            border: none;
            border-radius: 7px;

            padding: 11px 18px;

            font-size: 14px;
            font-weight: bold;

            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        /* =====================================================
           ALERT
           ===================================================== */

        .alert {
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        /* =====================================================
           FOOTER
           ===================================================== */

        .simu-footer {
            text-align: center;
            padding: 20px;

            font-size: 12px;
            color: #6b7280;
        }

        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 600px) {

            .simu-header {
                padding: 12px 15px;
            }

            .simu-logo {
                width: 36px;
                height: 36px;
                font-size: 17px;
            }

            .simu-brand-text h1 {
                font-size: 17px;
            }

            .simu-brand-text p {
                font-size: 10px;
            }

            .container {
                padding: 15px;
            }

            .card {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<header class="simu-header">

    <div class="simu-header-inner">

        <div class="simu-brand">

            <div class="simu-logo">
                M
            </div>

            <div class="simu-brand-text">

                <h1>SIMU</h1>

                <p>
                    Sistem Informasi Manajemen Ujian Matematika SMP
                </p>

            </div>

        </div>

    </div>

</header>

<main class="page-content">
```
