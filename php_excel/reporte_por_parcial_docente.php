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
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';
require_once '../scripts/clases/class.aportes_evaluacion.php';

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_usuario = $_SESSION["id_usuario"];

$usuario = new usuarios();
$nombreUsuario = $usuario->obtenerNombreUsuario($id_usuario);

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();
$jornada = $institucion->obtenerJornada($id_paralelo);

//Para poner una imagen en PHPExcel
/*$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('test_img');
$objDrawing->setDescription('test_img');
$objDrawing->setPath('../images/logo.png');
$objDrawing->setCoordinates('A1');                      
//setOffsetX works properly
$objDrawing->setOffsetX(5); 
$objDrawing->setOffsetY(5);                
//set width, height
$objDrawing->setWidth(100); 
$objDrawing->setHeight(35); 
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/

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

// Primero busco la plantilla adecuada de acuerdo al numero de insumos del parcial
$numInsumos = $aporte_evaluacion->contarInsumos($id_aporte_evaluacion, $id_asignatura);

// Obtengo el recordset de nombres de insumos
$nombresInsumos = $aporte_evaluacion->obtenerNombresInsumos($id_aporte_evaluacion, $id_asignatura);

// Obtengo del nombre de la asignatura
$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO PARCIALES - ";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename . $numInsumos . " INSUMOS.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A2', 'REPORTE DEL ' . $nombreAporteEvaluacion)
	->setCellValue('A3', 'CURSO ' . $nombreParalelo . " (" . $nombrePeriodoLectivo . ")")
	->setCellValue('A4', 'ASIGNATURA: ' . $nombreAsignatura)
	->setCellValue('B60', $nombreUsuario);

// Vectores de configuracion para las columnas
$colInsumos = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S');

// Columna para escribir el promedio de las asignaturas
switch ($numInsumos) {
	case 1:
		$colPromedio = 'D';
		$colJornada = 'C';
		break;
	case 2:
		$colPromedio = 'E';
		$colJornada = 'C';
		break;
	case 3:
		$colPromedio = 'F';
		$colJornada = 'C';
		break;
	case 4:
		$colPromedio = 'G';
		$colJornada = 'C';
		break;
	case 5:
		$colPromedio = 'H';
		$colJornada = 'C';
		break;
}

$objPHPExcel->getActiveSheet()->setCellValue($colJornada . '6', 'JORNADA ' . $jornada);

$filaBase = 8; // fila base en la plantilla en Excel

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$db = new MySQL();

// Recupero el id_tipo_aporte para ver si es parcial o examen de sub periodo
// id_tipo_aporte = 1 : PARCIAL
// id_tipo_aporte = 2 : EXAMEN SUB PERIODO

$consulta = $db->consulta("SELECT id_tipo_aporte FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");

$tipo_aporte = $db->fetch_object($consulta);
$id_tipo_aporte = $tipo_aporte->id_tipo_aporte;

// Despliego los nombres de los insumos
$rowInsumo = 7;
$contInsumo = 0;
while ($nombreInsumo = $db->fetch_assoc($nombresInsumos)) {
	$insumo = $nombreInsumo["ru_nombre"];
	$objPHPExcel->getActiveSheet()->setCellValue($colInsumos[$contInsumo] . $rowInsumo, $insumo);
	$contInsumo++;
}

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = $filaBase; // fila base
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$rubricas = $db->consulta("SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion ORDER BY id_rubrica_evaluacion ASC");

		$contInsumo = 0;
		$suma = 0;
		while ($rubrica = $db->fetch_assoc($rubricas)) {
			$id_rubrica_evaluacion = $rubrica["id_rubrica_evaluacion"];
			$query = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_rubrica_personalizada = $id_rubrica_evaluacion AND id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
			$total_registros = $db->num_rows($query);
			if ($total_registros > 0) {
				$registro = $db->fetch_assoc($query);
				$calificacion = $registro["re_calificacion"];
				$suma += $calificacion;
			} else {
				$calificacion = " ";
			}
			$objPHPExcel->getActiveSheet()->setCellValue($colInsumos[$contInsumo] . $row, $calificacion);
			$contInsumo++;
		}

		if ($suma > 0) {
			$promedio = $suma / $contInsumo;
			$objPHPExcel->getActiveSheet()->setCellValue($colPromedio . $row, $promedio);
		}

		$row++;
	}

	// Elimino las filas excedentes
	if ($num_total_estudiantes < 50)
		$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);

	// Seteo las fórmulas para calcular los promedios generales de cada asignatura
	for ($col = 0; $col <= $contInsumo; $col++) {
		$objPHPExcel->getActiveSheet()->setCellValue($colInsumos[$col] . $row, "=SUM(" . $colInsumos[$col] . $filaBase . ":" . $colInsumos[$col] . ($row - 1) . ")/" . $num_total_estudiantes);
	}
} // $num_total_estudiantes > 0

$filename = "CUADRO PARCIALES " . str_replace('"', '', $nombreParalelo) . " " . $nombreAporteEvaluacion . "(" . $nombrePeriodoLectivo . ").xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
