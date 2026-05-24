<?php
require_once "../vendor/autoload.php";

//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
require_once '../scripts/clases/class.cursos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];

$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

if (isset($_POST["impresion_para_juntas"])) {
	$impresion_para_juntas = $_POST["impresion_para_juntas"];
} else {
	$impresion_para_juntas = 0;
}

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Instancia de la clase MySQL
$db = new MySQL();

// Obtener nombre del tutor
$queryString = "SELECT u.* FROM sw_usuario u, sw_paralelo_tutor pt WHERE u.id_usuario = pt.id_usuario AND pt.id_periodo_lectivo = $id_periodo_lectivo AND id_paralelo = $id_paralelo";
$consulta = $db->consulta($queryString);
$registro = $db->fetch_object($consulta);
$nombreTutor = $registro->us_titulo . " " . $registro->us_apellidos . " " . $registro->us_nombres;
$generoTutor = $registro->us_genero;
$terminacion = $generoTutor == 'M' ? '' : 'A';
$generoTutor = "TUTOR" . $terminacion;

// Instancia de la clase periodos_lectivos
$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

// Instancia de la clase institucion
$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

// Instancia de la clase paralelos
$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
$nombreParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
$nombreCurso = $paralelo->obtenerNombreParalelo($id_paralelo);
$jornada = $institucion->obtenerJornada($id_paralelo);

$cursos = new cursos();

$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo, $id_curso, 2);

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO SUBPERIODO TUTOR.xls";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A3', 'CUADRO DE CALIFICACIONES - ' . $nombrePeriodoEvaluacion)
	->setCellValue('B62', $nombreTutor)
	->setCellValue('B63', $generoTutor);

// Renombrar la hoja de calculo
$objPHPExcel->getActiveSheet()->setTitle($nombrePeriodoEvaluacion);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U');
$contNotasValidasArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

// Columna para escribir el promedio de las asignaturas

$objPHPExcel->getActiveSheet()->setCellValue('A4', $nombreCurso);
$objPHPExcel->getActiveSheet()->setCellValue('A5', 'AÑO LECTIVO: ' . $nombrePeriodoLectivo);
$objPHPExcel->getActiveSheet()->setCellValue('T7', 'JORNADA ' . $jornada);

// Ciclo Lectivo
$meses_abrev = array(0, "ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
$queryString = "SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$consulta = $db->consulta($queryString);
$periodo_lectivo = $db->fetch_assoc($consulta);
$fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
$fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
$ciclo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];

$objPHPExcel->getActiveSheet()->setCellValue('B7', 'CICLO: ' . $ciclo);

// Código para poner el logo de la Institución Educativa

// Imprimir los nombres de las asignaturas
$asignaturas = $db->consulta("SELECT as_nombre 
								FROM sw_asignatura_curso ac, 
									 sw_paralelo p, 
									 sw_asignatura a 
							   WHERE ac.id_curso = p.id_curso 
								 AND ac.id_asignatura = a.id_asignatura 
								 AND id_paralelo = $id_paralelo 
							ORDER BY ac_orden");

$rowAsignatura = 8;
$contAsignaturas = 0;

while ($asignatura = $db->fetch_assoc($asignaturas)) {
	$asignatura = $asignatura["as_nombre"];
	$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignaturas] . $rowAsignatura, $asignatura);
	$contAsignaturas++;
}

$sheet = $objPHPExcel->getActiveSheet();

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

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante

//Obtengo quien inserta el comportamiento
$sql = "SELECT qc.nombre FROM sw_periodo_lectivo pl, sw_quien_inserta_comp qc WHERE qc.id = pl.quien_inserta_comp_id AND pl.id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($sql);

$quien_inserta_comp = strtolower($resultado->fetch_object()->nombre);

