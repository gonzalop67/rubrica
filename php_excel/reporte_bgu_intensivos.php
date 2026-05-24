<?php
/*
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

function truncar($numero, $digitos)
{
	$truncar = pow(10, $digitos);
	return intval($numero * $truncar) / $truncar;
}

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

set_time_limit(0);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

/* PHPExcel_IOFactory */
require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../php_excel/Classes/PHPExcel/IOFactory.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once("../funciones/funciones_sitio.php");

$funciones = new funciones();

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$AMIEInstitucion = $institucion->obtenerAMIEInstitucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();
$distritoInstitucion = $institucion->obtenerDistritoInstitucion();

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$fecha_actual = date_create(date('Y-m-d')); //fecha actual

$db = new MySQL();

//Obtener la oferta educativa y el nombre del curso
$qry = $db->consulta("SELECT es_figura,
                             es_nombre,
							 cu_nombre 
                        FROM sw_tipo_educacion t, 
                             sw_especialidad e, 
                             sw_curso c, 
                             sw_paralelo p
                       WHERE t.id_tipo_educacion = e.id_tipo_educacion
                         AND e.id_especialidad = c.id_especialidad
                         AND c.id_curso = p.id_curso 
                         AND p.id_paralelo = $id_paralelo");
$res = $db->fetch_object($qry);
$nivelEducacion = $res->es_figura;
$curso = $res->cu_nombre;

$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);

$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);

