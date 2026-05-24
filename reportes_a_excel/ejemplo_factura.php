<?php
require '../vendor/autoload.php'; // Asegúrate de tener PhpSpreadsheet instalado con Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Crear nuevo documento
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Datos de ejemplo para la factura
$sheet->setCellValue('A1', 'FACTURA');
$sheet->mergeCells('A1:D1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Cliente: Juan Pérez');
$sheet->setCellValue('A3', 'Fecha: ' . date('d/m/Y'));

$sheet->setCellValue('A5', 'Producto');
$sheet->setCellValue('B5', 'Cantidad');
$sheet->setCellValue('C5', 'Precio Unit.');
$sheet->setCellValue('D5', 'Total');

$sheet->fromArray([
    ['Laptop', 1, 800, 800],
    ['Mouse', 2, 15, 30],
    ['Teclado', 1, 25, 25],
], null, 'A6');

// Total final
$sheet->setCellValue('C9', 'TOTAL:');
$sheet->setCellValue('D9', '=SUM(D6:D8)');

// Ajustar ancho de columnas
foreach (range('A', 'D') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// También podemos aplicar bordes internos finos si se desea
$sheet->getStyle('A5:D9')->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
]);

// Aplicar bordes externos gruesos a toda la factura
$sheet->getStyle('A5:D9')->applyFromArray([
    'borders' => [
        'outline' => [
            'borderStyle' => Border::BORDER_THICK,
            'color' => ['argb' => '000000'],
        ],
    ],
]);

$sheet->mergeCells('A9:B9');

// Guardar archivo
$writer = new Xlsx($spreadsheet);
$writer->save('factura.xlsx');

echo "Factura generada con bordes externos gruesos.";
