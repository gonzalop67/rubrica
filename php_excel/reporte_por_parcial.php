<?php
require_once "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet; // Cambiado para inicializar hoja limpia y dinámica
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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

$db = new MySQL();

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
$tipoEducacion = $paralelo->obtenerTipoEducacion($id_paralelo);

// Consultar asignaturas dinámicamente para saber cuántas son
$sql = "SELECT as_nombre 
		  FROM sw_asignatura_curso ac, 
			   sw_paralelo p, 
			   sw_asignatura a 
		 WHERE ac.id_curso = p.id_curso 
		   AND ac.id_asignatura = a.id_asignatura 
		   AND id_paralelo = $id_paralelo 
	     ORDER BY ac_orden";

$resAsignaturas = $db->consulta($sql);
$numAsignaturas = $db->num_rows($resAsignaturas);

// --- ELIMINADOS LOS ARRAYS FIJOS ---
// Ahora usamos índices numéricos: Columna A = 1, B = 2, C = 3...
$colInicioAsignaturas = 3; // Columna C

// Inicializar un array dinámico para contadores si necesitas procesar estadísticas por materia posterior
$contNotasValidasArray = array_fill(0, $numAsignaturas, 0);

$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

$aporte_evaluacion = new aportes_evaluacion();
$nombreAporteEvaluacion = $aporte_evaluacion->obtenerNombreAporteEvaluacion($id_aporte_evaluacion);

// Crear hoja desde cero en lugar de depender de un .xls fijo limitado
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Consolidado');

// Estilos base reutilizables
$estiloCabeceraMatriz = [
	'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
	'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
	'font' => ['bold' => true, 'size' => 9]
];

// Colocación del Logo del Gobierno
$logoPath = __DIR__ . '/logo-presidencia-noboa.jpg';
if (file_exists($logoPath)) {
	$drawing = new Drawing();
	$drawing->setName('Logo del Gobierno');
	$drawing->setPath($logoPath);
	$drawing->setHeight(50);
	$drawing->setCoordinates('A1');
	$drawing->setWorksheet($sheet);
}

// Colocación del Logo de la Institución en la última columna de materias
$queryString = "SELECT in_logo FROM sw_institucion WHERE id_institucion = 1";
$consulta = $db->consulta($queryString);
$registro = $db->fetch_object($consulta);
$in_logo = $registro->in_logo;
$logoInstitucionPath = dirname(__DIR__) . '/public/uploads/' . $in_logo;

if (file_exists($logoInstitucionPath)) {
	$drawing2 = new Drawing();
	$drawing2->setName('Logo de la Institucion');
	$drawing2->setPath($logoInstitucionPath);
	$drawing2->setHeight(50);
	// Dinámico: se posiciona en la letra de la última asignatura cargada
	$colUltimaAsignaturaLetra = Coordinate::stringFromColumnIndex($colInicioAsignaturas + $numAsignaturas - 1);
	$drawing2->setCoordinates($colUltimaAsignaturaLetra . '1');
	$drawing2->setWorksheet($sheet);
}

// Ciclo Lectivo
$meses_abrev = array(0, "ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
$queryString = "SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$consulta = $db->consulta($queryString);
$periodo_lectivo = $db->fetch_assoc($consulta);
$fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
$fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
$ciclo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];

// Textos principales
$sheet->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A2', 'REPORTE CONSOLIDADO DEL ' . $nombreAporteEvaluacion)
	->setCellValue('A3', 'CURSO: ' . $nombreParalelo . " (" . $nombrePeriodoLectivo . ")")
	->setCellValue('C5', 'JORNADA ' . $jornada)
	->setCellValue('G5', 'CICLO: ' . $ciclo);

// Cabeceras fijas de control izquierdo
$rowAsignatura = 6;
$sheet->setCellValue('A' . $rowAsignatura, 'Nro.');
$sheet->setCellValue('B' . $rowAsignatura, 'Nómina');

// Aplicar el estilo de bordes y alineación a Nro. y Nómina
$sheet->getStyle('A' . $rowAsignatura)->applyFromArray($estiloCabeceraMatriz);
$sheet->getStyle('B' . $rowAsignatura)->applyFromArray($estiloCabeceraMatriz);

