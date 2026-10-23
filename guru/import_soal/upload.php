<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Upload Import Soal
|--------------------------------------------------------------------------
| Membaca file Excel kemudian menyimpannya ke Session
| untuk ditampilkan pada halaman Preview.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../../app/keamanan/session.php';
require_once __DIR__ . '/../../app/database/database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/*
|--------------------------------------------------------------------------
| VALIDASI REQUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['error'] = "Akses tidak diizinkan.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| AMBIL TAHUN PELAJARAN AKTIF
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT
        id_tahun,
        nama_tahun
    FROM tahun_ajaran
    WHERE aktif = 'Ya'
    LIMIT 1

";

$stmt = $pdo->query($sql);

$tahun = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tahun) {

    $_SESSION['error'] = "Belum ada Tahun Pelajaran yang aktif.";

    header("Location: index.php");

    exit;

}

$idTahun = (int) $tahun['id_tahun'];

/*
|--------------------------------------------------------------------------
| VALIDASI JENJANG
|--------------------------------------------------------------------------
*/

$jenjang = trim($_POST['jenjang'] ?? '');

$daftarJenjang = [

    'VII',
    'VIII',
    'IX'

];

if (!in_array($jenjang, $daftarJenjang)) {

    $_SESSION['error'] = "Silakan pilih jenjang terlebih dahulu.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| VALIDASI FILE
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['file_excel'])) {

    $_SESSION['error'] = "File Excel belum dipilih.";

    header("Location: index.php");

    exit;

}

$file = $_FILES['file_excel'];

