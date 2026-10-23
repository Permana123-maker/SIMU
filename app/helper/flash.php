<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Membuat Flash Message
|--------------------------------------------------------------------------
*/

function setFlash($pesan, $tipe = "success")
{
    $_SESSION['flash'] = [

        'pesan' => $pesan,

        'tipe' => $tipe

    ];
}

/*
|--------------------------------------------------------------------------
| Menampilkan Flash Message
|--------------------------------------------------------------------------
*/

function tampilFlash()
{

    if (!isset($_SESSION['flash'])) {

        return;

    }

    $flash = $_SESSION['flash'];

?>

<div class="alert alert-<?= htmlspecialchars($flash['tipe']); ?> alert-dismissible fade show">

    <?= htmlspecialchars($flash['pesan']); ?>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php

unset($_SESSION['flash']);

}