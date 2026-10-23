<?php

/*
|--------------------------------------------------------------------------
| Helper Kode Soal SIMU
|--------------------------------------------------------------------------
|
| Format :
|
| SOAL01
| SOAL02
| SOAL03
|
| Berdasarkan nomor soal dalam satu paket.
|
*/

if (!function_exists('generateKodeSoal')) {

    function generateKodeSoal(int $nomor): string
    {

        /*
        |--------------------------------------------------------------------------
        | Validasi Nomor
        |--------------------------------------------------------------------------
        */

        if ($nomor < 1) {

            throw new InvalidArgumentException(
                "Nomor soal harus lebih besar dari 0."
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Hasil
        |--------------------------------------------------------------------------
        */

        return

            "SOAL" .

            str_pad(

                $nomor,

                2,

                "0",

                STR_PAD_LEFT

            );

    }

}