$docentes = $db->consulta("SELECT u.us_shortname 
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
$docente = $db->fetch_object($docentes);
$nombreDocente = $docente->us_shortname;

$nombreParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
$nombreCurso = $paralelo->obtenerNombreParalelo($id_paralelo);

// Obtengo quien inserta el comportamiento (0 : Docentes, 1 : Tutor)
$consulta = $db->consulta("SELECT quien_inserta_comp FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
$resultado = $db->fetch_assoc($consulta);
$quien_inserta_comportamiento = $resultado["quien_inserta_comp"];

// Vector de configuracion para las columnas
$colParciales = array('E', 'F', 'I', 'L', 'M', 'P');
$colComportamiento = array('T', 'U');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$baseFilename = "CUADRO FINAL BGU INTENSIVO";
$objPHPExcel = $objReader->load("../templates/" . $baseFilename . ".xlsx");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('C5', $AMIEInstitucion)
	->setCellValue('C6', $distritoInstitucion)
	->setCellValue('C7', $nivelEducacion)
	->setCellValue('C8', $curso)
	->setCellValue('C9', $nombreAsignatura)
	->setCellValue('R7', $nombrePeriodoLectivo)
	->setCellValue('R8', $nombreDocente)
	->setCellValue('R9', $nombreParalelo)
	->setCellValue('T5', $nombreInstitucion);

$estudiantes = $db->consulta("SELECT e.id_estudiante, 
							  es_apellidos, 
							  es_nombres,
							  es_cedula,
							  es_retirado,
							  es_genero 
						 FROM sw_estudiante e, 
							  sw_estudiante_periodo_lectivo p 
						WHERE e.id_estudiante = p.id_estudiante 
						  AND activo = 1 
						  AND p.id_paralelo = $id_paralelo  
					 ORDER BY es_apellidos, es_nombres");

$num_total_estudiantes = $db->num_rows($estudiantes);

if ($num_total_estudiantes > 0) {
	$row = 14; // fila base
	$col = 'E'; // columna base
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$retirado = $estudiante["es_retirado"];
		$genero = $estudiante["es_genero"];

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$terminacion = ($genero == 'M') ? '' : 'A';
		if ($retirado == 'S') $objPHPExcel->getActiveSheet()->setCellValue('X' . $row, "DESERTOR" . $terminacion);

		//Consultar las calificaciones de cada parcial...
		$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
		$num_total_registros = $db->num_rows($periodo_evaluacion);
		if ($num_total_registros > 0) {
			$contParcial = 0;
			while ($periodo = $db->fetch_assoc($periodo_evaluacion)) {
				$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

				$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
				$aporte_evaluacion = $db->consulta($qry);
				$num_total_registros = $db->num_rows($aporte_evaluacion);
				if ($num_total_registros > 0) {
					while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
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
						$promedio = truncar($suma_rubricas / $contador_rubricas, 2);
						$objPHPExcel->getActiveSheet()->setCellValue($colParciales[$contParcial] . $row, $promedio);
						$contParcial++;
					}
				}
			}
		}

		if ($quien_inserta_comportamiento == 0) { // Ingresan el comportamiento los docentes
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion 
													FROM sw_periodo_evaluacion 
												   WHERE pe_principal = 1
												     AND id_periodo_lectivo = $id_periodo_lectivo");

			// Se utilizará un bucle while para los periodos de evaluación
			$contPeriodos = 0;
			while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
				$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion, 
															ap_fecha_cierre
													   FROM sw_periodo_evaluacion p, 
															sw_aporte_evaluacion a 
													  WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
														AND ap_tipo = 1 
														AND p.id_periodo_evaluacion = $id_periodo_evaluacion");

				$suma_aportes = 0;
				$contador_aportes = 0;

				while ($aporte = $db->fetch_assoc($aportes_evaluacion)) {
					$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];

					// Aqui se calcula el promedio del comportamiento asignado por los docentes

					$asignaturas = $db->consulta("SELECT a.id_asignatura 
													FROM sw_asignatura a, 
														 sw_asignatura_curso ac, 
														 sw_paralelo p 
												   WHERE a.id_asignatura = ac.id_asignatura 
													 AND p.id_curso = ac.id_curso 
													 AND p.id_paralelo = $id_paralelo");

					$suma_comp_asignatura = 0;
					$contador_asignaturas = 0;

					while ($asignatura = $db->fetch_assoc($asignaturas)) {
						$contador_asignaturas++;
						$id_asignatura = $asignatura["id_asignatura"];

						// Aqui se consulta la calificacion del comportamiento ingresada por cada docente
						$calificaciones = $db->consulta("SELECT co_calificacion 
														   FROM sw_calificacion_comportamiento 
														  WHERE id_estudiante = $id_estudiante 
															AND id_paralelo = $id_paralelo 
															AND id_asignatura = $id_asignatura 
															AND id_aporte_evaluacion = $id_aporte_evaluacion");
						if ($db->num_rows($calificaciones) > 0) {
							$calificaciones = $db->fetch_assoc($calificaciones);
							$calificacion = $calificaciones["co_calificacion"];
						} else
							$calificacion = 0;
						$suma_comp_asignatura += $calificacion;
					}

					$promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
					$suma_aportes += $promedio_comp;

					$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comp");
					$resultado = $db->fetch_assoc($query);
					$comp_docentes = $resultado["ec_equivalencia"];

					$contador_aportes++;
				}

				// Calculo el promedio quimestral
				$promedio_quimestral = ceil($suma_aportes / $contador_aportes);
				$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_quimestral");
				$resultado = $db->fetch_assoc($query);
				$comp_quimestral = $resultado["ec_equivalencia"];

				// Determino la fecha de cierre del examen quimestral
				$fecha = $db->consulta("SELECT ap_fecha_cierre FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
				$aporte = $db->fetch_assoc($fecha);
				$fecha_cierre = date_create($aporte["ap_fecha_cierre"]); //fecha de db
				$interval = date_diff($fecha_cierre, $fecha_actual, false);
				$dias = intval($interval->format('%R%a'));

				$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento[$contPeriodos] . $row, $dias < 0 ? " " : $comp_quimestral);

				$contPeriodos++;
			}
		} else {
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion 
														FROM sw_periodo_evaluacion 
													WHERE pe_principal = 1
														AND id_periodo_lectivo = $id_periodo_lectivo");
			// Se utilizará un bucle while para los periodos de evaluación
			$contPeriodos = 0;
			while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
				$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion, 
																ap_fecha_cierre
														   FROM sw_periodo_evaluacion p, 
																sw_aporte_evaluacion a 
														  WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
															AND ap_tipo = 1 
															AND p.id_periodo_evaluacion = $id_periodo_evaluacion");

				$suma_comportamientos = 0;
				$contador_aportes = 0;

				while ($aporte = $db->fetch_assoc($aportes_evaluacion)) {
					$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];

					// Aquí se obtiene el comportamiento asentado por el tutor
					$calificaciones = $db->consulta("SELECT co_calificacion, 
																ec_correlativa
														   FROM sw_comportamiento_tutor ct, 
														   		sw_escala_comportamiento ec
													      WHERE ec.id_escala_comportamiento = ct.id_escala_comportamiento
														    AND id_estudiante = $id_estudiante 
															AND id_paralelo = $id_paralelo 
															AND id_aporte_evaluacion = $id_aporte_evaluacion");
					if ($db->num_rows($calificaciones) > 0) {
						$resultado = $db->fetch_assoc($calificaciones);
						$calificacion = $resultado["co_calificacion"];
						$correlativa = $resultado["ec_correlativa"];
					} else {
						$calificacion = ' ';
						$correlativa = 0;
					}

					$suma_comportamientos += $correlativa;
					$contador_aportes++;
				}

				// Aquí calculo el promedio quimestral del comportamiento
				$promedio_quimestral = ceil($suma_comportamientos / $contador_aportes);
				$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_quimestral");
				$resultado = $db->fetch_assoc($query);
				$comp_quimestral = $resultado["ec_equivalencia"];

				// Determino la fecha de cierre del examen quimestral
				$fecha = $db->consulta("SELECT ap_fecha_cierre FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
				$aporte = $db->fetch_assoc($fecha);
				$fecha_cierre = date_create($aporte["ap_fecha_cierre"]); //fecha de db
				$interval = date_diff($fecha_cierre, $fecha_actual, false);
				$dias = intval($interval->format('%R%a'));

				$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento[$contPeriodos] . $row, $dias < 0 ? " " : $comp_quimestral);

				$contPeriodos++;
			}

			$objPHPExcel->getActiveSheet()->setCellValue('W' . $row, 7);

			// Aqui va el codigo para determinar el examen con el cual aprobo la materia (si existe)
			if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
				// Obtencion de la calificacion del examen supletorio
				$examen_supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
				if ($examen_supletorio >= 7) {
					$objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $examen_supletorio);
				}
			}

		}
		$row++;
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("CUADRO FINAL " . str_replace('"', '', $nombreCurso) . " " . $nombrePeriodoLectivo . ".xlsx");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=\"" . "CUADRO FINAL " . str_replace('"', '', $nombreCurso) . " " . $nombrePeriodoLectivo . ".xlsx" . "\"");
readfile("CUADRO FINAL " . str_replace('"', '', $nombreCurso) . " " . $nombrePeriodoLectivo . ".xlsx");
