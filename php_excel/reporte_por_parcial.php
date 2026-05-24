<?php
require_once "../vendor/autoload.php";

//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
$jornada = $institucion->obtenerJornada($id_paralelo);

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
$ultimaColumna = $numAsignaturas + 1;

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T');
$contNotasValidasArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

// Variable de instancia de la clase MySQL
$db = new MySQL();

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO PARCIALES.xls";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);

$sheet = $objPHPExcel->getActiveSheet();

$logoPath = __DIR__ . '/logo-presidencia-noboa.jpg'; // Ruta a tu logo

// Logo del Gobierno
$drawing = new Drawing();
$drawing->setName('Logo del Gobierno');
$drawing->setDescription('Logo Gobierno');
$drawing->setPath($logoPath);
$drawing->setHeight(50); // Altura en píxeles
$drawing->setCoordinates('A1'); // Celda donde se coloca
$drawing->setWorksheet($sheet);

$queryString = "SELECT in_logo FROM sw_institucion WHERE id_institucion = 1";
$consulta = $db->consulta($queryString);
$registro = $db->fetch_object($consulta);
$in_logo = $registro->in_logo;

$logoInstitucionPath = dirname(__DIR__) . '/public/uploads/' . $in_logo;

// Logo de la Institución
$drawing2 = new Drawing();
$drawing2->setName('Logo de la Institucion');
$drawing2->setDescription('Logo Institucion');
$drawing2->setPath($logoInstitucionPath);
$drawing2->setHeight(80); // Altura en píxeles
$drawing2->setCoordinates($colAsignaturas[$numAsignaturas - 1] . '1'); // Celda donde se coloca
$drawing2->setWorksheet($sheet);

// Ciclo Lectivo
$meses_abrev = array(0, "ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
$queryString = "SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$consulta = $db->consulta($queryString);
$periodo_lectivo = $db->fetch_assoc($consulta);
$fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
$fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
$ciclo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A2', 'REPORTE CONSOLIDADO DEL ' . $nombreAporteEvaluacion)
	->setCellValue('A3', 'CURSO: ' . $nombreParalelo . " (" . $nombrePeriodoLectivo . ")")
	->setCellValue('C5', 'JORNADA ' . $jornada)
	->setCellValue('G5', 'CICLO: ' . $ciclo)
	->setCellValue('B59', $nombreRector)
	->setCellValue('G59', $nombreSecretario);

// Columna para escribir el promedio de las asignaturas
switch ($numAsignaturas) {
	case 6:
		$colPromedio = 'I';
		$colObservacion = 'J';
		break;
	case 7:
		$colPromedio = 'J';
		$colObservacion = 'K';
		break;
	case 8:
		$colPromedio = 'K';
		$colObservacion = 'L';
		break;
	case 9:
		$colPromedio = 'L';
		$colObservacion = 'M';
		break;
	case 10:
		$colPromedio = 'M';
		$colObservacion = 'N';
		break;
	case 11:
		$colPromedio = 'N';
		$colObservacion = 'O';
		break;
	case 12:
		$colPromedio = 'O';
		$colObservacion = 'P';
		break;
	case 13:
		$colPromedio = 'P';
		$colObservacion = 'Q';
		break;
	case 14:
		$colPromedio = 'Q';
		$colObservacion = 'R';
		break;
	case 15:
		$colPromedio = 'R';
		$colObservacion = 'S';
		break;
	case 16:
		$colPromedio = 'S';
		$colObservacion = 'T';
		break;
	case 17:
		$colPromedio = 'T';
		$colObservacion = 'U';
		break;
}

$sheet->mergeCells('A1:' . $colObservacion . '1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:' . $colObservacion . '2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A3:' . $colObservacion . '3');
$sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$filaBase = 7; // fila base en la plantilla en Excel

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante

// Imprimir los nombres de las asignaturas
$asignaturas = $db->consulta("SELECT as_nombre 
								FROM sw_asignatura_curso ac, 
									 sw_paralelo p, 
									 sw_asignatura a 
							   WHERE ac.id_curso = p.id_curso 
								 AND ac.id_asignatura = a.id_asignatura 
								 AND id_paralelo = $id_paralelo 
							ORDER BY ac_orden");

$rowAsignatura = 6;
$contAsignaturas = 0;

while ($asignatura = $db->fetch_assoc($asignaturas)) {
	$asignatura = $asignatura["as_nombre"];
	$sheet->getStyle($colAsignaturas[$contAsignaturas] . $rowAsignatura)->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THIN,
				'color' => ['argb' => '000000'],
			],
		],
	]);
	$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignaturas] . $rowAsignatura, $asignatura);
	$contAsignaturas++;
}

// Cabecera de Promedio y Observación
$sheet->getStyle($colAsignaturas[$contAsignaturas] . $rowAsignatura)->applyFromArray([
	'borders' => [
		'allBorders' => [
			'borderStyle' => Border::BORDER_THIN,
			'color' => ['argb' => '000000'],
		],
	],
]);

// Obtener la nota mínima para aprobar el periodo lectivo
$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$nota_aprobacion = $registro->pe_nota_aprobacion;

