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

require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.usuarios.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
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

$periodo = new periodos_evaluacion();
$nombrePeriodo = $periodo->getNombrePeriodoEvaluacion($id_periodo_evaluacion);

$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);
$nombreArea = $asignatura->obtenerNombreArea($id_asignatura);

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();
$direccionInstitucion = $institucion->obtenerDireccionInstitucion();
$telefonoInstitucion = $institucion->obtenerTelefonoInstitucion();
$AMIE = $institucion->obtenerAMIEInstitucion();

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "INFORME QUIMESTRAL DE APRENDIZAJES";
$objPHPExcel = $objReader->load("../templates/" . $baseFilename . ".xls");

$objPHPExcel->getActiveSheet()->setCellValue('B1', $nombreInstitucion)
	->setCellValue('B2', $direccionInstitucion)
	->setCellValue('B3', 'AMIE: ' . $AMIE . ' - Teléfono: ' . $telefonoInstitucion)
	->setCellValue('B6', $nombrePeriodoLectivo)
	->setCellValue('C8', $nombreArea)
	->setCellValue('D8', $nombrePeriodo)
	->setCellValue('C9', $nombreUsuario)
	->setCellValue('F9', $nombreParalelo)
	->setCellValue('C10', $nombreAsignatura)
	->setCellValue('C91', $nombreUsuario);

$db = new MySQL();

// Primero consulto las escalas de calificaciones
$query = "SELECT ec_cuantitativa, ec_cualitativa, ec_nota_minima, ec_nota_maxima FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY ec_orden";
$result = $db->consulta($query);
$escala = array();
while ($dato = $db->fetch_array($result)) {
	$escala[] = array(
		'cualitativa' => $dato['ec_cualitativa'],
		'escala' => $dato['ec_cuantitativa'],
		'minima' => $dato['ec_nota_minima'],
		'maxima' => $dato['ec_nota_maxima'],
		'contador' => 0
	);
}

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$datos = array();
$estudiantes = $db->consulta("SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = $id_paralelo AND id_periodo_lectivo = $id_periodo_lectivo AND es_retirado = 'N' AND activo = 1");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$query = $db->consulta("SELECT calcular_promedio_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo,$id_asignatura) AS calificacion");
		$calificacion = $db->fetch_assoc($query);
		$calificacion_quimestral = $calificacion["calificacion"];

		// Calculo de cantidad de estudiantes de acuerdo a la escala de calificaciones
		for ($i = 0; $i < count($escala); $i++) {
			$nota_minima = $escala[$i]['minima'];
			$nota_maxima = $escala[$i]['maxima'];
			if ($calificacion_quimestral >= $nota_minima && $calificacion_quimestral <= $nota_maxima) {
				$escala[$i]['contador'] = $escala[$i]['contador'] + 1;
			}
		}
	}

	// Calculo de porcentajes de acuerdo a la escala de calificaciones
	$row = 20;
	for ($i = 0; $i < count($escala); $i++) {
		/* if ($escala[$i]['contador'] == 1)
			$terminacion = "";
		else
			$terminacion = "s";
		$datos[] = array(
			'escala' => $escala[$i]['escala'] . " (" . $escala[$i]['contador'] . " estudiante" . $terminacion . ")",
			'porcentaje' => $escala[$i]['contador'] / $num_total_estudiantes * 100
		); */
		$porcentaje = $escala[$i]['contador'] / $num_total_estudiantes * 100;
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $escala[$i]['cualitativa'] . " (" . $escala[$i]['escala'] . ")");
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $porcentaje);
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $escala[$i]['contador']);
		$row++;
	}
}

// Aqui va el codigo para calcular el promedio del periodo de cada estudiante
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
	$row = 32; // fila base
	$fila_base = $row;
	$contador = 0;
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$nombreEstudiante = $estudiante["es_apellidos"] . " " . $estudiante["es_nombres"];
		$query = $db->consulta("SELECT calcular_promedio_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo,$id_asignatura) AS calificacion");
		$calificacion = $db->fetch_assoc($query);
		$calificacion_quimestral = $paralelo->truncar($calificacion["calificacion"], 2);

		// Desplegar los estudiantes con promedio menor que siete
		if ($calificacion_quimestral < 7 && $calificacion_quimestral > 0) {
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $nombreEstudiante);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $calificacion_quimestral);
			$row++;
			$contador++;
		}
	}
	// Elimino las filas excedentes
	if ($contador < 50)
		$objPHPExcel->getActiveSheet()->removeRow($row, $fila_base + 50 - $row);
}

//
$filename = "INFORME DE APRENDIZAJES " . $nombreParalelo . " " . $nombreAsignatura . " " . $nombrePeriodo . " " . $nombrePeriodoLectivo . ".xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
