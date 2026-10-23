<?php

/*
|--------------------------------------------------------------------------
| TEMPLATE IMPORT SOAL
|--------------------------------------------------------------------------
| SIMU
| Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/*
|--------------------------------------------------------------------------
| MEMBUAT SPREADSHEET
|--------------------------------------------------------------------------
*/

$spreadsheet = new Spreadsheet();

/*
|--------------------------------------------------------------------------
| SHEET 1
|--------------------------------------------------------------------------
*/

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Template Soal');

/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

$sheet->setCellValue('A1', 'NO');
$sheet->setCellValue('B1', 'JENIS');
$sheet->setCellValue('C1', 'PERTANYAAN');
$sheet->setCellValue('D1', 'PILIHAN A');
$sheet->setCellValue('E1', 'PILIHAN B');
$sheet->setCellValue('F1', 'PILIHAN C');
$sheet->setCellValue('G1', 'PILIHAN D');
$sheet->setCellValue('H1', 'KUNCI JAWABAN');
$sheet->setCellValue('I1', 'BOBOT');

/*
|--------------------------------------------------------------------------
| STYLE HEADER
|--------------------------------------------------------------------------
*/

$sheet->getStyle('A1:I1')->applyFromArray([

    'font' => [

        'bold' => true,

        'color' => [

            'rgb' => 'FFFFFF'

        ]

    ],

    'fill' => [

        'fillType' => Fill::FILL_SOLID,

        'startColor' => [

            'rgb' => '0D6EFD'

        ]

    ],

    'alignment' => [

        'horizontal' => Alignment::HORIZONTAL_CENTER,

        'vertical' => Alignment::VERTICAL_CENTER

    ],

    'borders' => [

        'allBorders' => [

            'borderStyle' => Border::BORDER_THIN

        ]

    ]

]);

$sheet->getRowDimension(1)->setRowHeight(24);

/*
|--------------------------------------------------------------------------
| LEBAR KOLOM
|--------------------------------------------------------------------------
*/

$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(14);
$sheet->getColumnDimension('C')->setWidth(60);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(25);
$sheet->getColumnDimension('F')->setWidth(25);
$sheet->getColumnDimension('G')->setWidth(25);
$sheet->getColumnDimension('H')->setWidth(18);
$sheet->getColumnDimension('I')->setWidth(10);

/*
|--------------------------------------------------------------------------
| FREEZE HEADER
|--------------------------------------------------------------------------
*/

$sheet->freezePane('A2');

/*
|--------------------------------------------------------------------------
| LANJUT BAGIAN 2
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| CONTOH DATA
|--------------------------------------------------------------------------
| Contoh soal Pilihan Ganda
|--------------------------------------------------------------------------
*/

$sheet->setCellValue('A2', '1');
$sheet->setCellValue('B2', 'PG');
$sheet->setCellValue('C2', 'Hasil dari 2 + 3 adalah ....');
$sheet->setCellValue('D2', '4');
$sheet->setCellValue('E2', '5');
$sheet->setCellValue('F2', '6');
$sheet->setCellValue('G2', '7');
$sheet->setCellValue('H2', 'B');
$sheet->setCellValue('I2', '1');

/*
|--------------------------------------------------------------------------
| Contoh soal Isian
|--------------------------------------------------------------------------
*/

$sheet->setCellValue('A3', '2');
$sheet->setCellValue('B3', 'ISIAN');
$sheet->setCellValue('C3', 'Hasil dari √144 adalah ....');
$sheet->setCellValue('D3', '');
$sheet->setCellValue('E3', '');
$sheet->setCellValue('F3', '');
$sheet->setCellValue('G3', '');
$sheet->setCellValue('H3', '12');
$sheet->setCellValue('I3', '2');

/*
|--------------------------------------------------------------------------
| Contoh soal Uraian
|--------------------------------------------------------------------------
*/

$sheet->setCellValue('A4', '3');
$sheet->setCellValue('B4', 'URAIAN');
$sheet->setCellValue('C4', 'Jelaskan langkah-langkah menentukan FPB dari 24 dan 36.');
$sheet->setCellValue('D4', '');
$sheet->setCellValue('E4', '');
$sheet->setCellValue('F4', '');
$sheet->setCellValue('G4', '');
$sheet->setCellValue('H4', '');
$sheet->setCellValue('I4', '5');

/*
|--------------------------------------------------------------------------
| BORDER DATA
|--------------------------------------------------------------------------
*/

$sheet->getStyle('A2:I4')->applyFromArray([

    'borders' => [

        'allBorders' => [

            'borderStyle' => Border::BORDER_THIN

        ]

    ]

]);

/*
|--------------------------------------------------------------------------
| RATA TENGAH
|--------------------------------------------------------------------------
*/

$sheet->getStyle('A2:B4')->getAlignment()->setHorizontal(
    Alignment::HORIZONTAL_CENTER
);

$sheet->getStyle('H2:I4')->getAlignment()->setHorizontal(
    Alignment::HORIZONTAL_CENTER
);

/*
|--------------------------------------------------------------------------
| AUTO FILTER
|--------------------------------------------------------------------------
*/

$sheet->setAutoFilter('A1:I4');

/*
|--------------------------------------------------------------------------
| WRAP TEXT
|--------------------------------------------------------------------------
*/

$sheet->getStyle('C:C')->getAlignment()->setWrapText(true);

$sheet->getStyle('D:G')->getAlignment()->setWrapText(true);

/*
|--------------------------------------------------------------------------
| VERTICAL ALIGN
|--------------------------------------------------------------------------
*/

