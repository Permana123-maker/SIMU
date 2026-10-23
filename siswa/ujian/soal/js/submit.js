/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Submit / Selesai Ujian
|--------------------------------------------------------------------------
*/

"use strict";

/*
|--------------------------------------------------------------------------
| Variabel
|--------------------------------------------------------------------------
*/

let prosesSelesai = false;


/*
|--------------------------------------------------------------------------
| Inisialisasi Submit
|--------------------------------------------------------------------------
*/

function initSubmit() {

    const btnSelesai = document.getElementById("btn-selesai");

    if (!btnSelesai) {
        return;
    }

    btnSelesai.addEventListener("click", function () {

        konfirmasiSelesai();

    });

}

/*
|--------------------------------------------------------------------------
| Konfirmasi Selesai
|--------------------------------------------------------------------------
*/

function konfirmasiSelesai() {

    if (prosesSelesai) {
        return;
    }

    const yakin = confirm(
        "Apakah Anda yakin ingin menyelesaikan ujian?\n\n" +
        "Pastikan semua jawaban sudah diperiksa."
    );

    if (!yakin) {
        return;
    }

    prosesSelesaiUjian();
}


/*
|--------------------------------------------------------------------------
| Proses Selesai Ujian
|--------------------------------------------------------------------------
*/

function prosesSelesaiUjian() {

    if (prosesSelesai) {
        return;
    }

    prosesSelesai = true;

    const btnSelesai = document.getElementById("btn-selesai");

    if (btnSelesai) {
        btnSelesai.disabled = true;
        btnSelesai.textContent = "Mengirim...";
    }

    const idSesi = document.getElementById("id_sesi").value;
    const idUjian = document.getElementById("id_ujian").value;

    const formData = new FormData();

    formData.append("id_sesi", idSesi);
    formData.append("id_ujian", idUjian);

    fetch("ajax/selesai_ujian.php", {
        method: "POST",
        body: formData
    })
    .then(function (response) {
        return response.json();
    })
    .then(function (hasil) {

        if (hasil.status === true) {

            window.location.href =
                "selesai.php?id_ujian=" + idUjian;

        } else {

            prosesSelesai = false;

            if (btnSelesai) {
                btnSelesai.disabled = false;
                btnSelesai.textContent = "Selesai Ujian";
            }

            alert(
                hasil.pesan || "Gagal menyelesaikan ujian."
            );
        }

    })
    .catch(function (error) {

        console.error(
            "Submit ujian error:",
            error
        );

        prosesSelesai = false;

        if (btnSelesai) {
            btnSelesai.disabled = false;
            btnSelesai.textContent = "Selesai Ujian";
        }

        alert(
            "Koneksi bermasalah. Ujian belum dapat diselesaikan."
        );

    });
}

/*
|--------------------------------------------------------------------------
| Selesai Otomatis
|--------------------------------------------------------------------------
*/

function selesaiOtomatis() {

    if (prosesSelesai) {
        return;
    }

    prosesSelesai = true;

    const idSesiElement = document.getElementById("id_sesi");
    const idUjianElement = document.getElementById("id_ujian");

    if (!idSesiElement || !idUjianElement) {
        return;
    }

    const idSesi = idSesiElement.value;
    const idUjian = idUjianElement.value;

    const formData = new FormData();

    formData.append("id_sesi", idSesi);
    formData.append("id_ujian", idUjian);

    fetch("ajax/selesai_ujian.php", {
        method: "POST",
        body: formData
    })
    .then(function (response) {
        return response.json();
    })
    .then(function (hasil) {

        if (hasil.status === true) {

            window.location.href =
                "selesai.php?id_ujian=" + idUjian;

        } else {

            prosesSelesai = false;

            alert(
                hasil.pesan ||
                "Ujian gagal diselesaikan otomatis."
            );

        }

    })
    .catch(function (error) {

        console.error(
            "Selesai otomatis error:",
            error
        );

        prosesSelesai = false;

        alert(
            "Koneksi bermasalah. Ujian belum dapat diselesaikan."
        );

    });
}


/*
|--------------------------------------------------------------------------
| Jalankan Submit
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    initSubmit();

});