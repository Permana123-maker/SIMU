/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Navigasi Soal
|--------------------------------------------------------------------------
*/

"use strict";

/*
|--------------------------------------------------------------------------
| Inisialisasi Navigasi
|--------------------------------------------------------------------------
*/

function initNavigation() {

    /*
    |--------------------------------------------------------------------------
    | Tombol Sebelumnya
    |--------------------------------------------------------------------------
    */

    const btnSebelumnya = document.getElementById(

        "btn-sebelumnya"

    );

    if (btnSebelumnya) {

        btnSebelumnya.addEventListener(

            "click",

            function () {

                pindahSoal(

                    this.dataset.nomor

                );

            }

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Tombol Berikutnya
    |--------------------------------------------------------------------------
    */

    const btnBerikutnya = document.getElementById(

        "btn-berikutnya"

    );

    if (btnBerikutnya) {

        btnBerikutnya.addEventListener(

            "click",

            function () {

                pindahSoal(

                    this.dataset.nomor

                );

            }

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Nomor Soal
    |--------------------------------------------------------------------------
    */

    const daftarNomor = document.querySelectorAll(

        "[data-nomor]"

    );

    daftarNomor.forEach(function (item) {

        item.addEventListener(

            "click",

            function () {

                pindahSoal(

                    this.dataset.nomor

                );

            }

        );

    });

}

/*
|--------------------------------------------------------------------------
| Pindah Soal
|--------------------------------------------------------------------------
*/

function pindahSoal(nomor) {

    /*
    |--------------------------------------------------------------------------
    | Validasi Nomor
    |--------------------------------------------------------------------------
    */

    nomor = parseInt(nomor);

    if (isNaN(nomor)) {

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Ambil ID Ujian
    |--------------------------------------------------------------------------
    */

    const idUjian = document.getElementById(

        "id_ujian"

    ).value;

    /*
    |--------------------------------------------------------------------------
    | Pindah Halaman
    |--------------------------------------------------------------------------
    */

    window.location.href =

        "soal.php?id_ujian="

        + idUjian

        + "&nomor="

        + nomor;

}

/*
|--------------------------------------------------------------------------
| Pindah Soal
|--------------------------------------------------------------------------
*/

function pindahSoal(nomor) {

    /*
    |--------------------------------------------------------------------------
    | Validasi Nomor
    |--------------------------------------------------------------------------
    */

    nomor = parseInt(nomor);

    if (isNaN(nomor)) {

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Tunggu Autosave
    |--------------------------------------------------------------------------
    */

    if (autosaveSedangBerjalan) {

        const tunggu = setInterval(function () {

            if (!autosaveSedangBerjalan) {

                clearInterval(tunggu);

                pindahSoal(nomor);

            }

        }, 100);

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Ambil ID Ujian
    |--------------------------------------------------------------------------
    */

    const idUjian = document.getElementById(

        "id_ujian"

    ).value;

    /*
    |--------------------------------------------------------------------------
    | Pindah Halaman
    |--------------------------------------------------------------------------
    */

    window.location.href =

        "soal.php?id_ujian="

        + idUjian

        + "&nomor="

        + nomor;

}