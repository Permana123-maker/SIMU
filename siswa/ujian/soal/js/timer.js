/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Timer Ujian
|--------------------------------------------------------------------------
*/

"use strict";

/*
|--------------------------------------------------------------------------
| Variabel Timer
|--------------------------------------------------------------------------
*/

let timerInterval = null;

/*
|--------------------------------------------------------------------------
| Inisialisasi Timer
|--------------------------------------------------------------------------
*/

function initTimer() {

    const timer = document.getElementById("timer");

    if (!timer) {

        return;

    }

    const waktuSelesai = parseInt(timer.dataset.selesai);

    if (isNaN(waktuSelesai)) {

        console.error("Waktu selesai tidak ditemukan.");

        return;

    }

    updateTimer(timer, waktuSelesai);

    timerInterval = setInterval(function () {

        updateTimer(timer, waktuSelesai);

    }, 1000);

}

/*
|--------------------------------------------------------------------------
| Update Timer
|--------------------------------------------------------------------------
*/

function updateTimer(timer, waktuSelesai) {

    const sekarang = Math.floor(Date.now() / 1000);

    let sisa = waktuSelesai - sekarang;

    /*
    |--------------------------------------------------------------------------
    | Waktu Habis
    |--------------------------------------------------------------------------
    */

    if (sisa <= 0) {

        clearInterval(timerInterval);

        timer.innerHTML = "00:00:00";

        selesaiOtomatis();

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Hitung Jam Menit Detik
    |--------------------------------------------------------------------------
    */

    const jam = Math.floor(sisa / 3600);

    const menit = Math.floor((sisa % 3600) / 60);

    const detik = sisa % 60;

    timer.innerHTML =

        String(jam).padStart(2, "0")

        + ":"

        + String(menit).padStart(2, "0")

        + ":"

        + String(detik).padStart(2, "0");

    /*
    |--------------------------------------------------------------------------
    | Warna Timer
    |--------------------------------------------------------------------------
    */

    if (sisa <= 300) {

        timer.classList.remove(

            "text-success",

            "text-warning"

        );

        timer.classList.add("text-danger");

    }

    else if (sisa <= 900) {

        timer.classList.remove(

            "text-success",

            "text-danger"

        );

        timer.classList.add("text-warning");

    }

    else {

        timer.classList.remove(

            "text-warning",

            "text-danger"

        );

        timer.classList.add("text-success");

    }

}

/*
|--------------------------------------------------------------------------
| Jalankan Timer
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    initTimer();

});