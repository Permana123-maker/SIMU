<?php
/*
|--------------------------------------------------------------------------
| TOPBAR TEMPLATE SIMU
|--------------------------------------------------------------------------
| Versi Final
|--------------------------------------------------------------------------
*/
?>

<div class="topbar">

    <div class="topbar-left">

        <h4 class="mb-1 fw-bold">

            <?= htmlspecialchars($page_title); ?>

        </h4>

        <small class="text-muted">

            Sistem Informasi Manajemen Ujian Matematika SMP

        </small>

    </div>

    <div class="topbar-right">

        <div class="text-end me-4">

            <div id="tanggal">

                <?= date('d F Y'); ?>

            </div>

            <div id="jam">

                --:--:--

            </div>

        </div>

        <div class="avatar">

            <?= strtoupper(substr($nama_guru, 0, 1)); ?>

        </div>

        <div>

            <div class="fw-bold">

                <?= htmlspecialchars($nama_guru); ?>

            </div>

            <small class="text-muted">

                Guru Matematika

            </small>

        </div>

    </div>

</div>

<script>

function updateJam() {

    const sekarang = new Date();

    const jam =
        String(sekarang.getHours()).padStart(2, '0') + ':' +
        String(sekarang.getMinutes()).padStart(2, '0') + ':' +
        String(sekarang.getSeconds()).padStart(2, '0');

    const elemenJam = document.getElementById('jam');

    const elemenClock = document.getElementById('clock');

    if (elemenJam) {
        elemenJam.innerHTML = jam;
    }

    if (elemenClock) {
        elemenClock.innerHTML = jam;
    }

}

updateJam();

setInterval(updateJam, 1000);

</script>