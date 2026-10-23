<?php
/*
|--------------------------------------------------------------------------
| FOOTER TEMPLATE SIMU SISWA
| Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
*/
?>

</main>

<footer class="simu-footer">

    <div class="simu-footer-content">

        <div class="simu-footer-title">
            SIMU
        </div>

        <div class="simu-footer-text">
            Sistem Informasi Manajemen Ujian Matematika SMP
        </div>

        <div class="simu-footer-copy">
            &copy; <?= date('Y') ?> SIMU. Semua hak dilindungi.
        </div>

    </div>

</footer>

<style>
    /* =========================================================
       FOOTER SIMU
       ========================================================= */

    .simu-footer {
        width: 100%;
        margin-top: 30px;
        padding: 20px 15px;

        background: #1e3a8a;
        color: white;

        text-align: center;
    }

    .simu-footer-content {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .simu-footer-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .simu-footer-text {
        font-size: 12px;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .simu-footer-copy {
        font-size: 11px;
        opacity: 0.75;
    }

    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 600px) {

        .simu-footer {
            margin-top: 20px;
            padding: 16px 10px;
        }

        .simu-footer-title {
            font-size: 16px;
        }

        .simu-footer-text {
            font-size: 11px;
        }

        .simu-footer-copy {
            font-size: 10px;
        }
    }
</style>