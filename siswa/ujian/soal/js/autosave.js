/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Auto Save Jawaban
|--------------------------------------------------------------------------
*/

"use strict";

/*
|--------------------------------------------------------------------------
| Variabel
|--------------------------------------------------------------------------
*/

let autosaveSedangBerjalan = false;
let jawabanTerakhir = "";

/*
|--------------------------------------------------------------------------
| Inisialisasi Autosave
|--------------------------------------------------------------------------
*/

function initAutosave() {

    const radioJawaban = document.querySelectorAll(
        'input[name="jawaban"]'
    );

    if (radioJawaban.length === 0) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil Jawaban Awal
    |--------------------------------------------------------------------------
    */

    const jawabanAwal = document.querySelector(
        'input[name="jawaban"]:checked'
    );

    if (jawabanAwal) {
        jawabanTerakhir = jawabanAwal.value;
    }

    /*
    |--------------------------------------------------------------------------
    | Event Perubahan Jawaban
    |--------------------------------------------------------------------------
    */

    radioJawaban.forEach(function (radio) {

        radio.addEventListener("change", function () {
            simpanJawaban();
        });

    });

}

/*
|--------------------------------------------------------------------------
| Simpan Jawaban
|--------------------------------------------------------------------------
*/

async function simpanJawaban() {

    if (autosaveSedangBerjalan) {
        return;
    }

    autosaveSedangBerjalan = true;

    ubahStatusAutosave("Menyimpan...", "text-warning");

    const idSesi = document.getElementById("id_sesi").value;
    const idSoal = document.getElementById("id_soal").value;

    const jawaban = document.querySelector(
        'input[name="jawaban"]:checked'
    );

    /*
    |--------------------------------------------------------------------------
    | Tidak Ada Jawaban
    |--------------------------------------------------------------------------
    */

    if (!jawaban) {
        autosaveSedangBerjalan = false;
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Cek Apakah Jawaban Berubah
    |--------------------------------------------------------------------------
    */

    if (jawaban.value === jawabanTerakhir) {
        autosaveSedangBerjalan = false;
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Data Yang Dikirim
    |--------------------------------------------------------------------------
    */

    const formData = new FormData();

    formData.append("id_sesi", idSesi);
    formData.append("id_soal", idSoal);
    formData.append("jawaban", jawaban.value);

    /*
    |--------------------------------------------------------------------------
    | Kirim AJAX
    |--------------------------------------------------------------------------
    */

    try {

        const response = await fetch(
            "ajax/simpan_jawaban.php",
            {
                method: "POST",
                body: formData
            }
        );

        const hasil = await response.json();

        /*
        |--------------------------------------------------------------------------
        | Response Berhasil
        |--------------------------------------------------------------------------
        */

        if (hasil.status === true) {

            ubahStatusAutosave(
                "Tersimpan",
                "text-success"
            );

            jawabanTerakhir = jawaban.value;

        } else {

            ubahStatusAutosave(
                hasil.pesan || "Gagal menyimpan",
                "text-danger"
            );

        }

    } catch (error) {

        ubahStatusAutosave(
            "Koneksi bermasalah",
            "text-danger"
        );

        console.error(
            "Autosave error:",
            error
        );

    } finally {

        autosaveSedangBerjalan = false;

    }
}

/*
|--------------------------------------------------------------------------
| Ubah Status Autosave
|--------------------------------------------------------------------------
*/

function ubahStatusAutosave(status, warna) {

    const element = document.getElementById("status-autosave");

    if (!element) {
        return;
    }

    element.classList.remove(
        "text-success",
        "text-warning",
        "text-danger"
    );

    element.classList.add(warna);

    element.textContent = status;
}


/*
|--------------------------------------------------------------------------
| Jalankan Autosave
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    initAutosave();

});