$sheet->getStyle('A:I')->getAlignment()->setVertical(
    Alignment::VERTICAL_TOP
);

/*
|--------------------------------------------------------------------------
| LANJUT BAGIAN 3
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| SHEET 2
|--------------------------------------------------------------------------
| PETUNJUK PENGISIAN
|--------------------------------------------------------------------------
*/

$petunjuk = $spreadsheet->createSheet();

$petunjuk->setTitle('Petunjuk');

/*
|--------------------------------------------------------------------------
| JUDUL
|--------------------------------------------------------------------------
*/

$petunjuk->setCellValue('A1', 'PETUNJUK PENGISIAN TEMPLATE IMPORT SOAL SIMU');

$petunjuk->mergeCells('A1:B1');

$petunjuk->getStyle('A1')->applyFromArray([

    'font' => [

        'bold' => true,

        'size' => 14,

        'color' => [

            'rgb' => 'FFFFFF'

        ]

    ],

    'fill' => [

        'fillType' => Fill::FILL_SOLID,

        'startColor' => [

            'rgb' => '0D6EFD'

        ]

    ],

    'alignment' => [

        'horizontal' => Alignment::HORIZONTAL_CENTER

    ]

]);

/*
|--------------------------------------------------------------------------
| HEADER TABEL
|--------------------------------------------------------------------------
*/

$petunjuk->setCellValue('A3', 'KOLOM');
$petunjuk->setCellValue('B3', 'KETERANGAN');

$petunjuk->getStyle('A3:B3')->applyFromArray([

    'font' => [

        'bold' => true,

        'color' => [

            'rgb' => 'FFFFFF'

        ]

    ],

    'fill' => [

        'fillType' => Fill::FILL_SOLID,

        'startColor' => [

            'rgb' => '198754'

        ]

    ],

    'alignment' => [

        'horizontal' => Alignment::HORIZONTAL_CENTER

    ],

    'borders' => [

        'allBorders' => [

            'borderStyle' => Border::BORDER_THIN

        ]

    ]

]);

/*
|--------------------------------------------------------------------------
| ISI PETUNJUK
|--------------------------------------------------------------------------
*/

$dataPetunjuk = [

    ['NO', 'Nomor urut soal.'],
    ['JENIS', 'Isi dengan PG, ISIAN, atau URAIAN.'],
    ['PERTANYAAN', 'Isi teks soal. Dapat menggunakan LaTeX (opsional).'],
    ['PILIHAN A', 'Diisi hanya untuk soal PG.'],
    ['PILIHAN B', 'Diisi hanya untuk soal PG.'],
    ['PILIHAN C', 'Diisi hanya untuk soal PG.'],
    ['PILIHAN D', 'Diisi hanya untuk soal PG.'],
    ['KUNCI JAWABAN', 'PG: A/B/C/D. ISIAN: jawaban singkat. URAIAN: boleh dikosongkan.'],
    ['BOBOT', 'Bobot nilai setiap soal. Gunakan angka positif.']

];

$baris = 4;

foreach ($dataPetunjuk as $item) {

    $petunjuk->setCellValue('A' . $baris, $item[0]);
    $petunjuk->setCellValue('B' . $baris, $item[1]);

    $baris++;

}

/*
|--------------------------------------------------------------------------
| CATATAN
|--------------------------------------------------------------------------
*/

$baris += 2;

$petunjuk->setCellValue('A' . $baris, 'CATATAN');

$petunjuk->getStyle('A' . $baris)->getFont()->setBold(true);

$baris++;

$petunjuk->setCellValue(
    'A' . $baris,
    '• Pilihan E tidak digunakan pada SIMU karena mengikuti standar SMP.'
);

$baris++;

$petunjuk->setCellValue(
    'A' . $baris,
    '• Rumus matematika dapat ditulis menggunakan LaTeX (opsional).'
);

$baris++;

$petunjuk->setCellValue(
    'A' . $baris,
    '• SIMU menggunakan MathJax untuk menampilkan rumus matematika.'
);

$baris++;

$petunjuk->setCellValue(
    'A' . $baris,
    '• Jangan mengubah nama header pada Sheet "Template Soal".'
);

/*
|--------------------------------------------------------------------------
| FORMAT
|--------------------------------------------------------------------------
*/

foreach (range('A', 'B') as $kolom) {

    $petunjuk->getColumnDimension($kolom)->setAutoSize(true);

}

$petunjuk->getStyle('A3:B' . ($baris - 4))->applyFromArray([

    'borders' => [

        'allBorders' => [

            'borderStyle' => Border::BORDER_THIN

        ]

    ]

]);

$petunjuk->freezePane('A4');

/*
|--------------------------------------------------------------------------
| LANJUT BAGIAN 4
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| KEMBALI KE SHEET PERTAMA
|--------------------------------------------------------------------------
*/

$spreadsheet->setActiveSheetIndex(0);

/*
|--------------------------------------------------------------------------
| NAMA FILE
|--------------------------------------------------------------------------
*/

$namaFile = 'Template_Import_Soal_SIMU.xlsx';

/*
|--------------------------------------------------------------------------
| HEADER DOWNLOAD
|--------------------------------------------------------------------------
*/

if (ob_get_length()) {

    ob_end_clean();

}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment; filename="' . $namaFile . '"');

header('Cache-Control: max-age=0');

header('Expires: 0');

header('Pragma: public');

/*
|--------------------------------------------------------------------------
| SIMPAN KE OUTPUT
|--------------------------------------------------------------------------
*/

$writer = new Xlsx($spreadsheet);

$writer->save('php://output');

exit;