<?php
require_once "../vendor/autoload.php";

//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

/* Error reporting */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

/* PHPExcel_IOFactory */

require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

function truncateFloat($number, $digitos)
{
	$raiz = 10;
	$multiplicador = pow($raiz, $digitos);
	$resultado = ((int)($number * $multiplicador)) / $multiplicador;
	return $resultado;
}

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$impresion_para_juntas = $_POST["impresion_para_juntas"];

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

// Definir el estilo para el color de fondo
$styleArray = [
	'fill' => [
		'fillType' => Fill::FILL_SOLID,
		'startColor' => [
			'rgb' => '2F75B5', // Azul, Énfasis 1, Oscuro 25%
		],
	],
	'font' => [
		'color' => ['rgb' => 'FFFFFF'], // Color blanco en formato RGB
		//'bold' => true, // Opcional: texto en negrita
	],
	'borders' => [
		'outline' => [ // Borde exterior
			'borderStyle' => Border::BORDER_THIN, // Tipo de borde
			'color' => ['argb' => 'FF000000'], // Color del borde (negro)
		],
		'inside' => [
			'borderStyle' => Border::BORDER_THIN,
			'color' => ['argb' => 'FF000000'], // Color del borde (negro)
		],
	],
];

$sheet = $objPHPExcel->getActiveSheet();

// Aplicar el estilo al rango de celdas
$sheet->getStyle('A5:B5')->applyFromArray($styleArray);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

// Aqui va el codigo para calcular los promedios de los parciales de cada estudiante
$db = new MySQL();

// Obtener el rango de calificaciones para acceder al examen supletorio según el periodo lectivo
$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$rango_desde = $registro->rango_desde;
$rango_hasta = $registro->rango_hasta;