// =========================================================================
// MODIFICACIÓN: IMPRIMIR LAS ASIGNATURAS CON ROTACIÓN DE 90 GRADOS
// =========================================================================
$contAsignaturas = 0;
while ($asignatura = $db->fetch_assoc($resAsignaturas)) {
	$nombre_asig = $asignatura["as_nombre"];
    $colLetraActual = Coordinate::stringFromColumnIndex($colInicioAsignaturas + $contAsignaturas);
    
	$sheet->setCellValue($colLetraActual . $rowAsignatura, $nombre_asig);
	
	// Aplicar estilos base (bordes, alineación)
	$sheet->getStyle($colLetraActual . $rowAsignatura)->applyFromArray($estiloCabeceraMatriz);
	
	// NUEVO: Forzar rotación de texto a 90 grados hacia arriba
	$sheet->getStyle($colLetraActual . $rowAsignatura)
		->getAlignment()
		->setTextRotation(90);

	$contAsignaturas++;
}

// Configurar un alto de fila cómodo en la fila 6 para que los nombres rotados se lean completos
$sheet->getRowDimension($rowAsignatura)->setRowHeight(90);

// Definir columnas posteriores basándonos estrictamente en el conteo total final
$colPromedioNum = $colInicioAsignaturas + $contAsignaturas;
$colObservacionNum = $colPromedioNum + 1;

$colPromedioLetra = Coordinate::stringFromColumnIndex($colPromedioNum);
$colObservacionLetra = Coordinate::stringFromColumnIndex($colObservacionNum);

// Encabezados dinámicos finales
$sheet->getStyle($colPromedioLetra . $rowAsignatura)->applyFromArray($estiloCabeceraMatriz);
$sheet->setCellValue($colPromedioLetra . $rowAsignatura, 'PROMEDIO');

// Combinación dinámica de celdas superiores según la anchura real conseguida
$sheet->mergeCells('A1:' . $colPromedioLetra . '1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:' . $colPromedioLetra . '2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A3:' . $colPromedioLetra . '3');
$sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Parámetros de aprobación
$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$nota_aprobacion = $registro->pe_nota_aprobacion;

$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$rango_desde = $registro->rango_desde;
$rango_hasta = $registro->rango_hasta;

// Ejecución de la nómina
$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);

// --- CORRECCIÓN AQUÍ: Definir la fila base ANTES de validar la existencia de alumnos ---
$filaBase = 7;
if ($num_total_estudiantes > 0) {
	$row = $filaBase; // Fila base (7)

	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];

		$numero_problemas = 0;
		$contadorNotasValidas = 0;

		$sheet->setCellValue('A' . $row, $row - $filaBase + 1);
		$sheet->setCellValue('B' . $row, $apellidos . " " . $nombres);

		// ==========================================
		// NUEVO: APLICAR BORDES A Nro. Y NÓMINA
		// ==========================================
		$estiloBordeEstudiante = [
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];
		$sheet->getStyle('A' . $row)->applyFromArray($estiloBordeEstudiante);
		$sheet->getStyle('B' . $row)->applyFromArray($estiloBordeEstudiante);

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
			$contAsignatura = 0;
			$sumaPromedios = 0;
			$cuantitativas = 0;

			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				$id_asignatura = $asignatura["id_asignatura"];
				$id_tipo_asignatura = $asignatura["id_tipo_asignatura"];

				// Cálculo dinámico de la letra de la columna actual
				$colLetraActual = Coordinate::stringFromColumnIndex($colInicioAsignaturas + $contAsignatura);

				if ($id_tipo_asignatura == 1) // CUANTITATIVA
				{
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

					// Aplicar borde dinámico usando la letra calculada
					$sheet->getStyle($colLetraActual . $row)->applyFromArray([
						'borders' => [
							'outline' => [
								'borderStyle' => Border::BORDER_THIN,
								'color' => ['argb' => '000000'],
							],
						],
					]);

					$sheet->setCellValue($colLetraActual . $row, $promedio_parcial);
					$cuantitativas++;
				} else { // CUALITATIVA
					$qryString = "SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura";
					$query = $db->consulta($qryString);
					$total_registros = $db->num_rows($query);
					if ($total_registros > 0) {
						$registro = $db->fetch_assoc($query);
						$calificacion = $registro["rc_calificacion"];
					} else {
						$calificacion = " ";
					}

					$sheet->getStyle($colLetraActual . $row)->applyFromArray([
						'borders' => [
							'outline' => [
								'borderStyle' => Border::BORDER_THIN,
								'color' => ['argb' => '000000'],
							],
						],
					]);

					$sheet->setCellValue($colLetraActual . $row, $calificacion);
				}

				$contAsignatura++;
			} // fin while $asignatura

			// Aplicar borde al Promedio final de la fila
			$sheet->getStyle($colPromedioLetra . $row)->applyFromArray([
				'borders' => [
					'outline' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['argb' => '000000'],
					],
				],
			]);

			// Cálculo e impresión del promedio del estudiante
			$promedioAsignaturas = ($cuantitativas > 0) ? ($sumaPromedios / $cuantitativas) : 0;
			if ($promedioAsignaturas == 0) {
				$sheet->setCellValue($colPromedioLetra . $row, "");
			} else {
				$sheet->setCellValue($colPromedioLetra . $row, truncar($promedioAsignaturas, 2));
			}
		} // fin if $total_asignaturas

		$row++;
	} // fin while $estudiante

	// Seteo las fórmulas dinámicas para los promedios verticales de cada asignatura al final de la tabla
	for ($col = 0; $col < $contAsignaturas; $col++) {
		$colLetraActual = Coordinate::stringFromColumnIndex($colInicioAsignaturas + $col);

		if ($contNotasValidasArray[$col] > 0) {
			$sheet->getStyle($colLetraActual . $row)->applyFromArray([
				'borders' => [
					'outline' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['argb' => '000000'],
					],
				],
			]);
			// Escribe la fórmula nativa de Excel basada en coordenadas dinámicas reales
			$sheet->setCellValue($colLetraActual . $row, "=SUM(" . $colLetraActual . $filaBase . ":" . $colLetraActual . ($row - 1) . ")/" . $contNotasValidasArray[$col]);
		}
	}
} // $num_total_estudiantes > 0

