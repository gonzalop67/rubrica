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

function truncar($numero, $digitos)
{
	$truncar = pow(10, $digitos);
	return intval($numero * $truncar) / $truncar;
}

require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO ANUAL.xls";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A2', 'REPORTE DEL PERIODO LECTIVO ' . $nombrePeriodoLectivo)
	->setCellValue('A3', 'CURSO: ' . $nombreParalelo);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V');

// Aqui va el codigo para calcular los promedios de los parciales de cada estudiante
// Se utilizara el store procedure sp_calcular_prom_anual que tiene los siguientes parametros:
//    IdPeriodoLectivo : $id_periodo_lectivo (parametro SESSION)
//    IdParalelo : $id_paralelo (parametro POST)
$db = new MySQL();
// Primero se vaciará la tabla utilizada para almacenar los promedios anuales
$qry = "DELETE FROM sw_estudiante_prom_anual";
$res = $db->consulta($qry);
$qry = "CALL sp_calcular_prom_anual($id_periodo_lectivo, $id_paralelo)";
$res = $db->consulta($qry);

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, ea_promedio FROM sw_estudiante e, sw_estudiante_prom_anual ea WHERE e.id_estudiante = ea.id_estudiante AND id_paralelo = $id_paralelo ORDER BY ea_promedio DESC");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = 6; // fila base
	$contador = 0;
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$promedio_anual_total = $estudiante["ea_promedio"];

		$contador++;

		$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $contador)
			->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$asignaturas = $db->consulta("SELECT a.id_asignatura, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if ($total_asignaturas > 0) {
			$rowAsignatura = 5;
			$contAsignatura = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$asignatura = $asignatura["as_abreviatura"];

				$contador_sin_examen = 0;

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, $asignatura);

				// Aca voy a llamar a una funcion almacenada que calcula el promedio quimestral de la asignatura
				$query = $db->consulta("SELECT calcular_promedio_final($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
				$calificacion = $db->fetch_assoc($query);
				$promedio_anual = $calificacion["promedio"];

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, truncar($promedio_anual, 2));

				$contAsignatura++;
			} // fin while $asignatura

			// Calculo e impresion del promedio de asignaturas
			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, 'PROM.');
			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, truncar($promedio_anual_total, 2));
		} // fin if $total_asignatura

		$row++;
	}
}

$filename = "CUADRO ANUAL EXCEL " . str_replace('"', '', $nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