// Obtener la nota mínima para aprobar el periodo lectivo
$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$nota_aprobacion = $registro->pe_nota_aprobacion;

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, dg_abreviatura, es_retirado FROM sw_estudiante e, sw_def_genero dg, sw_estudiante_periodo_lectivo p WHERE dg.id_def_genero = e.id_def_genero AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = 6; // fila base
	$filaBase = $row;
	$contador = 0;

	$total_aprobados = 0;
	$total_supletorios = 0;
	$total_no_aprobados = 0;

	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$retirado = $estudiante["es_retirado"];

		$contador++;
		$suma_promedios = 0;

		$styleArray = [
			'borders' => [
				'outline' => [ // Borde exterior
					'borderStyle' => Border::BORDER_THIN, // Tipo de borde
					'color' => ['argb' => 'FF000000'], // Color del borde (negro)
				],
				'inside' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => 'FF000000'], // Color del borde (negro)
				],
			],
		];

		// Aplicar el estilo al rango de celdas
		$sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleArray);

		$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $contador)
			->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$asignaturas = $db->consulta("SELECT a.id_asignatura, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if ($total_asignaturas > 0) {
			$rowAsignatura = 5;
			$contAsignatura = 0;

			// Contadores
			$contador_aprobadas = 0;
			$contador_supletorios = 0;
			$contador_no_aprobadas = 0;

			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$asignatura = $asignatura["as_abreviatura"];

				$contador_sin_examen = 0;

				// Definir el estilo para el color de fondo
				$styleArray = [
					'fill' => [
						'fillType' => Fill::FILL_SOLID,
						'startColor' => [
							'rgb' => '2F75B5', // Azul, Énfasis 1, Oscuro 25%
						],
					],
					'font' => [
						'color' => ['rgb' => 'FFFFFF'], // Color blanco en formato RGB
						//'bold' => true, // Opcional: texto en negrita
					],
					'borders' => [
						'outline' => [ // Borde exterior
							'borderStyle' => Border::BORDER_THIN, // Tipo de borde
							'color' => ['argb' => 'FF000000'], // Color del borde (negro)
						],
					],
				];

				// Aplicar el estilo al rango de celdas
				$sheet->getStyle($colAsignaturas[$contAsignatura] . $rowAsignatura)->applyFromArray($styleArray);

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, $asignatura);

				$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion, pe_ponderacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo IN (1, 7)");
				$num_total_registros = $db->num_rows($periodo_evaluacion);
				if ($num_total_registros > 0) {
					$suma_ponderados_subperiodos = 0;
					while ($periodo = $db->fetch_assoc($periodo_evaluacion)) {
						// $contador_periodos++;

						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$pe_ponderacion = $periodo["pe_ponderacion"];

						/*-------------------------------------*/
						$qry = "SELECT id_aporte_evaluacion, ap_ponderacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
						$aporte_evaluacion = $db->consulta($qry);
						$num_total_registros = $db->num_rows($aporte_evaluacion);
						if ($num_total_registros > 0) {
							// Aqui calculo los promedios y desplegar en la tabla
							// $suma_promedios = 0;
							$contador_aportes = 0;
							$suma_ponderados = 0;
							while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
								$contador_aportes++;
								$ponderacion_aporte = $aporte["ap_ponderacion"];

								$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_tipo_asignatura = 1 AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
								$total_rubricas = $db->num_rows($rubrica_evaluacion);
								if ($total_rubricas > 0) {
									$suma_rubricas = 0;
									$contador_rubricas = 0;
									while ($rubricas = $db->fetch_assoc($rubrica_evaluacion)) {
										$contador_rubricas++;
										$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
										$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
										$total_registros = $db->num_rows($qry);
										if ($total_registros > 0) {
											$rubrica_estudiante = $db->fetch_assoc($qry);
											$calificacion = $rubrica_estudiante["re_calificacion"];
										} else {
											$calificacion = 0;
										}
										$suma_rubricas += $calificacion;
									}
								}
								// Aqui calculo el promedio del aporte de evaluacion
								$promedio = truncateFloat($suma_rubricas / $contador_rubricas, 2);
								$ponderado = truncateFloat($promedio * $ponderacion_aporte, 3);

								// $suma_promedios += $promedio;
								$suma_ponderados += $ponderado;
							}
						}
						// Aqui se calculan las calificaciones del periodo de evaluacion
						$calificacion_subperiodo = truncateFloat($suma_ponderados, 2);
						$calificacion_ponderada = $calificacion_subperiodo * $pe_ponderacion;

						$suma_ponderados_subperiodos += $calificacion_ponderada;
						// $suma_periodos += $calificacion_quimestral;
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion

				$puntaje_final = truncateFloat($suma_ponderados_subperiodos, 2);

				$suma_promedios += $suma_ponderados_subperiodos;

				if ($suma_promedios >= $rango_desde && $suma_promedios <= $rango_hasta) {
					$query = $db->consulta("SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS examen_supletorio");
					$registro = $db->fetch_object($query);
					$examen_supletorio = $registro->examen_supletorio;

					if ($examen_supletorio >= 7) {
						$puntaje_final = 7;
					}
				}

				$styleArray = [
					'borders' => [
						'outline' => [ // Borde exterior
							'borderStyle' => Border::BORDER_THIN, // Tipo de borde
							'color' => ['argb' => 'FF000000'], // Color del borde (negro)
						],
					],
				];

				// Aplicar el estilo al rango de celdas
				$sheet->getStyle($colAsignaturas[$contAsignatura] . $row)->applyFromArray($styleArray);

				$nota_final = ($puntaje_final == 0) ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $nota_final);

				$contAsignatura++;
			} // fin while $asignatura

			// Definir el estilo para el color de fondo
			$styleArray = [
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => '2F75B5', // Azul, Énfasis 1, Oscuro 25%
					],
				],
				'font' => [
					'color' => ['rgb' => 'FFFFFF'], // Color blanco en formato RGB
					//'bold' => true, // Opcional: texto en negrita
				],
				'borders' => [
					'outline' => [ // Borde exterior
						'borderStyle' => Border::BORDER_THIN, // Tipo de borde
						'color' => ['argb' => 'FF000000'], // Color del borde (negro)
					],
				],
			];

			// Aplicar el estilo al rango de celdas
			$sheet->getStyle($colAsignaturas[$contAsignatura] . $rowAsignatura)->applyFromArray($styleArray);

			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, "OBSERVACION");

			$observacion = "";

			/* if ($contador_no_aprobadas > 0) {
				$observacion = "NO APRUEBA";

				// Definir el estilo para el color de fondo
				$styleArray = [
					'fill' => [
						'fillType' => Fill::FILL_SOLID,
						'startColor' => [
							'rgb' => 'FF0000', // Rojo
						],
					],
					'font' => [
						'color' => ['rgb' => 'FFFFFF'], // Color blanco en formato RGB
						//'bold' => true, // Opcional: texto en negrita
					],
					'borders' => [
						'outline' => [ // Borde exterior
							'borderStyle' => Border::BORDER_THIN, // Tipo de borde
							'color' => ['argb' => 'FF000000'], // Color del borde (negro)
						],
					],
				];

				$total_no_aprobados++;
			} else if ($contador_supletorios > 0) {
				$observacion = "SUPLETORIO";

				// Definir el estilo para el color de fondo
				$styleArray = [
					'fill' => [
						'fillType' => Fill::FILL_SOLID,
						'startColor' => [
							'rgb' => 'FFC000', // Naranja
						],
					],
					'borders' => [
						'outline' => [ // Borde exterior
							'borderStyle' => Border::BORDER_THIN, // Tipo de borde
							'color' => ['argb' => 'FF000000'], // Color del borde (negro)
						],
					],
				];

				$total_supletorios++;
			} else {
				$observacion = "APRUEBA";

				// Definir el estilo para el color de fondo
				$styleArray = [
					'fill' => [
						'fillType' => Fill::FILL_SOLID,
						'startColor' => [
							'rgb' => '92D050', // Verde claro
						],
					],
					'borders' => [
						'outline' => [ // Borde exterior
							'borderStyle' => Border::BORDER_THIN, // Tipo de borde
							'color' => ['argb' => 'FF000000'], // Color del borde (negro)
						],
					],
				];

				$total_aprobados++;
			} */

			$styleArray = [
				'borders' => [
					'outline' => [ // Borde exterior
						'borderStyle' => Border::BORDER_THIN, // Tipo de borde
						'color' => ['argb' => 'FF000000'], // Color del borde (negro)
					],
				],
			];

			// Aplicar el estilo al rango de celdas
			$sheet->getStyle($colAsignaturas[$contAsignatura] . $row)->applyFromArray($styleArray);

			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $observacion);

			// Autoajustar el ancho de las columnas
			$sheet->getColumnDimension($colAsignaturas[$contAsignatura])->setAutoSize(true);
		} // fin if $total_asignatura

		$row++;
	}
}

$objPHPExcel->setActiveSheetIndex(0);

$filename = "CUADRO ANUAL EXCEL " . str_replace('"', '', $nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
