<?php
require_once "../scripts/clases/class.mysql.php";
require_once "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};

$db = new MySQL();

$id_paralelo = $_POST['id_paralelo'];

$query = "SELECT td_nombre, es_cedula, dn_nombre, es_apellidos, es_nombres, dg_nombre, es_email, es_sector, es_direccion, es_telefono, es_fec_nacim FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_def_genero dg, sw_tipo_documento td, sw_def_nacionalidad dn WHERE e.id_estudiante = ep.id_estudiante AND dg.id_def_genero = e.id_def_genero AND td.id_tipo_documento = e.id_tipo_documento AND dn.id_def_nacionalidad = e.id_def_nacionalidad AND activo = 1 and id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC";

$resultado = $db->consulta($query);

$excel = new Spreadsheet();
$hojaActiva = $excel->getActiveSheet();
$hojaActiva->setTitle("Alumnos");

$hojaActiva->setCellValue('A1', 'TIPO DE DOCUMENTO DE IDENTIFICACIÓN');
$hojaActiva->setCellValue('B1', 'NÚMERO DEL DOCUMENTO DE IDENTIFICACIÓN');
$hojaActiva->setCellValue('C1', 'NACIONALIDAD');
$hojaActiva->setCellValue('D1', 'APELLIDOS');
$hojaActiva->setCellValue('E1', 'NOMBRES');
$hojaActiva->setCellValue('F1', 'SEXO');
$hojaActiva->setCellValue('G1', 'CORREO ELECTRÓNICO');
$hojaActiva->setCellValue('H1', 'PARROQUIA DEL ESTUDIANTE');
$hojaActiva->setCellValue('I1', 'DIRECCION DEL ESTUDIANTE');
$hojaActiva->setCellValue('J1', 'TELÉFONO CELULAR');
$hojaActiva->setCellValue('K1', 'FECHA DE NACIMIENTO');

$fila = 2;

while ($rows = $resultado->fetch_assoc()) {
    $hojaActiva->setCellValue('A' . $fila, $rows['td_nombre']);
    $hojaActiva->setCellValue('B' . $fila, $rows['es_cedula']);
    $hojaActiva->setCellValue('C' . $fila, $rows['dn_nombre']);
    $hojaActiva->setCellValue('D' . $fila, $rows['es_apellidos']);
    $hojaActiva->setCellValue('E' . $fila, $rows['es_nombres']);
    $hojaActiva->setCellValue('F' . $fila, $rows['dg_nombre']);
    $hojaActiva->setCellValue('G' . $fila, $rows['es_email']);
    $hojaActiva->setCellValue('H' . $fila, $rows['es_sector']);
    $hojaActiva->setCellValue('I' . $fila, $rows['es_direccion']);
    $hojaActiva->setCellValue('J' . $fila, $rows['es_telefono']);
    $hojaActiva->setCellValue('K' . $fila, $rows['es_fec_nacim']);

    $fila++;
}

$columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

for ($i = 0; $i < count($columnas); $i++) {
    $excel->getActiveSheet()
        ->getColumnDimension($columnas[$i])
        ->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="alumnos.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($excel, 'Xlsx');
$writer->save('php://output');
exit;