// Obtener el rango de calificaciones para acceder al examen supletorio según el periodo lectivo
$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$rango_desde = $registro->rango_desde;
$rango_hasta = $registro->rango_hasta;

$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignaturas] . $rowAsignatura, 'PROMEDIO');

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = $filaBase; // fila base

	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];

		$numero_problemas = 0;

		$contadorNotasValidas = 0;

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$asignaturas = $db->consulta("SELECT a.id_asignatura, 
											 a.id_tipo_asignatura  
										FROM sw_asignatura_curso ac, 
											 sw_paralelo p, 
											 sw_asignatura a 
									   WHERE ac.id_curso = p.id_curso 
									     AND ac.id_asignatura = a.id_asignatura  
										 AND id_paralelo = $id_paralelo 
									   ORDER BY ac_orden");
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

				$contadorNotasValidas = 0;

				if ($id_tipo_asignatura == 1) // Se trata de una asignatura CUANTITATIVA
				{
					// Aca voy a llamar a una funcion almacenada que calcula el promedio parcial de la asignatura
					$query = $db->consulta("SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
					$calificacion = $db->fetch_assoc($query);
					$promedio_parcial = $calificacion["promedio"];
					$sumaPromedios += $promedio_parcial;

					if ($promedio_parcial == 0) {
						$promedio_parcial = " ";
					} else {
						$promedio_parcial = truncar($promedio_parcial, 2);
						$contNotasValidasArray[$contAsignatura]++;
					}

					// Aplicar borde outline delgado
					$sheet->getStyle($colAsignaturas[$contAsignatura] . $row)->applyFromArray([
						'borders' => [
							'outline' => [
								'borderStyle' => Border::BORDER_THIN,
								'color' => ['argb' => '000000'],
							],
						],
					]);

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $promedio_parcial);
					$cuantitativas++;
				} else {
					// Aca obtengo la calificacion cualitativa de la asignatura
					$qryString = "SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura";
					$query = $db->consulta($qryString);
					$total_registros = $db->num_rows($query);
					if ($total_registros > 0) {
						$registro = $db->fetch_assoc($query);
						$calificacion = $registro["rc_calificacion"];
					} else {
						$calificacion = " ";
					}

					// Aplicar borde outline delgado
					$sheet->getStyle($colAsignaturas[$contAsignatura] . $row)->applyFromArray([
						'borders' => [
							'outline' => [
								'borderStyle' => Border::BORDER_THIN,
								'color' => ['argb' => '000000'],
							],
						],
					]);

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $calificacion);
				}

				$contAsignatura++;
			} // fin while $asignatura

			// Aplicar borde outline delgado
			$sheet->getStyle($colPromedio . $row)->applyFromArray([
				'borders' => [
					'outline' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['argb' => '000000'],
					],
				],
			]);

			// Cálculo e impresión del promedio de asignaturas
			$promedioAsignaturas = $sumaPromedios / $cuantitativas;
			if ($promedioAsignaturas == 0) {
				$objPHPExcel->getActiveSheet()->setCellValue($colPromedio . $row, "");
			} else {
				$objPHPExcel->getActiveSheet()->setCellValue($colPromedio . $row, truncar($promedioAsignaturas, 2));
			}

		} // fin if $total_asignatura

		$row++;
	} // fin while($estudiante = $db->fetch_assoc($estudiantes))

	// Elimino las filas excedentes
	if ($num_total_estudiantes < 50)
		$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);

	// Seteo las fórmulas para calcular los promedios generales de cada asignatura
	for ($col = 0; $col <= $contAsignatura; $col++) {
		if ($contNotasValidasArray[$col] > 0) {
			// Aplicar borde outline delgado
			$sheet->getStyle($colAsignaturas[$col] . $row)->applyFromArray([
				'borders' => [
					'outline' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['argb' => '000000'],
					],
				],
			]);
			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$col] . $row, "=SUM(" . $colAsignaturas[$col] . $filaBase . ":" . $colAsignaturas[$col] . ($row - 1) . ")/" . $contNotasValidasArray[$col]);
		}
	}
} // $num_total_estudiantes > 0

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
		$sheet->mergeCells('B' . $row . ':C' . $row);
		$sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		// Aplicar borde outline delgado
		$sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		]);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $profesor);
		$sheet->mergeCells('D' . $row . ':E' . $row);
		$sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		// Aplicar borde outline delgado
		$sheet->getStyle('D' . $row . ':E' . $row)->applyFromArray([
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
	$sheet->getStyle('B2:E2')->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('B3:C3')->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('D3:E3')->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('B' . $filaBase . ':C' . $row - 1)->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);

	$sheet->getStyle('D' . $filaBase . ':E' . $row - 1)->applyFromArray([
		'borders' => [
			'outline' => [
				'borderStyle' => Border::BORDER_THICK,
				'color' => ['argb' => '000000'],
			],
		],
	]);
}

$objPHPExcel->setActiveSheetIndex(0);

$filename = "CUADRO PARCIALES " . str_replace('"', '', $nombreParalelo) . " " . $nombreAporteEvaluacion . "(" . $nombrePeriodoLectivo . ").xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
