<?php
require_once('../fpdf186/fpdf.php');
require_once("../scripts/clases/class.mysql.php");
require_once('../scripts/clases/class.paralelos.php');
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.especialidades.php');
require_once('../scripts/clases/class.periodos_lectivos.php');
require_once("../scripts/clases/class.funciones.php");

function obtenerFechaAperturaSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $id_tipo_perido, $id_periodo_lectivo)
{
	global $db;
	// Obtencion de la fecha de apertura del aporte indicado por el campo id_tipo_perido
	$qry = $db->consulta("SELECT ac.ap_fecha_apertura 
        FROM sw_periodo_evaluacion p, 
             sw_aporte_evaluacion a, 
             sw_aporte_paralelo_cierre ac,  
             sw_paralelo pa 
       WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion 
         AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion  
         AND pa.id_paralelo = ac.id_paralelo 
         AND pa.id_paralelo = $id_paralelo 
         AND id_tipo_periodo = $id_tipo_perido 
         AND p.id_periodo_lectivo = $id_periodo_lectivo");
	$registro = $db->fetch_assoc($qry);
	return $registro["ap_fecha_apertura"];
}

function obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $id_tipo_perido, $id_periodo_lectivo)
{
	global $db;
	$qry = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $id_tipo_perido AND p.id_periodo_lectivo = $id_periodo_lectivo");
	$registro = $db->fetch_assoc($qry);
	$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

	$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

	if ($qry) {
		$rubrica_estudiante = $db->fetch_assoc($qry);
		$calificacion = $rubrica_estudiante["re_calificacion"];
	} else {
		$calificacion = 0;
	}

	return $calificacion;
}

// Aca obtengo la fecha actual del sistema
$fecha_actual_string = fecha_actual();

// Aqui obtengo los parametros pasados mediante POST
$id_estudiante = $_POST["idestudiante"];
$id_periodo_lectivo = $_POST["idperiodolectivo"];

// Aqui instanciamos la clase funciones
$funciones = new funciones();

// Nombre de la institución educativa
$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

// Nombre del Periodo Lectivo
$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

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

//**********************************************************************************************************
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
$title = 'REPORTE DEL PERIODO LECTIVO: ' . $nombrePeriodoLectivo;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(7);

$pdf->SetFont('Helvetica', '', 16);
$title = $nombreFiguraProfesional;
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
$title = 'CURSO: ' . $nombreParalelo;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetFillColor(95, 95, 95);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(10, 5, 'NRO.', 0, 0, 'L', 1);
$pdf->Cell(60, 5, 'ASIGNATURA', 0, 0, 'L', 1);

$consulta = $db->consulta("SELECT pe_abreviatura, pe_ponderacion FROM sw_periodo_evaluacion WHERE id_tipo_periodo IN (1, 7) AND id_periodo_lectivo = " . $id_periodo_lectivo);

$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
	while ($titulo_periodo = $db->fetch_assoc($consulta)) {
		$pdf->Cell(16, 5, $titulo_periodo["pe_abreviatura"], 0, 0, 'L', 1);
		$pdf->Cell(16, 5, ($titulo_periodo["pe_ponderacion"] * 100) . "%", 0, 0, 'L', 1);
	}
}

//Leyendas de los exámenes supletorios, remediales, de gracia
$pdf->Cell(20, 5, "NOTA F.", 0, 0, 'L', 1);
$pdf->Cell(22, 5, "OBSERVACION", 0, 0, 'L', 1);

$consulta = $db->consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo IN (2, 3) AND id_periodo_lectivo = " . $id_periodo_lectivo);

while ($periodo = $db->fetch_assoc($consulta)) {
	$pdf->Cell(16, 5, $periodo["pe_abreviatura"], 0, 0, 'C', 1);
}

$pdf->Cell(22, 5, "OBS. FINAL", 0, 0, 'L', 1);
$pdf->Ln();

$pdf->SetFont('Helvetica', '', 8);
$pdf->SetTextColor(0, 0, 0);

// Segundo debo consultar las asignaturas del estudiante
$asignaturas = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND a.id_tipo_asignatura = 1 ORDER BY ac_orden");

$contador = 0;
$num_asignaturas_aprueba = 0;
$suma_promedio_asignaturas = 0;
$contador_asignaturas = 0;