// =========================================================
// DESPLIEGUE DE LA LISTA DE DOCENTES (HOJA INDEX 1)
// =========================================================
$spreadsheet->createSheet(); // Asegurar la existencia física de la pestaña de docentes de forma dinámica
$spreadsheet->setActiveSheetIndex(1);
$sheetDocentes = $spreadsheet->getActiveSheet();
$sheetDocentes->setTitle('Docentes');

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
	$filaBaseDocentes = 4;
	$rowDoc = 4;
	while ($docente = $db->fetch_object($docentes)) {
		$asignatura = $docente->as_nombre;
		$profesor = $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;

		$sheetDocentes->setCellValue('B' . $rowDoc, $asignatura);
		$sheetDocentes->mergeCells('B' . $rowDoc . ':C' . $rowDoc);
		$sheetDocentes->getStyle('B' . $rowDoc)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheetDocentes->getStyle('B' . $rowDoc . ':C' . $rowDoc)->applyFromArray([
			'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
		]);

		$sheetDocentes->setCellValue('D' . $rowDoc, $profesor);
		$sheetDocentes->mergeCells('D' . $rowDoc . ':E' . $rowDoc);
		$sheetDocentes->getStyle('D' . $rowDoc)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheetDocentes->getStyle('D' . $rowDoc . ':E' . $rowDoc)->applyFromArray([
			'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
		]);
		$rowDoc++;
	}

	// Estilos estructurales de contorno para la tabla de docentes
	$sheetDocentes->getStyle('B2:E2')->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]]]);
	$sheetDocentes->getStyle('B3:C3')->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]]]);
	$sheetDocentes->getStyle('D3:E3')->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]]]);
	$sheetDocentes->getStyle('B' . $filaBaseDocentes . ':C' . ($rowDoc - 1))->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]]]);
	$sheetDocentes->getStyle('D' . $filaBaseDocentes . ':E' . ($rowDoc - 1))->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]]]);
}

$spreadsheet->setActiveSheetIndex(0);

// ==========================================
// NUEVO: AUTOAJUSTAR EL ANCHO DE LA NÓMINA
// ==========================================
$sheet->getColumnDimension('B')->setAutoSize(true);

$filename = "CUADRO PARCIALES " . str_replace('"', '', $nombreParalelo) . " " . $nombreAporteEvaluacion . "(" . $nombrePeriodoLectivo . ").xlsx";

// Preparación de headers HTTP para salida de descarga directa nativa en Excel (.xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
