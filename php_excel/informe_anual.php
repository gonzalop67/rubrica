<?php
require_once "../vendor/autoload.php";

//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;

/* Error reporting */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

/* PHPExcel_IOFactory */

require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.usuarios.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_usuario = $_SESSION["id_usuario"];

$usuario = new usuarios();
$nombreUsuario = $usuario->obtenerNombreUsuario($id_usuario);

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->getNombreParalelo($id_paralelo);

$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);
$nombreArea = $asignatura->obtenerNombreArea($id_asignatura);

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "INFORME ANUAL DE APRENDIZAJES";
$objPHPExcel = $objReader->load("../templates/" . $baseFilename . ".xls");

$objPHPExcel->getActiveSheet()->setCellValue('B6', $nombrePeriodoLectivo)
	->setCellValue('C8', $nombreArea)
	->setCellValue('C9', $nombreUsuario)
	->setCellValue('F9', $nombreParalelo)
	->setCellValue('C10', $nombreAsignatura)
	->setCellValue('C59', $nombreUsuario);

// Aqui va el codigo para calcular el promedio del periodo de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT e.id_estudiante, 
									 es_apellidos, 
									 es_nombres 
								FROM sw_estudiante e,
									 sw_estudiante_periodo_lectivo ep 
							   WHERE e.id_estudiante = ep.id_estudiante
								 AND es_retirado = 'N' 
								 AND activo = 1 
								 AND id_paralelo = $id_paralelo
							   ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = 25;
	$filaBase = $row; // fila base
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$nombreEstudiante = $estudiante["es_apellidos"] . " " . $estudiante["es_nombres"];
		$query = $db->consulta("SELECT calcular_promedio_periodo_lectivo($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
		$calificacion = $db->fetch_assoc($query);
		$calificacion_anual = $paralelo->truncar($calificacion["calificacion"], 2);

		// Desplegar los estudiantes con promedio menor que siete
		if ($calificacion_anual < 7) {
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $nombreEstudiante);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $calificacion_anual);
			$row++;
		}
	}

	// Elimino las filas excedentes
	if ($num_total_estudiantes < 50)
		$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);
}

//
$filename = $baseFilename . " " . $nombreParalelo . " " . $nombreAsignatura . " " . $nombrePeriodoLectivo . ".xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