if ($file['error'] !== UPLOAD_ERR_OK) {

    $_SESSION['error'] = "Upload file gagal.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| VALIDASI EKSTENSI
|--------------------------------------------------------------------------
*/

$ekstensi = strtolower(

    pathinfo(

        $file['name'],

        PATHINFO_EXTENSION

    )

);

$ekstensiDiizinkan = [

    'xlsx',
    'xls'

];

if (!in_array($ekstensi, $ekstensiDiizinkan)) {

    $_SESSION['error'] = "Format file harus .xlsx atau .xls.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| VALIDASI UKURAN FILE
|--------------------------------------------------------------------------
*/

$ukuranMaksimal = 5 * 1024 * 1024;

if ($file['size'] > $ukuranMaksimal) {

    $_SESSION['error'] = "Ukuran file maksimal 5 MB.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| LANJUT BAGIAN 2
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| MEMBACA FILE EXCEL
|--------------------------------------------------------------------------
*/

try {

    $spreadsheet = IOFactory::load($file['tmp_name']);

    $sheet = $spreadsheet->getActiveSheet();

} catch (Exception $e) {

    $_SESSION['error'] = "File Excel tidak dapat dibaca.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| VALIDASI HEADER TEMPLATE
|--------------------------------------------------------------------------
*/

$header = [

    strtoupper(trim((string) $sheet->getCell('A1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('B1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('C1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('D1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('E1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('F1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('G1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('H1')->getValue())),
    strtoupper(trim((string) $sheet->getCell('I1')->getValue()))

];

$template = [

    'NO',
    'JENIS',
    'PERTANYAAN',
    'PILIHAN A',
    'PILIHAN B',
    'PILIHAN C',
    'PILIHAN D',
    'KUNCI JAWABAN',
    'BOBOT'

];

if ($header !== $template) {

    $_SESSION['error'] =
        "Template Excel tidak sesuai. Silakan gunakan Template Import Soal SIMU terbaru.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| BARIS TERAKHIR
|--------------------------------------------------------------------------
*/

$barisTerakhir = $sheet->getHighestRow();

if ($barisTerakhir < 2) {

    $_SESSION['error'] = "File Excel tidak memiliki data soal.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| ARRAY HASIL IMPORT
|--------------------------------------------------------------------------
*/

$hasilImport = [];

/*
|--------------------------------------------------------------------------
| LANJUT BAGIAN 3
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| MEMBACA SETIAP BARIS
|--------------------------------------------------------------------------
*/

for ($baris = 2; $baris <= $barisTerakhir; $baris++) {

    $nomor       = trim((string) $sheet->getCell('A' . $baris)->getValue());
    $jenis       = strtoupper(trim((string) $sheet->getCell('B' . $baris)->getValue()));
    $pertanyaan  = trim((string) $sheet->getCell('C' . $baris)->getValue());

    $pilihanA    = trim((string) $sheet->getCell('D' . $baris)->getValue());
    $pilihanB    = trim((string) $sheet->getCell('E' . $baris)->getValue());
    $pilihanC    = trim((string) $sheet->getCell('F' . $baris)->getValue());
    $pilihanD    = trim((string) $sheet->getCell('G' . $baris)->getValue());

    $kunci       = strtoupper(trim((string) $sheet->getCell('H' . $baris)->getValue()));

    $bobot       = trim((string) $sheet->getCell('I' . $baris)->getValue());

    /*
    |--------------------------------------------------------------------------
    | LEWATI BARIS KOSONG
    |--------------------------------------------------------------------------
    */

    if (

        $nomor == '' &&
        $jenis == '' &&
        $pertanyaan == '' &&
        $pilihanA == '' &&
        $pilihanB == '' &&
        $pilihanC == '' &&
        $pilihanD == '' &&
        $kunci == '' &&
        $bobot == ''

    ) {

        continue;

    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $status = "VALID";

    $keterangan = [];

    /*
    |--------------------------------------------------------------------------
    | VALIDASI NOMOR
    |--------------------------------------------------------------------------
    */

    if ($nomor == '') {

        $status = "ERROR";

        $keterangan[] = "Nomor kosong.";

    } elseif (!is_numeric($nomor)) {

        $status = "ERROR";

        $keterangan[] = "Nomor harus berupa angka.";

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI JENIS
    |--------------------------------------------------------------------------
    */

    if (!in_array($jenis, ['PG', 'ISIAN', 'URAIAN'])) {

        $status = "ERROR";

        $keterangan[] = "Jenis harus PG, ISIAN, atau URAIAN.";

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI PERTANYAAN
    |--------------------------------------------------------------------------
    */

    if ($pertanyaan == '') {

        $status = "ERROR";

        $keterangan[] = "Pertanyaan belum diisi.";

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI BOBOT
    |--------------------------------------------------------------------------
    */

    if ($bobot == '') {

        $status = "ERROR";

        $keterangan[] = "Bobot belum diisi.";

    } elseif (!is_numeric($bobot)) {

        $status = "ERROR";

        $keterangan[] = "Bobot harus berupa angka.";

    } elseif ($bobot <= 0) {

        $status = "ERROR";

        $keterangan[] = "Bobot minimal 1.";

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI SOAL PG
    |--------------------------------------------------------------------------
    */

    if ($jenis == 'PG') {

        if (
            $pilihanA == '' ||
            $pilihanB == '' ||
            $pilihanC == '' ||
            $pilihanD == ''
        ) {

            $status = "ERROR";

            $keterangan[] = "Pilihan A-D wajib diisi.";

        }

        if (!in_array($kunci, ['A', 'B', 'C', 'D'])) {

            $status = "ERROR";

            $keterangan[] = "Kunci PG harus A, B, C, atau D.";

        }

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI SOAL ISIAN
    |--------------------------------------------------------------------------
    */

    if ($jenis == 'ISIAN') {

        if ($kunci == '') {

            $status = "ERROR";

            $keterangan[] = "Kunci jawaban ISIAN belum diisi.";

        }

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI SOAL URAIAN
    |--------------------------------------------------------------------------
    */

    if ($jenis == 'URAIAN') {

        /*
        | Untuk URAIAN:
        | Pilihan A-D boleh kosong.
        | Kunci boleh kosong.
        */

    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN KE ARRAY
    |--------------------------------------------------------------------------
    */

    $hasilImport[] = [

        'baris'        => $baris,
        'nomor'        => (int) $nomor,
        'jenis'        => $jenis,
        'pertanyaan'   => $pertanyaan,

        'pilihan_a'    => $pilihanA,
        'pilihan_b'    => $pilihanB,
        'pilihan_c'    => $pilihanC,
        'pilihan_d'    => $pilihanD,

        'kunci'        => $kunci,
        'bobot'        => (int) $bobot,

        'status'       => $status,

        'keterangan'   => implode(' ', $keterangan)

    ];

}

/*
|--------------------------------------------------------------------------
| VALIDASI HASIL IMPORT
|--------------------------------------------------------------------------
*/

if (count($hasilImport) == 0) {

    $_SESSION['error'] = "Tidak ada data soal yang dapat diimport.";

    header("Location: index.php");

    exit;

}

/*
|--------------------------------------------------------------------------
| LANJUT BAGIAN 4
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| HITUNG JUMLAH DATA
|--------------------------------------------------------------------------
*/

$jumlahData = count($hasilImport);

$jumlahValid = 0;

$jumlahError = 0;

foreach ($hasilImport as $item) {

    if ($item['status'] === 'VALID') {

        $jumlahValid++;

    } else {

        $jumlahError++;

    }

}

/*
|--------------------------------------------------------------------------
| SIMPAN KE SESSION
|--------------------------------------------------------------------------
*/

$_SESSION['import_soal'] = $hasilImport;

$_SESSION['import_soal_info'] = [

    'id_tahun'      => $idTahun,

    'nama_tahun'    => $tahun['nama_tahun'],

    'jenjang'       => $jenjang,

    'jumlah_data'   => $jumlahData,

    'jumlah_valid'  => $jumlahValid,

    'jumlah_error'  => $jumlahError,

    'nama_file'     => $file['name'],

    'tanggal_import'=> date('Y-m-d H:i:s')

];

/*
|--------------------------------------------------------------------------
| PINDAH KE HALAMAN PREVIEW
|--------------------------------------------------------------------------
*/

header("Location: preview.php");

exit;