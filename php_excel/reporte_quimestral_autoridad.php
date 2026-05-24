<?php
require_once "../vendor/autoload.php";

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

//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();
$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);
$nombreCiclo = $periodo_lectivo->obtenerCiclo($id_periodo_lectivo);

$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
// $nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);
$db = new MySQL();
$consulta = $db->consulta("SELECT pa_nombre FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$record = $db->fetch_object($consulta);
$nombreParalelo = $record->pa_nombre;

$nomParalelo = $paralelo->getNombreParalelo($id_paralelo);

$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo, $id_curso, 2);
//if($tipoEducacion==0) $numAsignaturas++;

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO SUBPERIODO AUTORIDAD.xls";
$objPHPExcel = $objReader->load("../templates/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A3', 'CUADRO DE CALIFICACIONES - ' . $nombrePeriodoEvaluacion)
	->setCellValue('B62', $nombreRector)
	->setCellValue('F62', $nombreSecretario);

// Renombrar la hoja de calculo
$objPHPExcel->getActiveSheet()->setTitle($nombrePeriodoEvaluacion);

// Nombre del curso
$nombreCurso = $paralelo->getNombreCurso($id_paralelo);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V');
$numero_calificaciones_validas = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

// Columna para escribir el promedio de las asignaturas
switch ($numAsignaturas) {
	case 6:
		$colNomParalelo = 'I';
		break;
	case 7:
		$colNomParalelo = 'J';
		break;
	case 8:
		$colNomParalelo = 'K';
		break;
	case 9:
		$colNomParalelo = 'L';
		break;
	case 10:
		$colNomParalelo = 'M';
		break;
	case 11:
		$colNomParalelo = 'N';
		break;
	case 12:
		$colNomParalelo = 'O';
		break;
	case 13:
		$colNomParalelo = 'P';
		break;
	case 14:
		$colNomParalelo = 'Q';
		break;
	case 15:
		$colNomParalelo = 'R';
		break;
	case 16:
		$colNomParalelo = 'S';
		break;
	case 17:
		$colNomParalelo = 'T';
		break;
	case 18:
		$colNomParalelo = 'U';
		break;
}

$objPHPExcel->getActiveSheet()->setCellValue('A5', $nombreCurso);
$objPHPExcel->getActiveSheet()->setCellValue('A4', $nombrePeriodoLectivo . " " . $nombreCiclo);
$objPHPExcel->getActiveSheet()->setCellValue($colNomParalelo . '7', 'PARALELO "' . $nombreParalelo . '"');

$filaBase = 10; // fila base en la plantilla en Excel

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
// Se utilizara el store procedure sp_calcular_prom_quimestre que tiene los siguientes parametros:
//    IdPeriodoEvaluacion : $id_periodo_evaluacion (parametro POST)
//    IdParalelo : $id_paralelo (parametro POST)

// Primero se vaciará la tabla utilizada para almacenar los promedios del quimestre
$qry = "DELETE FROM sw_estudiante_prom_quimestral";
$res = $db->consulta($qry);
$qry = "CALL sp_calcular_prom_quimestre($id_periodo_evaluacion, $id_paralelo)";
$res = $db->consulta($qry);

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, eq_promedio FROM sw_estudiante e, sw_estudiante_prom_quimestral eq WHERE e.id_estudiante = eq.id_estudiante AND id_paralelo = $id_paralelo ORDER BY eq_promedio DESC");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = $filaBase; // fila base
	$num_promedios_validos = 0;
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$promedio_quimestral_total = $estudiante["eq_promedio"];

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$asignaturas = $db->consulta("SELECT a.id_asignatura, a.id_tipo_asignatura, as_nombre FROM sw_asignatura_curso ac, sw_paralelo p, sw_asignatura a WHERE ac.id_curso = p.id_curso AND ac.id_asignatura = a.id_asignatura AND id_paralelo = $id_paralelo ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if ($total_asignaturas > 0) {
			$rowAsignatura = 8;
			$contAsignatura = 0;
			$contCuantitativas = 0;
			$sumaComportamiento = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				$num_calif_validas = 0;
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$id_tipo_asignatura = $asignatura["id_tipo_asignatura"];
				$asignatura = $asignatura["as_nombre"];

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, $asignatura);

				if ($id_tipo_asignatura == 1) // Se trata de una asignatura CUANTITATIVA
				{
					// Aca voy a llamar a una funcion almacenada que calcula el promedio quimestral de la asignatura
					$query = $db->consulta("SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
					$calificacion = $db->fetch_assoc($query);
					$promedio_quimestral = $calificacion["promedio"];

					if ($promedio_quimestral > 0) $numero_calificaciones_validas[$contAsignatura]++;

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, truncar($promedio_quimestral, 2));

					$contCuantitativas++;
				} else {
					//Aqui consulto las asignaturas cualitativas
					$consulta = $db->consulta("SELECT calc_prom_subperiodo_cualitativa($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_sub_periodo");

					$registro = $db->fetch_object($consulta);
					$promedio_sub_periodo = $registro->promedio_sub_periodo;

					$consulta = $db->consulta("SELECT ref_cualitativa FROM sw_escala_referencial WHERE nota_cuantitativa = $promedio_sub_periodo");
					$registro = $db->fetch_object($consulta);
					if (empty($registro)) {
						$ref_cualitativa = "";
					} else {
						$ref_cualitativa = $registro->ref_cualitativa;
					}

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $ref_cualitativa);
				}

				$query = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS comportamiento");
				$calificacion = $db->fetch_assoc($query);
				$comportamiento = $calificacion["comportamiento"];
				$sumaComportamiento += $comportamiento;

				$contAsignatura++;
			} // fin while $asignatura

			// Calculo e impresion del promedio de asignaturas
			$objPHPExcel->getActiveSheet()->setCellValue('W' . $row, truncar($promedio_quimestral_total, 2));

			if ($promedio_quimestral_total > 0) $num_promedios_validos++;

			// Calculo e impresion del promedio de comportamiento
			$promedioComportamiento = $sumaComportamiento / $total_asignaturas;
			$promedio_comportamiento = ceil($promedioComportamiento);

			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comportamiento");
			$equivalencia = $db->fetch_assoc($query);
			$objPHPExcel->getActiveSheet()->setCellValue('X' . $row, $equivalencia['ec_equivalencia']);
		} // fin if $total_asignatura
		$row++;
	}
	// Elimino las filas excedentes
	if ($num_total_estudiantes < 50)
		$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);
	// Seteo las fórmulas para calcular los promedios generales de cada asignatura
	for ($col = 0; $col < $contCuantitativas; $col++) {
		$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$col] . $row, "=SUM(" . $colAsignaturas[$col] . $filaBase . ":" . $colAsignaturas[$col] . ($row - 1) . ")/" . $numero_calificaciones_validas[$col]);
	}
	//Aqui va la formula para el promedio de los promedios
	$objPHPExcel->getActiveSheet()->setCellValue('W' . $row, "=SUM(" . $colAsignaturas[0] . $row . ":" . $colAsignaturas[$col - 1] . $row . ")/" . $contCuantitativas);
}