$estudiantes = $db->consulta("SELECT e.id_estudiante, 
									 es_apellidos, 
									 es_nombres,
									 es_retirado, 
									 dg_abreviatura
						        FROM sw_estudiante e, 
									 sw_estudiante_periodo_lectivo p, 
									 sw_def_genero dg
						       WHERE e.id_estudiante = p.id_estudiante 
							     AND dg.id_def_genero = e.id_def_genero 
							     AND activo = 1 
							     AND p.id_paralelo = $id_paralelo  
						    ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = 10; // fila base
	$filaBase = $row;
	$contador = 0;

	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$retirado = $estudiante["es_retirado"];
		$genero = $estudiante["dg_abreviatura"];

		$numero_problemas = 0;

		$contador++;

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
			];
		}

		// Aplicar el estilo al rango de celdas
		$sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleArray);

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$colObservaciones = 'Y';

		$terminacion = ($genero == 'M') ? '' : 'A';
		if ($retirado == 'S') $objPHPExcel->getActiveSheet()->setCellValue($colObservaciones . $row, "DESERTOR" . $terminacion);

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
			$rowAsignatura = 8;
			$sumaPromedios = 0;
			$comportamiento = 0;
			$contAsignaturas = 0;
			$sumaComportamiento = 0;
			$promedio_quimestral = 0;
			$cuantitativas = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				// Aqui proceso los promedios de cada asignatura
				$id_tipo_asignatura = $asignatura["id_tipo_asignatura"];
				$id_asignatura = $asignatura["id_asignatura"];

				if ($id_tipo_asignatura == 1) {
					//Asignaturas cuantitativas
					$consulta = $db->consulta("SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_sub_periodo");
					$registro = $db->fetch_object($consulta);
					$promedio_quimestral = $registro->promedio_sub_periodo;

					$sumaPromedios += $promedio_quimestral;

					if ($promedio_quimestral > 0) {
						$contNotasValidasArray[$contAsignaturas]++;
					}

					if ($promedio_quimestral >= $nota_aprobacion) {
						// Color de fondo verde
						$styleArray = [
							'fill' => [
								'fillType' => Fill::FILL_SOLID,
								'startColor' => [
									'rgb' => '92D050', // Verde claro
								],
							],
						];
					} else if ($promedio_quimestral >= $rango_desde) {
						// Color de fondo naranja
						$styleArray = [
							'fill' => [
								'fillType' => Fill::FILL_SOLID,
								'startColor' => [
									'rgb' => 'FFC000', // Naranja
								],
							],
						];
						$numero_problemas++;
					} else {
						// Color de fondo rojo con letra en blanco
						$styleArray = [
							'fill' => [
								'fillType' => Fill::FILL_SOLID,
								'startColor' => [
									'rgb' => 'FF0000', // Rojo
								],
							],
							'font' => [
								'color' => ['rgb' => 'FFFFFF'], // Color blanco en formato RGB
							],
						];
						$numero_problemas++;
					}

					// Aplicar el estilo al rango de celdas
					$sheet->getStyle($colAsignaturas[$contAsignaturas] . $row)->applyFromArray($styleArray);

					$promedio_quimestral = ($promedio_quimestral == "" || $promedio_quimestral == 0) ? "" : substr($promedio_quimestral, 0, strpos($promedio_quimestral, '.') + 3);

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignaturas] . $row, $promedio_quimestral);
					$cuantitativas++;
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

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignaturas] . $row, $ref_cualitativa);
				}

				// Si el docente ingresa el comportamiento...

				if ($quien_inserta_comp == 2) {
					$query = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS comportamiento");
					$registro = $db->fetch_assoc($query);
					$comportamiento = $registro["comportamiento"];
					$sumaComportamiento += $comportamiento;
				} else {
					// El comportamiento es ingresado por parte del tutor
				}

				$contAsignaturas++;
			} // fin while $asignatura

			// Cálculo e impresión del promedio de asignaturas
			$promedioAsignaturas = $sumaPromedios / $cuantitativas;

			if (!$impresion_para_juntas && $promedioAsignaturas != 0) {
				$promedioAsignaturas = ($promedioAsignaturas == "") ? "" : substr($promedioAsignaturas, 0, strpos($promedioAsignaturas, '.') + 3);

				$objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $promedioAsignaturas);
			}

			// Impresión del número de problemas
			if ($numero_problemas > 0) {
				$terminacion = "";
				if ($numero_problemas > 1)
					$terminacion = "S";
				$objPHPExcel->getActiveSheet()->setCellValue('Y' . $row, $numero_problemas . " PROBLEMA" . $terminacion);
			}

			// Calculo e impresion del promedio de comportamiento
			if ($quien_inserta_comp == 'docente') {
				// $promedioComportamiento = $sumaComportamiento / $total_asignaturas;
				// $promedio_comportamiento = ceil($promedioComportamiento);
				$query = $db->consulta("SELECT calcular_comp_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo) AS comportamiento");
				$calificacion = $db->fetch_assoc($query);
				$promedio_comportamiento = ceil($calificacion["comportamiento"]);
			} else {
				$query = $db->consulta("SELECT calcular_comp_tutor($id_periodo_evaluacion, $id_estudiante, $id_paralelo) AS comportamiento");
				$calificacion = $db->fetch_assoc($query);
				$promedio_comportamiento = ceil($calificacion["comportamiento"]);
			}

			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comportamiento");
			$registro = $db->fetch_assoc($query);
			$equivalencia = $registro["ec_equivalencia"];
			$equivalencia = ($equivalencia == "S/N" ? "" : $equivalencia);
			$objPHPExcel->getActiveSheet()->setCellValue('X' . $row, $equivalencia);
		} // fin if $total_asignatura  
		$row++;
	}
}

// Elimino las filas excedentes
if ($num_total_estudiantes < 50)
	$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);

// Seteo las fórmulas para calcular los promedios generales de cada asignatura
$sumaPromedios = 0;
for ($col = 0; $col <= $contAsignaturas; $col++) {
	if ($contNotasValidasArray[$col] > 0) {
		$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$col] . $row, "=SUM(" . $colAsignaturas[$col] . $filaBase . ":" . $colAsignaturas[$col] . ($row - 1) . ")/" . $contNotasValidasArray[$col]);

		// Get the value from cell $colAsignaturas[$col] . $row
		$cellValue = $objPHPExcel->getActiveSheet()->getCell($colAsignaturas[$col] . $row)->getCalculatedValue();

		$sumaPromedios += $cellValue;
	}
}

$objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $sumaPromedios / $cuantitativas);

// Elimino las columnas excedentes
$sheet->removeColumn($colAsignaturas[$contAsignaturas], 20 - $contAsignaturas);

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

$nombreParalelo = $nombreParalelo . " - " . $jornada;

$filename = "CUADRO $nombrePeriodoEvaluacion $nombreCurso - $nombrePeriodoLectivo.xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
