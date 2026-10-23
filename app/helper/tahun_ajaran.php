<?php

/*
|--------------------------------------------------------------------------
| HELPER TAHUN AJARAN
|--------------------------------------------------------------------------
|
| Digunakan untuk mengambil data tahun ajaran yang sedang digunakan
| oleh sistem SIMU.
|
| Karena SIMU hanya menggunakan SATU tahun ajaran aktif,
| maka helper ini akan selalu mengambil data tahun ajaran
| terakhir yang belum dihapus.
|
*/

if (!function_exists('getTahunAjaran')) {

    function getTahunAjaran(PDO $pdo): array
    {
        $query = $pdo->prepare("
            SELECT
                id_tahun,
                nama_tahun
            FROM tahun_ajaran
            WHERE dihapus_pada IS NULL
            ORDER BY id_tahun DESC
            LIMIT 1
        ");

        $query->execute();

        $tahun = $query->fetch(PDO::FETCH_ASSOC);

        return $tahun ?: [];
    }

}