<?php
require_once "../scripts/clases/class.mysql.php";
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

require_once "../vendor/autoload.php";

//load phpspreadsheet class using namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;

/* Error reporting */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

$db = new MySQL();

function truncar($numero, $digitos)
{
	$truncar = pow(10, $digitos);
	return intval($numero * $truncar) / $truncar;
}

function contarAsignaturas($id_paralelo, $id_curso)
{
	global $db;

	$consulta = $db->consulta("SELECT COUNT(*) AS total_registros FROM sw_malla_curricular m, sw_asignatura a WHERE a.id_asignatura = m.id_asignatura AND id_curso = $id_curso");
	$registro = $db->fetch_assoc($consulta);
	$num_asignaturas = $registro["total_registros"];
	if ($num_asignaturas == 0) {
		$consulta = $db->consulta("SELECT COUNT(*) AS total_registros FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo");
		$registro = $db->fetch_assoc($consulta);
		$num_asignaturas = $registro["total_registros"];
		if ($num_asignaturas == 0) {
			$consulta = $db->consulta("SELECT COUNT(*) AS total_registros FROM sw_malla_curricular m, sw_asignatura a WHERE a.id_asignatura = m.id_asignatura AND id_curso = $id_curso AND id_tipo_asignatura = 1");
			$registro = $db->fetch_assoc($consulta);
			$num_asignaturas = $registro["total_registros"];
		}
	}
	return $num_asignaturas;
}

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

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

