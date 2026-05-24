<?php
require_once('../fpdf16/fpdf.php');
require_once("../scripts/clases/class.mysql.php");
require_once('../scripts/clases/class.paralelos.php');
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.especialidades.php');
require_once('../scripts/clases/class.periodos_lectivos.php');
require_once('../scripts/clases/class.periodos_evaluacion.php');
require_once("../scripts/clases/class.aportes_evaluacion.php");
require_once("../funciones/funciones_sitio.php");

// Aca obtengo la fecha actual del sistema
$fecha_actual = fecha_actual();

// Aqui obtengo los parametros pasados mediante POST
$id_estudiante = $_POST["idestudiante"];
$id_periodo_lectivo = $_POST["idperiodolectivo"];
$id_periodo_evaluacion = $_POST["idperiodoevaluacion"];

// Nombre de la institucion educativa
$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

// Nombre del Periodo Lectivo
$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

// Nombre del Periodo de Evaluacion (Quimestre)
$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

// Aqui obtengo el id_paralelo del estudiante
$db = new MySQL();
$consulta = $db->consulta("SELECT ep.id_paralelo, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.id_estudiante = $id_estudiante");
$registro = $db->fetch_assoc($consulta);
$id_paralelo = $registro["id_paralelo"];

// Nombre del curso y paralelo
$paralelo = new paralelos();
$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

// Nombre de la Figura Profesional
$especialidad = new especialidades();
$nombreFiguraProfesional = $especialidad->obtenerNombreFiguraProfesional($id_paralelo);

// Apellidos y Nombres del Estudiante
$nombreEstudiante = $registro["es_apellidos"] . " " . $registro["es_nombres"];

// Apellidos y Nombres del Tutor del paralelo
$consulta = $db->consulta("SELECT id_usuario FROM sw_paralelo_tutor WHERE id_paralelo = $id_paralelo");
$registro = $db->fetch_assoc($consulta);
$id_usuario = $registro["id_usuario"];

$consulta = $db->consulta("SELECT CONCAT(us_titulo, ' ', us_apellidos, ' ', us_nombres) AS nombreTutor FROM sw_usuario WHERE id_usuario = $id_usuario");
$registro = $db->fetch_assoc($consulta);
$nombreTutor = $registro["nombreTutor"];

//********************************************************************************************************* */
//Creación del objeto FPDF
$pdf = new FPDF('L');
$pdf->AddPage();

$pdf->SetFont('Helvetica', '', 20);
$title = utf8_decode($nombreInstitucion);
$w = $pdf->GetStringWidth($title);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(7);

$pdf->SetFont('Helvetica', '', 18);
$title = 'REPORTE DEL ' . $nombrePeriodoEvaluacion . ': ' . $nombrePeriodoLectivo;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(7);

$pdf->SetFont('Helvetica', '', 12);
$title = 'ESTUDIANTE: ' . utf8_decode($nombreEstudiante);
$w = $pdf->GetStringWidth($title);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(5);

$pdf->SetFont('Helvetica', '', 12);
$title = 'CURSO: ' . utf8_decode($nombreParalelo);
$w = $pdf->GetStringWidth($title);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln();

/**********************************************************************************/
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetFillColor(95, 95, 95);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(10, 5, 'NRO.', 0, 0, 'L', 1);
$pdf->Cell(82, 5, 'ASIGNATURA', 0, 0, 'L', 1);

$consulta = $db->consulta("SELECT ap_abreviatura, ap_ponderacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");

$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
	while ($aporte = $db->fetch_object($consulta)) {
		$titulo_periodo = $aporte->ap_abreviatura;
		$ap_ponderacion = $aporte->ap_ponderacion;
		$pdf->Cell(22, 5, $titulo_periodo, 0, 0, 'C', 1);
		$pdf->Cell(22, 5, ($ap_ponderacion * 100) . "%", 0, 0, 'C', 1);
	}

	$pdf->Cell(22, 5, 'NOTA P.', 0, 1, 'C', 1);
}

$pdf->SetFont('Helvetica', '', 9);
$pdf->SetTextColor(0, 0, 0);

// Segundo debo consultar las asignaturas del estudiante
$asignaturas = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND a.id_tipo_asignatura = 1 ORDER BY ac_orden");

$contador = 0;
while ($asignatura = $db->fetch_assoc($asignaturas)) {
	$contador++;
	$contador_sin_examen = 0;

	if ($contador % 2 == 0) {
		$pdf->SetFillColor(204, 204, 204);
	} else {
		$pdf->SetFillColor(245, 245, 245);
	}

	$pdf->Cell(10, 5, $contador, 0, 0, 'L', 1);

	$id_asignatura = $asignatura["id_asignatura"];
	$nom_asignatura = utf8_decode($asignatura["as_nombre"]);

	$pdf->Cell(82, 5, $nom_asignatura, 0, 0, 'L', 1);

	//*************************************************************************************

	// Aqui se calculan los promedios de cada aporte de evaluacion
	$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion, ap_ponderacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");

	$num_total_registros = $db->num_rows($aporte_evaluacion);
	if ($num_total_registros > 0) {
		// Aqui calculo los promedios y desplegar en la tabla
		$suma_ponderados = 0;
		while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
			$ponderacion = $aporte["ap_ponderacion"];
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
			$promedio = $suma_rubricas / $contador_rubricas; // promedio del aporte
			$ponderado = $promedio * $ponderacion;
			$suma_ponderados += $ponderado;
			$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
			$pdf->Cell(22, 5, $promedio, 0, 0, 'C', 1);
			$ponderado = $ponderado == 0 ? "" : substr($ponderado, 0, strpos($ponderado, '.') + 3);
			$pdf->Cell(22, 5, $ponderado, 0, 0, 'C', 1);
		}
		// Aqui debo calcular el ponderado de los promedios parciales
		$suma_ponderados = $suma_ponderados == 0 ? "" : substr($suma_ponderados, 0, strpos($suma_ponderados, '.') + 3);
		$pdf->Cell(22, 5, $suma_ponderados, 0, 1, 'C', 1);
	}
}

// Luego consultar las asignaturas cualitativas del estudiante



$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$texto = 'Firma del Tutor: __________________________________________';
$pdf->SetFillColor(255, 255, 255);

$pdf->Cell(100, 5, $texto, 0, 0, 'L', 1);

$texto = '  Firma del Estudiante: _____________________________________';
$pdf->Cell(44, 5, $texto, 0, 1, 'L', 1);

$texto = utf8_decode($nombreTutor);
$pdf->Cell(100, 5, $texto, 0, 0, 'C', 1);

$texto = utf8_decode($nombreEstudiante);
$pdf->Cell(100, 5, $texto, 0, 0, 'C', 1);

$pdf->Ln(5);

$w = $pdf->GetStringWidth($fecha_actual);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, utf8_decode($fecha_actual), 0, 0, 'C');

header('Content-Type: application/pdf');
$pdf->Output(utf8_decode($nombreEstudiante) . ".pdf", "I");
