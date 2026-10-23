<?php

/*
|--------------------------------------------------------------------------
| Helper Nomor Soal SIMU
|--------------------------------------------------------------------------
|
| Menghasilkan nomor soal berikutnya berdasarkan paket soal.
|
| Contoh:
|
| Paket A
| -------
| 1
| 2
| 3
|
| Maka menghasilkan:
| 4
|
*/

if (!function_exists('generateNomorSoal')) {

    function generateNomorSoal(
        PDO $pdo,
        int $id_paket
    ): int
    {

        /*
        |--------------------------------------------------------------------------
        | Ambil Nomor Terbesar
        |--------------------------------------------------------------------------
        */

        $query = $pdo->prepare("
            SELECT
                MAX(nomor) AS nomor_terakhir
            FROM soal
            WHERE
                id_paket = ?
                AND dihapus_pada IS NULL
        ");

        $query->execute([
            $id_paket
        ]);

        $data = $query->fetch(PDO::FETCH_ASSOC);

        /*
        |--------------------------------------------------------------------------
        | Nomor Berikutnya
        |--------------------------------------------------------------------------
        */

        if (

            !$data ||
            $data['nomor_terakhir'] === null

        ) {

            return 1;

        }

        return

            ((int) $data['nomor_terakhir']) + 1;

    }

}