$sheet = $objPHPExcel->getActiveSheet();

// Elimino las columnas excedentes
$sheet->removeColumn($colAsignaturas[$contAsignatura], 20 - $contAsignatura);

// Aqui va el codigo para desplegar la lista de docentes del paralelo

$objPHPExcel->setActiveSheetIndex(1);

$sheet = $objPHPExcel->getActiveSheet();

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
	$filaBase = 4;
	$row = 4;
	while ($docente = $db->fetch_object($docentes)) {
		$asignatura = $docente->as_nombre;
		$profesor = $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $asignatura);
		$sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		// Aplicar borde outline delgado
		$sheet->getStyle('B' . $row)->applyFromArray([
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		]);
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $profesor);
		// $sheet->mergeCells('D' . $row . ':E' . $row);
		$sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		// Aplicar borde outline delgado
		$sheet->getStyle('C' . $row)->applyFromArray([
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		]);
		$row++;
	}

	// Aplicar borde outline grueso
	$sheet->getStyle('B2:C2')->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('B3')->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('C3')->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('B' . $filaBase . ':B' . $row - 1)->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('C' . $filaBase . ':C' . $row - 1)->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);
}

foreach ($sheet->getColumnIterator() as $column) {
    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
}

$sheet->mergeCells('B2:C2'); // Combinar las celdas B2:C2
$sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$objPHPExcel->setActiveSheetIndex(0);

$filename = "CUADRO SUBPERIODO "  . str_replace('"', '', $nomParalelo) . " " . $nombrePeriodoEvaluacion . "(" . $nombrePeriodoLectivo . ").xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