$consulta = $db->consulta("SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$id_curso = $db->fetch_object($consulta)->id_curso;

$consulta = $db->consulta("SELECT cu_nombre, es_nombre, es_figura, es_bach_tecnico FROM sw_curso cu, sw_especialidad es WHERE cu.id_especialidad = es.id_especialidad AND cu.id_curso = $id_curso");
$resultado = $db->fetch_object($consulta);
if ($resultado->es_bach_tecnico == 0) {
	$nombreCurso = $resultado->cu_nombre . " DE " . $resultado->es_figura;
} else {
	$nombreCurso = $resultado->cu_nombre . " DE " . $resultado->es_nombre . ": " . $resultado->es_figura;
}

$jornada = $institucion->obtenerJornada($id_paralelo);

$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

// Nombre del Periodo Lectivo
$consulta = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$periodo_lectivo = $db->fetch_object($consulta);
$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;

// Nombre del Paralelo
// $nombreParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
$consulta = $db->consulta("SELECT pa_nombre FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$paralelo = $db->fetch_object($consulta);
$nombreParalelo = $paralelo->pa_nombre;

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = contarAsignaturas($id_paralelo, $id_curso);

//load the template
//load from xlsx template
$objReader = IOFactory::createReader('Xls');
$baseFilename = "CUADRO QUIMESTRAL - ";
$objPHPExcel = $objReader->load("../templates/" . $baseFilename . $numAsignaturas . " ASIGNATURAS.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
	->setCellValue('A3', 'CUADRO DE CALIFICACIONES - ' . $nombrePeriodoEvaluacion)
	->setCellValue('B62', $nombreRector)
	->setCellValue('B63', $genero_rector)
	->setCellValue('F62', $nombreSecretario)
	->setCellValue('F63', $genero_secretario);

// Renombrar la hoja de calculo
$objPHPExcel->getActiveSheet()->setTitle($nombrePeriodoEvaluacion);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S');

// Columna para escribir el promedio de las asignaturas
switch ($numAsignaturas) {
	case 6:
		$colPromedio = 'I';
		$colComportamiento = 'J';
		$colNomParalelo = 'I';
		$colObservaciones = 'K';
		break;
	case 7:
		$colPromedio = 'J';
		$colComportamiento = 'K';
		$colNomParalelo = 'J';
		$colObservaciones = 'L';
		break;
	case 8:
		$colPromedio = 'K';
		$colComportamiento = 'L';
		$colNomParalelo = 'K';
		$colObservaciones = 'M';
		break;
	case 9:
		$colPromedio = 'L';
		$colComportamiento = 'M';
		$colNomParalelo = 'L';
		$colObservaciones = 'N';
		break;
	case 10:
		$colPromedio = 'M';
		$colComportamiento = 'N';
		$colNomParalelo = 'M';
		$colObservaciones = 'O';
		break;
	case 11:
		$colPromedio = 'N';
		$colComportamiento = 'P';
		$colNomParalelo = 'N';
		$colObservaciones = 'Q';
		break;
	case 12:
		$colPromedio = 'O';
		$colComportamiento = 'P';
		$colNomParalelo = 'O';
		$colObservaciones = 'Q';
		break;
	case 13:
		$colPromedio = 'P';
		$colComportamiento = 'Q';
		$colNomParalelo = 'P';
		$colObservaciones = 'R';
		break;
	case 14:
		$colPromedio = 'Q';
		$colComportamiento = 'R';
		$colNomParalelo = 'Q';
		$colObservaciones = 'S';
		break;
	case 15:
		$colPromedio = 'R';
		$colComportamiento = 'S';
		$colNomParalelo = 'R';
		$colObservaciones = 'T';
		break;
	case 16:
		$colPromedio = 'S';
		$colComportamiento = 'T';
		$colNomParalelo = 'S';
		$colObservaciones = 'U';
		break;
	case 17:
		$colPromedio = 'T';
		$colComportamiento = 'V';
		$colNomParalelo = 'T';
		$colObservaciones = 'W';
		break;
}

$objPHPExcel->getActiveSheet()->setCellValue('A5', $nombreCurso);
$objPHPExcel->getActiveSheet()->setCellValue('B7', 'JORNADA ' . $jornada);
$objPHPExcel->getActiveSheet()->setCellValue('F4', $nombrePeriodoLectivo);
$objPHPExcel->getActiveSheet()->setCellValue($colNomParalelo . '7', 'PARALELO ' . $nombreParalelo);

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante

//Obtengo quien inserta el comportamiento: 1 = Tutor; 2 = Docente
$query2 = $db->consulta("SELECT quien_inserta_comp_id FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$resultado = $db->fetch_assoc($query2);
$quien_inserta_comp = $resultado["quien_inserta_comp_id"];

// Get the students data
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
	while ($estudiante = $db->fetch_assoc($estudiantes)) {
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$retirado = $estudiante["es_retirado"];
		$genero = $estudiante["dg_abreviatura"];

		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);

		$terminacion = ($genero == 'M') ? 'O' : 'A';
		if ($retirado == 'S') $objPHPExcel->getActiveSheet()->setCellValue($colObservaciones . $row, "RETIRAD" . $terminacion);

		$asignaturas = $db->consulta("SELECT a.id_asignatura, 
											 a.id_tipo_asignatura, 
											 as_nombre 
										FROM sw_asignatura_curso ac, 
											 sw_paralelo p, 
											 sw_asignatura a 
									   WHERE ac.id_curso = p.id_curso 
									     AND ac.id_asignatura = a.id_asignatura 
										 AND id_paralelo = $id_paralelo 
										 AND id_tipo_asignatura = 1
									   ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if ($total_asignaturas > 0) {
			$rowAsignatura = 8;
			$sumaPromedios = 0;
			$comportamiento = 0;
			$contAsignaturas = 0;
			$sumaComportamiento = 0;
			$promedio_quimestral = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				// Aqui proceso los promedios de cada asignatura
				$id_tipo_asignatura = $asignatura["id_tipo_asignatura"];
				$id_asignatura = $asignatura["id_asignatura"];
				$asignatura = $asignatura["as_nombre"];

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignaturas] . $rowAsignatura, $asignatura);

				if ($id_tipo_asignatura == 1) {
					$consulta = $db->consulta("SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_sub_periodo");
					$registro = $db->fetch_object($consulta);
					$promedio_quimestral = $registro->promedio_sub_periodo;

					// if ($impresion_para_juntas == 1 && $promedio_quimestral >= 7) {
					// 	$promedio_quimestral = "";
					// }

					$sumaPromedios += $promedio_quimestral;

					$promedio_quimestral = ($promedio_quimestral == "" || $promedio_quimestral == 0) ? "" : substr($promedio_quimestral, 0, strpos($promedio_quimestral, '.') + 3);

					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignaturas] . $row, $promedio_quimestral);
				} else {
					//Aqui consulto las asignaturas cualitativas

				}

				// 

				// Si el docente ingresa el comportamiento...

				// if ($quien_inserta_comp == 0) {
				// 	$query = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS comportamiento");
				// 	$registro = $db->fetch_assoc($query);
				// 	$comportamiento = $registro["comportamiento"];
				// 	$sumaComportamiento += $comportamiento;
				// }

				$contAsignaturas++;
			} // fin while $asignatura

			// Calculo e impresion del promedio de asignaturas
			$promedioAsignaturas = $sumaPromedios / $total_asignaturas;

			// if (!$impresion_para_juntas && $promedioAsignaturas != 0) {
				$promedioAsignaturas = ($promedioAsignaturas == "" || $promedioAsignaturas == 0) ? "" : substr($promedioAsignaturas, 0, strpos($promedioAsignaturas, '.') + 3);

				$objPHPExcel->getActiveSheet()->setCellValue($colPromedio . $row, $promedioAsignaturas);
			// }

			// Calculo e impresion del promedio de comportamiento
			if ($quien_inserta_comp == 0) {
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
			$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento . $row, $equivalencia);
			// $objPHPExcel->getActiveSheet()->setCellValue($colComportamiento . $row, $contAsignaturas);
		} // fin if $total_asignatura
		$row++;
	}
}

// Elimino las filas excedentes
if ($num_total_estudiantes < 50)
	$objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);

// Aqui va el codigo para desplegar la lista de docentes del paralelo

$objPHPExcel->setActiveSheetIndex(1);
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
	$row = 4;
	while ($docente = $db->fetch_object($docentes)) {
		$asignatura = $docente->as_nombre;
		$profesor = $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $asignatura);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $profesor);
		$row++;
	}
}

$filename = "CUADRO $nombrePeriodoEvaluacion $nombreCurso $nombreParalelo - $nombrePeriodoLectivo.xls";

//set the header first, so the result will be treated as an xlsx file.
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
