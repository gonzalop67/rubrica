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
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../funciones/funciones_sitio.php';

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

$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

$db = new MySQL();

// Obtener género de la autoridad
$query = $db->consulta("SELECT in_genero_rector FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_rector = $registro->in_genero_rector;

$terminacion = ($in_genero_rector == 'M') ? '' : 'A';

$genero_rector = 'RECTOR' . $terminacion;

// Obtener género del secretario
$query = $db->consulta("SELECT in_genero_secretario FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_secretario = $registro->in_genero_secretario;

$terminacion = ($in_genero_secretario == 'M') ? 'O' : 'A';

$genero_secretario  = 'SECRETARI' . $terminacion;

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo, $id_curso, 1);

// Determinar si es fin de subnivel
$consulta = $db->consulta("SELECT es_fin_subnivel FROM sw_curso WHERE id_curso = $id_curso");
$registro = $db->fetch_object($consulta);
$es_fin_subnivel = $registro->es_fin_subnivel;

// Determinar si es educación intensiva
$consulta = $db->consulta("SELECT es_intensivo FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
$registro = $db->fetch_object($consulta);
$es_intensivo = $registro->es_intensivo;

// Determinar si es bachillerato técnico
$consulta = $db->consulta("SELECT es_bach_tecnico FROM sw_curso WHERE id_curso = $id_curso");
$registro = $db->fetch_object($consulta);
$es_bach_tecnico = $registro->es_bach_tecnico;

$colNombreSecretario = 'S60';
$colGeneroSecretario = 'S61';

switch ($numAsignaturas) {
	case 6:
		$colComportamiento = 'AC';
		break;
	case 7:
		$colComportamiento = 'Z';
		$colNombreSecretario = 'O60';
		$colGeneroSecretario = 'O61';
		break;
	case 8:
		$colComportamiento = 'AK';
		break;
	case 9:
		if (!$es_fin_subnivel)
			$colComportamiento = 'AO';
		else
			$colComportamiento = 'AX';
		break;
	case 10:
		$colComportamiento = 'AS';
		break;
	case 11:
		$colComportamiento = 'AW';
		break;
	case 12:
		$colComportamiento = 'BA';
		break;
	case 13:
		$colComportamiento = 'AR';
		break;
	case 14:
		$colComportamiento = 'AU';
		break;
	case 15:
		if (!$es_bach_tecnico && !$es_fin_subnivel) {
			$colComportamiento = 'BM';
			$colNombreSecretario = 'AK60';
			$colGeneroSecretario = 'AK61';
		} elseif (!$es_fin_subnivel) {
			$colComportamiento = 'CC';
			$colNombreSecretario = 'AK60';
			$colGeneroSecretario = 'AK61';
		} else {
			$colComportamiento = 'CR';
			$colNombreSecretario = 'AK60';
			$colGeneroSecretario = 'AK61';
		}
		break;

	case 16:
		if (!$es_bach_tecnico && !$es_fin_subnivel)
			$colComportamiento = 'BM';
		elseif (!$es_fin_subnivel)
			$colComportamiento = 'CH';
		break;
}

$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

$query = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$registro = $db->fetch_assoc($query);

$fecha_inicial = explode("-", $registro["pe_fecha_inicio"]);
$fecha_final = explode("-", $registro["pe_fecha_fin"]);

$nombreLargoPeriodoLectivo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO ANUAL - ";

$template_filename = "";

if (!$es_fin_subnivel)
	$template_filename = "../plantillas/" . $baseFilename . $numAsignaturas . " ASIGNATURAS.xls";
else
	$template_filename = "../plantillas/" . $baseFilename . $numAsignaturas . " ASIGNATURAS - EFN.xls";

//print_r($template_filename); die();

$objPHPExcel = $objReader->load($template_filename);

$query = "SELECT COUNT(*) AS numPeriodos FROM `sw_periodo_evaluacion`
WHERE id_periodo_lectivo = $id_periodo_lectivo
AND id_tipo_periodo IN (1, 7, 8)";

$consulta = $db->consulta($query);
$registro = $db->fetch_object($consulta);
$numPeriodos = $registro->numPeriodos;

if ($numPeriodos > 2) {
	$titulo = "CUADRO ANUAL DE CALIFICACIONES";
} else {
	$titulo = "CUADRO QUIMESTRAL DE CALIFICACIONES";
}

// Vectores de configuracion para las columnas

if (!$es_bach_tecnico && !$es_fin_subnivel)
	if ($numPeriodos == 2) {
		$colAsignaturas = array('C', 'F', 'I', 'L', 'O', 'R', 'U', 'AE', 'AI', 'AM', 'AQ', 'AU', 'AY', 'BC', 'BG');
		$colPrimerPeriodo = array('C', 'F', 'I', 'L', 'O', 'R', 'U', 'AE', 'AI', 'AM', 'AQ', 'AU', 'AY', 'BC', 'BG');
		$colSegundoPeriodo = array('D', 'G', 'J', 'M', 'P', 'S', 'V', 'AF', 'AJ', 'AN', 'AR', 'AV', 'AZ', 'BD', 'BH');
		$colPuntajeFinal = array('E', 'H', 'K', 'N', 'Q', 'T', 'W', 'AH', 'AL', 'AP', 'AT', 'AX');
		$colComportamiento = 'Z';
	}

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A2', $titulo)
	->setCellValue('A3', 'PERIODO LECTIVO: ' . $nombreLargoPeriodoLectivo)
	->setCellValue('B60', $nombreRector)
	->setCellValue('B61', $genero_rector)
	->setCellValue($colNombreSecretario, $nombreSecretario)
	->setCellValue($colGeneroSecretario, $genero_secretario)
	->setCellValue('A5', $nombreParalelo);

$colAsignaturas2 = array('C', 'H', 'M', 'R', 'W', 'AB', 'AG', 'AL', 'AQ', 'AV', 'BA', 'BF', 'BK', 'BP', 'BU', 'BZ');
$colAsignaturas3 = array('C', 'I', 'O', 'U', 'AA', 'AG', 'AM', 'AS', 'AY', 'BE', 'BK', 'BQ', 'BW', 'CC', 'CI', 'BZ');


$colPrimerPeriodo2 = array('C', 'H', 'M', 'R', 'W', 'AB', 'AG', 'AL', 'AQ', 'AV', 'BA', 'BF', 'BK', 'BP', 'BU', 'BZ');
$colPrimerPeriodo3 = array('C', 'I', 'O', 'U', 'AA', 'AG', 'AM', 'AS', 'AY', 'BE', 'BK', 'BQ', 'BW', 'CC', 'CI', 'BZ');


$colSegundoPeriodo2 = array('D', 'I', 'N', 'S', 'X', 'AC', 'AH', 'AM', 'AR', 'AW', 'BB', 'BG', 'BL', 'BQ', 'BV', 'CA');
$colSegundoPeriodo3 = array('D', 'J', 'P', 'V', 'AB', 'AH', 'AN', 'AT', 'AZ', 'BF', 'BL', 'BR', 'BX', 'CD', 'CJ', 'CA');

$colTercerPeriodo = array('E', 'I', 'M', 'Q', 'U', 'Y', 'AC', 'AG', 'AK', 'AO', 'AS', 'AW');
$colTercerPeriodo2 = array('E', 'J', 'O', 'T', 'Y', 'AD', 'AI', 'AN', 'AS', 'AX', 'BC', 'BH', 'BM', 'BR', 'BW', 'CB');
$colTercerPeriodo3 = array('E', 'K', 'Q', 'W', 'AC', 'AI', 'AO', 'AU', 'BA', 'BG', 'BM', 'BS', 'BY', 'CE', 'CK', 'CB');

$colCuartoPeriodo = array('F', 'K', 'P', 'U', 'Z', 'AE', 'AJ', 'AO', 'AT', 'AY', 'BD', 'BI', 'BN', 'BS', 'BX', 'CC');
$colCuartoPeriodo2 = array('F', 'L', 'R', 'X', 'AD', 'AJ', 'AP', 'AV', 'BB', 'BH', 'BN', 'BT', 'BZ', 'CF', 'CL', '');

$colQuintoPeriodo = array('G', 'M', 'S', 'Y', 'AE', 'AK', 'AQ', 'AW', 'BC', 'BI', 'BO', 'BU', 'CA', 'CG', 'CM', '');


$colPuntajeFinal2 = array('G', 'L', 'Q', 'V', 'AA', 'AF', 'AK', 'AP', 'AU', 'AZ', 'BE', 'BJ', 'BO', 'BT', 'BY', 'CD');
$colPuntajeFinal3 = array('H', 'N', 'T', 'Z', 'AF', 'AL', 'AR', 'AX', 'BD', 'BJ', 'BP', 'BV', 'CB', 'CH', 'CN', '');

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND es_retirado <> 'S' AND activo = 1 AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
	$row = 8; // fila base
	$filaBase = $row;
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$asignaturas = $db->consulta("SELECT a.id_asignatura, as_abreviatura, as_nombre FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if ($total_asignaturas > 0) {
			$rowAsignatura = 6;
			$contAsignatura = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$asignatura = $asignatura["as_nombre"];

				// Obtener el número de períodos de evaluación

				$periodos_evaluacion = $db->consulta("SELECT pc.id_periodo_evaluacion, pc.pe_ponderacion FROM sw_periodo_evaluacion pe, sw_periodo_evaluacion_curso pc WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion AND pc.id_periodo_lectivo = $id_periodo_lectivo AND pc.id_curso = $id_curso AND id_tipo_periodo IN (1, 7, 8) ORDER BY pc_orden");

				$num_total_registros = $db->num_rows($periodos_evaluacion);

				// if (!$es_bach_tecnico && !$es_fin_subnivel) {
				// 	if ($num_periodos == 2)
				// 		$colNombreAsignatura = $colAsignaturas[$contAsignatura];
				// } else {
				// 	if (!$es_fin_subnivel)
				// 		$colNombreAsignatura = $colAsignaturas2[$contAsignatura];
				// 	else
				// 		$colNombreAsignatura = $colAsignaturas3[$contAsignatura];
				// }

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, $asignatura);

				if ($num_total_registros > 0) {

					$contador_periodos = 0;
					$suma_ponderados = 0;

					while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
						$contador_periodos++;
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$pe_ponderacion = $periodo["pe_ponderacion"];

						//
						$qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
						$resultado = $db->consulta($qry);
						$calificacion = $db->fetch_assoc($resultado);
						$calificacion_sub_periodo = $calificacion["promedio"];

						//
						$promedio_ponderado = $calificacion_sub_periodo * $pe_ponderacion;
						// $suma_ponderados += $promedio_ponderado;

						if ($es_intensivo) { // Se trata de un periodo intensivo
							switch ($contador_periodos) {
								case 1:
									// if (!$es_fin_subnivel)
									// 	$colPeriodo = $colPrimerPeriodo[$contAsignatura];
									// else
									// 	$colPeriodo = $colPrimerPeriodo2[$contAsignatura];
									$colPeriodo = $colPrimerPeriodo[$contAsignatura];
									break;

								case 2:
									// if (!$es_fin_subnivel)
									// 	$colPeriodo = $colSegundoPeriodo[$contAsignatura];
									// else
									// 	$colPeriodo = $colSegundoPeriodo2[$contAsignatura];
									$colPeriodo = $colSegundoPeriodo[$contAsignatura];
									break;

								case 3:
									if (!$es_fin_subnivel)
										$colPeriodo = $colTercerPeriodo[$contAsignatura];
									else
										$colPeriodo = $colTercerPeriodo2[$contAsignatura];
									break;

								case 4:
									$colPeriodo = $colCuartoPeriodo[$contAsignatura];
									break;

								default:
									# code...
									break;
							}
						} else { // Se trata de un periodo no intensivo
							switch ($contador_periodos) {
								case 1:
									if (!$es_fin_subnivel)
										$colPeriodo = $colPrimerPeriodo2[$contAsignatura];
									else
										$colPeriodo = $colPrimerPeriodo3[$contAsignatura];
									break;

								case 2:
									if (!$es_fin_subnivel)
										$colPeriodo = $colSegundoPeriodo2[$contAsignatura];
									else
										$colPeriodo = $colSegundoPeriodo3[$contAsignatura];
									break;

								case 3:
									if (!$es_fin_subnivel)
										$colPeriodo = $colTercerPeriodo2[$contAsignatura];
									else
										$colPeriodo = $colTercerPeriodo3[$contAsignatura];
									break;

								case 4:
									if (!$es_fin_subnivel)
										$colPeriodo = $colCuartoPeriodo[$contAsignatura];
									else
										$colPeriodo = $colCuartoPeriodo2[$contAsignatura];
									break;

								case 5:
									$colPeriodo = $colQuintoPeriodo[$contAsignatura];
									break;

								default:
									# code...
									break;
							}
						}

						// if ($contador_periodos == 4) {
						// 	$colPeriodo = $colCuartoPeriodo[$contAsignatura];
						// }

						$calificacion_sub_periodo = $calificacion_sub_periodo == 0 ? "" : substr($calificacion_sub_periodo, 0, strpos($calificacion_sub_periodo, '.') + 3);

						$objPHPExcel->getActiveSheet()->setCellValue($colPeriodo . $row, $calificacion_sub_periodo);
					} // fin while $periodo_evaluacion

					if ($contador_periodos == $num_total_registros) {
						// if (!$es_bach_tecnico && !$es_fin_subnivel) {
						// 	$colPonderadoFinal = $colPuntajeFinal[$contAsignatura];
						// } else {
						// 	if (!$es_fin_subnivel)
						// 		$colPonderadoFinal = $colPuntajeFinal2[$contAsignatura];
						// 	else
						// 		$colPonderadoFinal = $colPuntajeFinal3[$contAsignatura];
						// }

						//
						$query = $db->consulta("SELECT calcular_promedio_periodo_lectivo($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
						$calificacion = $db->fetch_assoc($query);
						$promedio_anual = $calificacion["promedio"];

						$promedio_anual = $promedio_anual == 0 ? "" : substr($promedio_anual, 0, strpos($promedio_anual, '.') + 3);

						$objPHPExcel->getActiveSheet()->setCellValue($colPuntajeFinal[$contAsignatura] . $row, $promedio_anual);
					}
				} // fin if $periodo_evaluacion

				// Proyecto Integrador

				$contAsignatura++;
			} // fin while $asignatura

			// Calculo del comportamiento general de todas las materias
			// Determinar quien ingresa el comportamiento
			$consulta = $db->consulta("SELECT c.quien_inserta_comp FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
			$registro = $db->fetch_object($consulta);
			$quien_inserta_comp = $registro->quien_inserta_comp;

			if ($quien_inserta_comp == 2) {
				$periodos_eval_comp = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");

				$suma_comp_periodos = 0;
				$contador_periodos = 0;

				while ($periodo = $db->fetch_object($periodos_eval_comp)) {
					$id_periodo_evaluacion = $periodo->id_periodo_evaluacion;

					$qry = "SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion AND id_tipo_aporte = 1";
					$aportes_eval_comp = $db->consulta($qry);

					$suma_comp_aportes = 0;
					$contador_aportes = 0;

					while ($aporte = $db->fetch_object($aportes_eval_comp)) {
						$id_aporte_evaluacion = $aporte->id_aporte_evaluacion;

						$asignaturas_eval_comp = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");

						$suma_comp_asignatura = 0;
						$contador_asignaturas = 0;

						while ($asignatura = $db->fetch_object($asignaturas_eval_comp)) {
							$id_asignatura = $asignatura->id_asignatura;

							$qry = "SELECT co_calificacion FROM sw_calificacion_comportamiento WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion AND $id_asignatura";

							$registro = $db->consulta($qry);
							$num_registros = $db->num_rows($registro);
							$registro = $db->fetch_object($registro);

							$co_calificacion = $num_registros == 0 ? 0 : $registro->co_calificacion;
							$suma_comp_asignatura += $co_calificacion;

							$contador_asignaturas++;
						}

						$suma_comp_asignatura = ceil($suma_comp_asignatura / $contador_asignaturas);
						$suma_comp_aportes += $suma_comp_asignatura;
						$contador_aportes++;
					}

					$suma_comp_aportes = ceil($suma_comp_aportes / $contador_aportes);
					$suma_comp_periodos += $suma_comp_aportes;
					$contador_periodos++;
				}

				$suma_comp_periodos = ceil($suma_comp_periodos / $contador_periodos);
			}

			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $suma_comp_periodos");
			$registro = $db->fetch_object($query);
			$comportamiento = $registro->ec_equivalencia;

			// if (!$es_bach_tecnico && !$es_fin_subnivel) {
			// 	$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento . $row, $comportamiento);
			// } else {
			// 	if (!$es_fin_subnivel)
			// 		$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento2 . $row, $comportamiento);
			// 	else
			// 		$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento2 . $row, $comportamiento);
			// }
			$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento . $row, $comportamiento);
		} // fin if $total_asignatura
		$row++;
	}

	// Elimino las filas excedentes
	if ($num_total_estudiantes < 50)
		$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);
}

$filename = "CUADRO ANUAL " . str_replace('"', '', $nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
