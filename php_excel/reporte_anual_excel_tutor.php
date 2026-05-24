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
// $impresion_para_juntas = $_POST["impresion_para_juntas"];

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

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, dg_abreviatura, es_retirado FROM sw_estudiante e, sw_def_genero dg, sw_estudiante_periodo_lectivo p WHERE dg.id_def_genero = e.id_def_genero AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = 6; // fila base
	$filaBase = $row;
	$contador = 0;

	$total_aprobados = 0;
	$total_supletorios = 0;
	$total_no_aprobados = 0;
	$total_desertores = 0;

	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$retirado = $estudiante["es_retirado"];

		$contador++;
		$suma_promedios = 0;

		if ($contador % 2 !== 0) {
			// Fila impar
			// Definir el estilo para el color de fondo
			$styleArray = [
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => 'D9D9D9', // Blanco, Fondo 1, Oscuro 15%
					],
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
		} else {
			// Fila Par
			// Definir el estilo para el color de fondo
			$styleArray = [
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => 'FFFFFF', // Blanco, Fondo 1
					],
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
		}

		// Aplicar el estilo al rango de celdas
		$sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleArray);

		$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $contador)
			->setCellValue('B' . $row, $apellidos . " " . $nombres);
		//$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFill()->getStartColor()->setRGB('FF0000');

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
					$suma_subperiodos = 0;
					while ($periodo = $db->fetch_assoc($periodo_evaluacion)) {
						// $contador_periodos++;

						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$pe_ponderacion = $periodo["pe_ponderacion"];

						$qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion";
						$query = $db->consulta($qry);

						$record = $db->fetch_object($query);
						$promedio_sub_periodo = $record->calificacion;

						$suma_subperiodos += $promedio_sub_periodo;

					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion

				// Promedio Final del Periodo Lectivo
				$puntaje_final = $suma_subperiodos / $num_total_registros;

				//$nota_final = $puntaje_final == 0 ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

				/*$puntaje_final = truncateFloat($suma_ponderados_subperiodos, 2);

				$suma_promedios += $suma_ponderados_subperiodos;*/

				if ($puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
					$query = $db->consulta("SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS examen_supletorio");
					$registro = $db->fetch_object($query);
					$examen_supletorio = $registro->examen_supletorio;

					if ($examen_supletorio >= 7) {
						$puntaje_final = 7;
					}
				}

				if ($retirado == "S") {
					// $objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, "-");
					$styleArray = [
						'borders' => [
							'outline' => [ // Borde exterior
								'borderStyle' => Border::BORDER_THIN, // Tipo de borde
								'color' => ['argb' => 'FF000000'], // Color del borde (negro)
							],
						],
					];
				} else if ($puntaje_final >= $nota_aprobacion) {
					// APRUEBA
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

					$contador_aprobadas++;
				} else if ($puntaje_final >= $rango_desde) {
					// SUPLETORIO
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

					$contador_supletorios++;
				} else {
					// NO APRUEBA
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

					$contador_no_aprobadas++;
				}

				// Aplicar el estilo al rango de celdas
				$sheet->getStyle($colAsignaturas[$contAsignatura] . $row)->applyFromArray($styleArray);

				$nota_final = $puntaje_final == 0 ? "-" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

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

			if ($retirado == 'S') {
				$observacion = "DESERTOR";

				// Definir el estilo para el color de fondo
				$styleArray = [
					'fill' => [
						'fillType' => Fill::FILL_SOLID,
						'startColor' => [
							'rgb' => 'FFFFFF', // Blanco
						],
					],
					'borders' => [
						'outline' => [ // Borde exterior
							'borderStyle' => Border::BORDER_THIN, // Tipo de borde
							'color' => ['argb' => 'FF000000'], // Color del borde (negro)
						],
					],
				];

				$total_desertores++;
			} else if ($contador_no_aprobadas > 0) {
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
			}

			// Aplicar el estilo al rango de celdas
			$sheet->getStyle($colAsignaturas[$contAsignatura] . $row)->applyFromArray($styleArray);

			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $observacion);

			// Autoajustar el ancho de las columnas
			$sheet->getColumnDimension($colAsignaturas[$contAsignatura])->setAutoSize(true);
		} // fin if $total_asignatura

		$row++;
	}

	// Totales
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $row + 1, 'TOTAL APROBADOS');
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $row + 2, 'TOTAL SUPLETORIOS');
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $row + 3, 'TOTAL NO APROBADOS');
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $row + 4, 'TOTAL DESERTORES');

	$objPHPExcel->getActiveSheet()->setCellValue('C' . $row + 1, $total_aprobados);
	$objPHPExcel->getActiveSheet()->setCellValue('C' . $row + 2, $total_supletorios);
	$objPHPExcel->getActiveSheet()->setCellValue('C' . $row + 3, $total_no_aprobados);
	$objPHPExcel->getActiveSheet()->setCellValue('C' . $row + 4, $total_desertores);

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
			'inside' => [
				'borderStyle' => Border::BORDER_THIN,
				'color' => ['argb' => 'FF000000'], // Color del borde (negro)
			],
		],
	];

	// Aplicar el estilo al rango de celdas
	$sheet->getStyle('B' . $row + 1 . ':C' . $row + 1)->applyFromArray($styleArray);

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
			'inside' => [
				'borderStyle' => Border::BORDER_THIN,
				'color' => ['argb' => 'FF000000'], // Color del borde (negro)
			],
		],
	];

	// Aplicar el estilo al rango de celdas
	$sheet->getStyle('B' . $row + 2 . ':C' . $row + 2)->applyFromArray($styleArray);

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
			'inside' => [
				'borderStyle' => Border::BORDER_THIN,
				'color' => ['argb' => 'FF000000'], // Color del borde (negro)
			],
		],
	];

	// Aplicar el estilo al rango de celdas
	$sheet->getStyle('B' . $row + 3 . ':C' . $row + 3)->applyFromArray($styleArray);

	// Definir el estilo para el color de fondo
	$styleArray = [
		'fill' => [
			'fillType' => Fill::FILL_SOLID,
			'startColor' => [
				'rgb' => 'FFFFFF', // Blanco
			],
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

	// Aplicar el estilo al rango de celdas
	$sheet->getStyle('B' . $row + 4 . ':C' . $row + 4)->applyFromArray($styleArray);

	//$objPHPExcel->getActiveSheet()->setCellValue('B' . $row + 6, date('Y-m-d H:i:s'));
	$fecha_actual = date('j') . " de " . $meses[(int)date('m')] . " de " . date('Y') . ", " . date('H:i');
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $row + 6, $fecha_actual);
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
