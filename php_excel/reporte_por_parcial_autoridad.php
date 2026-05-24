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

/* PHPExcel_IOFactory */

require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';
require_once '../scripts/clases/class.aportes_evaluacion.php';

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();
$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);
$tipoEducacion = $paralelo->obtenerTipoEducacion($id_paralelo); // 0: Educacion Basica Superior  1: Bachillerato

$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

$aporte_evaluacion = new aportes_evaluacion();
$nombreAporteEvaluacion = $aporte_evaluacion->obtenerNombreAporteEvaluacion($id_aporte_evaluacion);

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo, $id_curso, 2);
//if($tipoEducacion==0) $numAsignaturas++;

// $objReader = PHPExcel_IOFactory::createReader('Excel5');

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO PARCIALES AUTORIDAD.xls";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A2', 'REPORTE CONSOLIDADO DEL ' . $nombreAporteEvaluacion)
	->setCellValue('A3', 'CURSO ' . $nombreParalelo . " (" . $nombrePeriodoLectivo . ")")
	->setCellValue('B59', $nombreRector)
	->setCellValue('G59', $nombreSecretario);

// Vectores de configuracion para las columnas
/*$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S');

// Columna para escribir el promedio de las asignaturas
switch ($numAsignaturas) {
	case 6:
		$colPromedio = 'I';
		break;
	case 7:
		$colPromedio = 'J';
		break;
	case 8:
		$colPromedio = 'K';
		break;
	case 9:
		$colPromedio = 'L';
		break;
	case 10:
		$colPromedio = 'M';
		break;
	case 11:
		$colPromedio = 'N';
		break;
	case 12:
		$colPromedio = 'O';
		break;
	case 13:
		$colPromedio = 'P';
		break;
	case 14:
		$colPromedio = 'Q';
		break;
	case 15:
		$colPromedio = 'R';
		break;
	case 16:
		$colPromedio = 'S';
		break;
	case 17:
		$colPromedio = 'T';
		break;
}

$filaBase = 7; // fila base en la plantilla en Excel

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
// Se utilizará el store procedure sp_calcular_prom_aporte que tiene los siguientes parametros:
//    IdAporteEvaluacion : $id_aporte_evaluacion (parametro POST)
//    IdParalelo : $id_paralelo (parametro POST)
$db = new MySQL();
// Primero se vaciará la tabla utilizada para almacenar los promedios del parcial
$qry = "DELETE FROM sw_estudiante_promedio_parcial";
$res = $db->consulta($qry);
$qry = "CALL sp_calcular_prom_aporte($id_aporte_evaluacion,$id_paralelo)";
$res = $db->consulta($qry);

$qry = "SELECT e.id_estudiante, es_apellidos, es_nombres, ep_promedio FROM sw_estudiante e, sw_estudiante_promedio_parcial ep WHERE e.id_estudiante = ep.id_estudiante AND ep.id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo ORDER BY ep_promedio DESC";
$estudiantes = $db->consulta($qry);
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = $filaBase; // fila base
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$asignaturas = $db->consulta("SELECT a.id_asignatura, a.id_tipo_asignatura, as_nombre FROM sw_asignatura_curso ac, sw_paralelo p, sw_asignatura a WHERE ac.id_curso = p.id_curso AND ac.id_asignatura = a.id_asignatura AND id_paralelo = $id_paralelo ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if ($total_asignaturas > 0) {
			$rowAsignatura = 6;
			$contAsignatura = 0;
			$sumaPromedios = 0;
			$cuantitativas = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$id_tipo_asignatura = $asignatura["id_tipo_asignatura"];
				$asignatura = $asignatura["as_nombre"];

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, $asignatura);

				if ($id_tipo_asignatura == 1) // Se trata de una asignatura CUANTITATIVA
				{
					// Aca voy a llamar a una funcion almacenada que calcula el promedio parcial de la asignatura
					$query = $db->consulta("SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
					$calificacion = $db->fetch_assoc($query);
					$promedio_parcial = $calificacion["promedio"];
					$sumaPromedios += $promedio_parcial;

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, truncar($promedio_parcial, 2));
					$cuantitativas++;
				} else {
					// Aca obtengo la calificacion cualitativa de la asignatura
					$query = $db->consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
					$total_registros = $db->num_rows($query);
					if ($total_registros > 0) {
						$registro = $db->fetch_assoc($query);
						$calificacion = $registro["rc_calificacion"];
					} else {
						$calificacion = " ";
					}

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $calificacion);
				}

				$contAsignatura++;
			} // fin while $asignatura

			// Calculo e impresion del promedio de asignaturas
			$promedioAsignaturas = $sumaPromedios / $cuantitativas;
			$objPHPExcel->getActiveSheet()->setCellValue($colPromedio . $row, truncar($promedioAsignaturas, 2));
		} // fin if $total_asignatura
		$row++;
	}
	// Elimino las filas excedentes
	if ($num_total_estudiantes < 50)
		$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);
	// Seteo las fórmulas para calcular los promedios generales de cada asignatura
	for ($col = 0; $col <= $contAsignatura; $col++) {
		$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$col] . $row, "=SUM(" . $colAsignaturas[$col] . $filaBase . ":" . $colAsignaturas[$col] . ($row - 1) . ")/" . $num_total_estudiantes);
	}
} // $num_total_estudiantes > 0

// Aqui va el codigo para desplegar la lista de docentes del paralelo

$objPHPExcel->setActiveSheetIndex(1);
$docentes = $db->consulta("SELECT us_titulo, 
								  us_apellidos, 
								  us_nombres, 
								  as_nombre 
						     FROM sw_distributivo di,
								  sw_asignatura_curso ac, 
								  sw_usuario u, 
								  sw_asignatura a 
					        WHERE u.id_usuario = di.id_usuario 
						      AND a.id_asignatura = di.id_asignatura
						      AND ac.id_asignatura = di.id_asignatura
						      AND ac.id_curso = $id_curso 
						      AND id_paralelo = $id_paralelo
					     ORDER BY ac_orden");
$num_total_docentes = $db->num_rows($docentes);
if ($num_total_docentes > 0) {
	$row = 4;
	while ($docente = $db->fetch_object($docentes)) {
		$asignatura = $docente->as_nombre;
		$profesor = $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $asignatura);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $profesor);
		$row++;
	}
}
*/
$filename = "CUADRO PARCIALES " . str_replace('"', '', $nombreParalelo) . " " . $nombreAporteEvaluacion . "(" . $nombrePeriodoLectivo . ").xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
