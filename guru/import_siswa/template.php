<?php

require_once "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Template Siswa');

/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'NIS');
$sheet->setCellValue('C1', 'Nama Lengkap');
$sheet->setCellValue('D1', 'Jenis Kelamin');
$sheet->setCellValue('E1', 'Kelas');

/*
|--------------------------------------------------------------------------
| CONTOH DATA
|--------------------------------------------------------------------------
*/

$sheet->setCellValue('A2', 1);
$sheet->setCellValue('B2', '240001');
$sheet->setCellValue('C2', 'Ahmad Fauzan');
$sheet->setCellValue('D2', 'L');
$sheet->setCellValue('E2', 'VII 1');

$sheet->setCellValue('A3', 2);
$sheet->setCellValue('B3', '240002');
$sheet->setCellValue('C3', 'Siti Aisyah');
$sheet->setCellValue('D3', 'P');
$sheet->setCellValue('E3', 'VII 1');

$sheet->setCellValue('A4', 3);
$sheet->setCellValue('B4', '240003');
$sheet->setCellValue('C4', 'Budi Santoso');
$sheet->setCellValue('D4', 'L');
$sheet->setCellValue('E4', 'VII 2');

/*
|--------------------------------------------------------------------------
| LEBAR KOLOM
|--------------------------------------------------------------------------
*/

$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(18);
$sheet->getColumnDimension('C')->setWidth(40);
$sheet->getColumnDimension('D')->setWidth(18);
$sheet->getColumnDimension('E')->setWidth(15);

/*
|--------------------------------------------------------------------------
| STYLE HEADER
|--------------------------------------------------------------------------
*/

$sheet->getStyle('A1:E1')->getFont()
    ->setBold(true)
    ->getColor()
    ->setRGB('FFFFFF');

$sheet->getStyle('A1:E1')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$sheet->getStyle('A1:E1')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setRGB('0D6EFD');

/*
|--------------------------------------------------------------------------
| BORDER
|--------------------------------------------------------------------------
*/

$sheet->getStyle('A1:E4')
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

/*
|--------------------------------------------------------------------------
| FORMAT NOMOR
|--------------------------------------------------------------------------
*/

$sheet->getStyle('A2:A1000')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getStyle('D2:D1000')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getStyle('E2:E1000')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

/*
|--------------------------------------------------------------------------
| FREEZE HEADER
|--------------------------------------------------------------------------
*/

$sheet->freezePane('A2');

/*
|--------------------------------------------------------------------------
| DOWNLOAD FILE
|--------------------------------------------------------------------------
*/

$filename = "Template_Import_Siswa.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;