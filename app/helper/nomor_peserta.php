<?php

/*
|--------------------------------------------------------------------------
| Helper Nomor Peserta SIMU
|--------------------------------------------------------------------------
|
| Format:
| 26 + 00001
| 26 + 00002
| 26 + 00003
|
| Dua digit pertama = Tahun mulai ajaran
| Lima digit berikut = Nomor urut
|
*/

if (!function_exists('generateNomorPeserta')) {

    function generateNomorPeserta(
        PDO $pdo,
        int $id_tahun,
        ?int &$nomorUrut = null
    ): string
    {

        /*
        |--------------------------------------------------------------------------
        | Ambil Tahun Mulai
        |--------------------------------------------------------------------------
        */

        $query = $pdo->prepare("
            SELECT
                tahun_mulai
            FROM tahun_ajaran
            WHERE
                id_tahun = ?
            LIMIT 1
        ");

        $query->execute([
            $id_tahun
        ]);

        $tahun = $query->fetch(PDO::FETCH_ASSOC);

        if (!$tahun) {

            throw new Exception(
                "Tahun ajaran tidak ditemukan."
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Prefix
        |--------------------------------------------------------------------------
        */

        $prefix = substr(
            $tahun['tahun_mulai'],
            2,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | Ambil Nomor Terakhir
        |--------------------------------------------------------------------------
        */

        if ($nomorUrut === null) {

            $query = $pdo->prepare("
                SELECT
                    nomor_peserta
                FROM siswa
                WHERE
                    nomor_peserta LIKE ?
                    AND dihapus_pada IS NULL
                ORDER BY
                    nomor_peserta DESC
                LIMIT 1
            ");

            $query->execute([
                $prefix . '%'
            ]);

            $data = $query->fetch(PDO::FETCH_ASSOC);

            if ($data) {

                $nomorUrut = (int) substr(
                    $data['nomor_peserta'],
                    2
                );

            } else {

                $nomorUrut = 0;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Nomor Berikutnya
        |--------------------------------------------------------------------------
        */

        $nomorUrut++;

        /*
        |--------------------------------------------------------------------------
        | Hasil
        |--------------------------------------------------------------------------
        */

        return

            $prefix .

            str_pad(

                $nomorUrut,

                5,

                "0",

                STR_PAD_LEFT

            );

    }

}