while ($asignatura = $db->fetch_assoc($asignaturas)) {
	$contador++;
	$contador_asignaturas++;
	$contador_sin_examen = 0;

	if ($contador % 2 == 0) {
		$pdf->SetFillColor(204, 204, 204);
	} else {
		$pdf->SetFillColor(245, 245, 245);
	}

	$pdf->Cell(10, 5, $contador, 0, 0, 'L', 1);

	$id_asignatura = $asignatura["id_asignatura"];
	$nom_asignatura = utf8_decode($asignatura["as_nombre"]);

	$pdf->Cell(60, 5, $nom_asignatura, 0, 0, 'L', 1);

	//*************************************************************************************
	$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion, pe_ponderacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo IN (1, 7)");
	$num_total_registros = $db->num_rows($periodo_evaluacion);
	if ($num_total_registros > 0) {
		$suma_periodos = 0;
		$contador_periodos = 0;
		$suma_ponderados = 0;

		while ($periodo = $db->fetch_object($periodo_evaluacion)) {
			$contador_periodos++;
			$id_periodo_evaluacion = $periodo->id_periodo_evaluacion;

			$id_periodo_evaluacion = $periodo->id_periodo_evaluacion;
			$query = $db->consulta("SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
			$record = $db->fetch_object($query);
			$promedio_sub_periodo = $record->calificacion;
			$promedio_ponderado = $promedio_sub_periodo * $periodo->pe_ponderacion;
			$suma_ponderados += $promedio_ponderado;

			$promedio_sub_periodo = $promedio_sub_periodo == 0 ? "" : substr($promedio_sub_periodo, 0, strpos($promedio_sub_periodo, '.') + 3);

			$promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio_ponderado, '.') + 3);

			$pdf->Cell(16, 5, $promedio_sub_periodo, 0, 0, 'L', 1);
			$pdf->Cell(16, 5, $promedio_ponderado, 0, 0, 'L', 1);
		} // fin while $periodo_evaluacion

		$suma_ponderados = $suma_ponderados == 0 ? "" : substr($suma_ponderados, 0, strpos($suma_ponderados, '.') + 3);

		$pdf->Cell(20, 5, $suma_ponderados, 0, 0, 'L', 1);

		// Desplegar la equivalencia final (APRUEBA, NO APRUEBA, SUPLETORIO, REMEDIAL)
		$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
		$query = $db->consulta($qry);
		$registro = $db->fetch_object($query);
		$nota_minima = $registro->pe_nota_aprobacion;

		$observacion = "";
		$total_registros = 0;

		$examen_supletorio = 0;
		$examen_remedial = 0;

		$puntaje_final = $suma_ponderados;

		$qry = "SELECT * 
                  FROM sw_equivalencia_supletorios 
                 WHERE id_periodo_lectivo = $id_periodo_lectivo 
                 ORDER BY rango_desde DESC";
		$query = $db->consulta($qry);
		$total_supletorios = $db->num_rows($query);

		if ($puntaje_final >= $nota_minima) {
			$observacion = "APRUEBA";
		} else {
			// Consultar si el estudiante tiene que dar exámenes supletorios o remediales
			while ($registro = $db->fetch_object($query)) {
				$rango_desde = $registro->rango_desde;
				$rango_hasta = $registro->rango_hasta;
				if ($puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
					$observacion = $registro->nombre_examen;
				}
			}
		}

		$pdf->Cell(22, 5, $observacion, 0, 0, 'L', 1);

		// Observación Final (Luego del periodo de evaluación de exámenes supletorios)
		$equiv_final = "";

		if ($observacion != "APRUEBA") {
			if ($observacion == "SUPLETORIO") {
				// Determinar la nota del examen supletorio

				$fecha_actual = new DateTime("now");
				$fecha_apertura = new DateTime(obtenerFechaAperturaSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo));

				if ($fecha_actual >= $fecha_apertura) {
					$examen_supletorio = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);

					if ($examen_supletorio >= 7) {
						// equivalencia final cualitativa
						$equiv_final = "APRUEBA";
						$puntaje_final = 7;
					} else {
						if ($total_registros == 1) {
							$equiv_final = "NO APRUEBA";
						} else {
							// Antigua LOEI
							$examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

							if ($examen_remedial >= 7) {
								$equiv_final = "APRUEBA";
								$puntaje_final = 7;
							} else {
								$equiv_final = "NO APRUEBA";
							}
						}
					}
				}
			} else if ($observacion == "REMEDIAL") {
				// Antigua LOEI
				$examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

				if ($examen_remedial >= 7) {
					$equiv_final = "APRUEBA";
					$puntaje_final = 7;
				} else {
					$equiv_final = "NO APRUEBA";
				}
			} else {
				// No alcanza el puntaje mínimo
				$equiv_final = "NO APRUEBA";
			}
		} else {
			//
			$qry = "SELECT * 
                      FROM sw_equivalencia_supletorios 
                     WHERE id_periodo_lectivo = $id_periodo_lectivo 
                     ORDER BY rango_desde DESC";
			$query = $db->consulta($qry);
		}

		$nota_supletorio = $examen_supletorio == 0 ? " " : substr($examen_supletorio, 0, strpos($examen_supletorio, '.') + 3);

		$nota_remedial = $examen_remedial == 0 ? " " : substr($examen_remedial, 0, strpos($examen_remedial, '.') + 3);

		if ($total_supletorios == 1) {
			$pdf->Cell(16, 5, $nota_supletorio, 0, 0, 'C', 1);
		} else {
			// Antigua LOEI
			$pdf->Cell(16, 5, $nota_supletorio, 0, 0, 'C', 1);
			$pdf->Cell(16, 5, $nota_remedial, 0, 0, 'C', 1);
		}

		$pdf->Cell(22, 5, $equiv_final, 0, 0, 'L', 1);
		// $pdf->Cell(22, 5, $total_supletorios, 0, 0, 'L', 1);

		$suma_promedio_asignaturas += $puntaje_final;
	} // if($num_total_registros>0)

	$pdf->Ln();
}

// Luego consultar las asignaturas cualitativas del estudiante
$promedio_general = $suma_promedio_asignaturas / $contador_asignaturas;
$promedio_general = $promedio_general == 0 ? "" : substr($promedio_general, 0, strpos($promedio_general, '.') + 3);

$pdf->SetFont('Helvetica', '', 9);

$title = "PROMEDIO GENERAL: $promedio_general";

$w = $pdf->GetStringWidth($title);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln();

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

$w = $pdf->GetStringWidth($fecha_actual_string);
$pdf->SetX((297 - $w) / 2);
$pdf->Cell($w, 10, utf8_decode($fecha_actual_string), 0, 0, 'C');

header('Content-Type: application/pdf');
$pdf->Output(utf8_decode($nombreEstudiante) . ".pdf", "I");
