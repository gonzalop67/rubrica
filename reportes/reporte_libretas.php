<?php
require('../fpdf16/fpdf.php');
require('../scripts/clases/class.mysql.php');
require('../scripts/clases/class.paralelos.php');
require('../scripts/clases/class.institucion.php');
require('../scripts/clases/class.tipos_educacion.php');
require('../scripts/clases/class.periodos_lectivos.php');

function equiv_rendimiento($id_periodo_lectivo, $calificacion)
{
	$db = new MySQL();
	// Determinacion de la letra de equivalencia que corresponde a la calificacion dada
	$escala_calificacion = $db->consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
	$equivalencia = "";
	while ($escala = $db->fetch_assoc($escala_calificacion)) {
		$nota_minima = $escala["ec_nota_minima"];
		$nota_maxima = $escala["ec_nota_maxima"];
		if ($calificacion >= $nota_minima && $calificacion <= $nota_maxima) {
			$equivalencia = $escala["ec_equivalencia"];
			break;
		}
	}
	return $equivalencia;
}

function truncar($numero, $digitos)
{
	$truncar = pow(10, $digitos);
	return intval($numero * $truncar) / $truncar;
}

class PDF extends FPDF
{
	var $nombreRector = "";
	var $nombreParalelo = "";
	var $nombreTutor = "";
	var $nombreInstitucion = "";
	var $nomNivelEducacion = "";
	var $logoInstitucion = "";
	var $telefonoInstitucion = "";
	var $direccionInstitucion = "";
	var $nombrePeriodoLectivo = "";
	var $nombrePeriodoEvaluacion = "";

	//Cabecera de página
	function Header()
	{
		//Logo Izquierda
		$this->Image('ministerio.png', 10, 8, 33);
		//Logo Derecha
		$logoInstitucion = dirname(dirname(__FILE__)) . '/public/uploads/' . $this->logoInstitucion;
		$this->Image($logoInstitucion, 247, 5, 23);

		$this->SetFont('Arial', 'B', 13);
		$title1 = $this->nombreInstitucion;
		$w = $this->GetStringWidth($title1);
		$this->SetX((297 - $w) / 2);
		$this->Cell($w, 10, $title1, 0, 0, 'C');
		$this->Ln(4);

		$this->SetFont('Arial', '', 9);
		$title2 = $this->nomNivelEducacion;
		$w = $this->GetStringWidth($title2);
		$this->SetX((297 - $w) / 2);
		$this->Cell($w, 10, $title2, 0, 0, 'C');

		$this->Ln(4);
		$this->SetFont('Arial', 'B', 7);
		$this->Cell(195, 10, "REGIMEN: ", 0, 0, 'R');
		$this->SetFont('Arial', '', 7);
		$this->Cell(10, 10, "SIERRA", 0, 0, 'R');

		$this->Ln(3);
		$this->SetFont('Arial', '', 11);
		$title3 = "REPORTE DE EVALUACION";
		$w = $this->GetStringWidth($title3);
		$this->SetX((297 - $w) / 2);
		$this->Cell($w, 10, $title3, 0, 0, 'C');

		$this->Ln(3);
		$this->SetFont('Arial', 'B', 7);
		$title4 = utf8_decode("AÑO LECTIVO: " . $this->nombrePeriodoLectivo);
		$w = $this->GetStringWidth($title4);
		$this->SetX((297 - $w) / 2);
		$this->Cell($w, 10, $title4, 0, 0, 'C');
		$this->Line(10, 30, 297 - 10, 30); // 20mm from each edge
		$this->Ln();
	}

	//Pie de página
	function Footer()
	{
		//Posición: a 3 cm del final
		$this->SetY(-30);
		//Arial italic 8
		$this->SetFont('Arial', '', 8);
		//Aqui van las firmas de rectora y secretaria
		$this->Cell(0, 10, '___________________________', 0, 0, 'L');
		$titulo1 = '___________________________';
		$w = $this->GetStringWidth($titulo1);
		$this->SetX(200 - $w);
		$this->Cell($w, 8, $titulo1, 0, 0, 'R');
		$this->Ln(5);
		$this->Cell(0, 10, '      ' . $this->nombreRector, 0, 0, 'L');
		$titulo2 = '            ' . $this->nombreTutor;
		$w = $this->GetStringWidth($titulo2);
		$this->SetX(190 - $w);
		$this->Cell($w, 8, $titulo2, 0, 0, 'R');
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(0, 10, '            RECTOR(A)', 0, 0, 'L');
		$titulo3 = '            TUTOR(A)';
		$w = $this->GetStringWidth($titulo3);
		$this->SetX(185 - $w);
		$this->Cell($w, 8, $titulo3, 0, 0, 'R');
	}
}

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$paralelo = new paralelos();
$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$institucion = new institucion();
$nombreInstitucion = utf8_decode($institucion->obtenerNombreInstitucion());
$logoInstitucion = $institucion->obtenerLogoInstitucion();
$nombreRector = utf8_decode($institucion->obtenerNombreRector());

//Obtengo el Nivel de Educación
$nivelEducacion = new tipos_educacion();
$nomNivelEducacion = utf8_decode($nivelEducacion->obtenerNombreTipoEducacion($id_paralelo));

//Creación del objeto de la clase heredada
$pdf = new PDF('L');
$pdf->SetTopMargin(4);

$pdf->nomNivelEducacion = $nomNivelEducacion;
$pdf->nombreParalelo = $nombreParalelo;
$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
$pdf->nombreInstitucion = $nombreInstitucion;
$pdf->logoInstitucion = $logoInstitucion;
$pdf->nombreRector = $nombreRector;

$fecha_actual = date_create(date('Y-m-d')); //fecha actual

$db = new MySQL();

// Obtengo el tutor del grado/curso
$consulta = $db->consulta("SELECT us_shortname FROM sw_usuario u, sw_paralelo_tutor p WHERE u.id_usuario = p.id_usuario AND p.id_paralelo = $id_paralelo AND p.id_periodo_lectivo = $id_periodo_lectivo");
$resultado = $db->fetch_assoc($consulta);
$pdf->nombreTutor = utf8_decode($resultado["us_shortname"]);

// Obtengo quien inserta el comportamiento (0 : Docentes, 1 : Tutor)
$consulta = $db->consulta("SELECT quien_inserta_comp FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
$resultado = $db->fetch_assoc($consulta);
$quien_inserta_comportamiento = $resultado["quien_inserta_comp"];

// Aqui va el codigo para imprimir las calificaciones de los estudiantes
$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND activo = 1 AND ep.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
	$contador = 0;
	while ($paralelo = $db->fetch_assoc($consulta)) {
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'B', 8);
		$nombre_completo = utf8_decode($paralelo["es_apellidos"]) . " " . utf8_decode($paralelo["es_nombres"]);
		$nombre = "ESTUDIANTE: " . $nombre_completo;
		$w = $pdf->GetStringWidth($nombre);
		$pdf->Cell($w, 8, $nombre, 0, 0, 'L');
		$w = $pdf->GetStringWidth($nombreParalelo);
		$pdf->SetX(287 - $w);
		$pdf->Cell($w, 8, $nombreParalelo, 0, 0, 'R');
		$pdf->Ln();
		//Impresión del cuadro de calificaciones
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->Cell(87, 5, " ", 0, 0, 'C');
		//Cálculo del ancho de las etiquetas para los quimestres
		$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion, 
														 pe_nombre
													FROM sw_periodo_evaluacion 
												   WHERE pe_principal = 1
													 AND id_periodo_lectivo = $id_periodo_lectivo");

		while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
			//Obtener el número de aportes de evaluación
			$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
			$nom_periodo = $periodo["pe_nombre"];
			$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
													  FROM sw_aporte_evaluacion 
													 WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
			$total_aportes = $db->num_rows($aporte_evaluacion);
			$width = 9.5 * ($total_aportes + 5);
			$pdf->Cell($width, 5, $nom_periodo, 1, 0, 'C');
		}

		$pdf->Cell(19, 5, "PROM. ANUAL", 1, 0, 'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 5);
		$pdf->Cell(42, 5, "AREAS DE ESTUDIO", 1, 0, 'C');
		$pdf->Cell(45, 5, "ASIGNATURAS", 1, 0, 'C');

		//Cabeceras de los parciales...
		$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion
													FROM sw_periodo_evaluacion 
												   WHERE pe_principal = 1
													 AND id_periodo_lectivo = $id_periodo_lectivo");
		$num_periodos = $db->num_rows($periodos_evaluacion);
		for ($i = 0; $i < $num_periodos; $i++) {
			$promedios_aportes[$i] = 0;
			$ponderados_aportes[$i] = 0;
			$promedios_examenes[$i] = 0;
			$ponderados_examenes[$i] = 0;
			$promedios_quimestrales[$i] = 0;
		}
		$contador_periodos = 0;
		while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
			$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
			$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
													  FROM sw_aporte_evaluacion 
													 WHERE id_periodo_evaluacion = $id_periodo_evaluacion
													   AND ap_tipo = 1");
			$contador_aportes = 0;
			while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
				$contador_aportes++;
				$pdf->Cell(9.5, 5, "P$contador_aportes", 1, 0, 'C');
				$promedios_parciales[$contador_periodos][$contador_aportes - 1] = 0;
			}
			$pdf->Cell(9.5, 5, "PROM", 1, 0, 'C');
			$pdf->Cell(9.5, 5, "(80%)", 1, 0, 'C');
			$pdf->Cell(9.5, 5, "EXAM.", 1, 0, 'C');
			$pdf->Cell(9.5, 5, "(20%)", 1, 0, 'C');
			$pdf->SetFillColor(204, 204, 204);
			$pdf->Cell(9.5, 5, "QUI", 1, 0, 'C', 1);
			$pdf->Cell(9.5, 5, "EC", 1, 0, 'C', 1);
		}

		$pdf->Cell(9.5, 5, "PA", 1, 0, 'C');
		$pdf->Cell(9.5, 5, "EC", 1, 0, 'C');
		//$pdf->Cell(26,6,$nombrePeriodoEvaluacion,1,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', '', 6);
		$id_estudiante = $paralelo["id_estudiante"];
		// Aqui va el codigo para imprimir cada asignatura
		$asignaturas = $db->consulta("SELECT a.id_asignatura, 
												 as_nombre, 
												 ar_nombre 
											FROM sw_asignatura a,
												 sw_area ar,
												 sw_asignatura_curso ac, 
												 sw_paralelo p 
										   WHERE a.id_area = ar.id_area
										     AND a.id_asignatura = ac.id_asignatura 
										     AND p.id_curso = ac.id_curso 
											 AND a.id_tipo_asignatura = 1
											 AND p.id_paralelo = $id_paralelo 
										   ORDER BY ac_orden ASC");
		$contador_asignaturas = 0;
		//Acumuladores y contadores
		$promedios_aportes = 0;
		$promedios_ponderados = 0;
		$promedios_examenes = 0;
		$ponderados_examenes = 0;
		//$promedios_quimestrales = 0; 
		$suma_anual = 0; // Para calcular los promedios anuales de cada asignatura
		while ($asignatura = $db->fetch_assoc($asignaturas)) {
			$contador_asignaturas++;
			$id_asignatura = $asignatura["id_asignatura"];
			$nombreArea = substr(utf8_decode($asignatura["ar_nombre"]), 0, 32);
			$nombreAsignatura = substr(utf8_decode($asignatura["as_nombre"]), 0, 33);
			$pdf->Cell(42, 5, $nombreArea, 1, 0, 'L');
			$pdf->Cell(45, 5, $nombreAsignatura, 1, 0, 'L');
			// Aqui proceso los promedios de cada asignatura
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion 
														FROM sw_periodo_evaluacion 
													   WHERE pe_principal = 1
													     AND id_periodo_lectivo = $id_periodo_lectivo");
			// Se utilizará un bucle while para los periodos de evaluación
			$contador_periodos = 0;
			$suma_periodos = 0;
			$contador_aportes = 0;
			$total_periodos_completados = 0;

			while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
				// Aqui despliego los registros de cada periodo de evaluación
				$contador_periodos++;
				$promedios_quimestre = 0;
				$total_parciales_ingresados = 0;
				$total_examenes_ingresados = 0;

				$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				//Calcular el número de parciales del quimestre
				$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
														  FROM sw_aporte_evaluacion 
													     WHERE id_periodo_evaluacion = $id_periodo_evaluacion
														   AND ap_tipo = 1");
				$total_parciales = $db->num_rows($aportes_evaluacion);
				$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion, 
															   ap_fecha_cierre, 
															   ap_tipo
														  FROM sw_periodo_evaluacion p, 
														  	   sw_aporte_evaluacion a 
														 WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
														   AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
				// Aqui calculo los promedios y desplegar en la tabla
				$suma_promedios = 0;
				$contador_parciales = 0;
				$contador_cierres = 0;
				while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {

					$contador_aportes++;
					$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
					$tipo_aporte = $aporte["ap_tipo"];
					$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion 
															   FROM sw_rubrica_evaluacion r,
																	sw_asignatura a
															  WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
															    AND a.id_tipo_asignatura = 1
																AND id_aporte_evaluacion = $id_aporte_evaluacion");
					$total_rubricas = $db->num_rows($rubrica_evaluacion);
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

					$pdf->SetFont('Arial', '', 6);
					// Aqui calculo el promedio del aporte de evaluacion
					$promedio = truncar($suma_rubricas / $contador_rubricas, 2);
					if ($tipo_aporte == 1) // Es un PARCIAL
					{
						$pdf->Cell(9.5, 5, $promedio == 0 ? " " : number_format($promedio, 2), 1, 0, 'C');
						if ($promedio > 0) $total_parciales_ingresados++;
						$contador_parciales++;
						$suma_promedios += $promedio;
						$promedios_quimestre += $promedio;
						$promedios_parciales[$contador_periodos - 1][$contador_aportes - 1] += $promedio;
					} else { // Es un EXAMEN QUIMESTRAL
						$examen_quimestral = $promedio;
						$promedios_examenes[$contador_periodos - 1] += $examen_quimestral;
						if ($examen_quimestral > 0) $total_examenes_ingresados++;
					}
				}

				// Promedio de los parciales
				$promedio_aportes = $suma_promedios / $contador_parciales;
				$promedios_aportes[$contador_periodos - 1] += $promedio_aportes;
				if ($total_parciales_ingresados == $total_parciales && $total_examenes_ingresados > 0) {
					$promedios_aportes[$contador_periodos - 1] += $promedio_aportes;
					$ponderados_aportes[$contador_periodos - 1] += 0.8 * $promedio_aportes;

					$promedio_aportes = ($promedio_aportes == 0) ? " " : number_format(truncar($promedio_aportes, 2), 2);
					$pdf->Cell(9.5, 5, $promedio_aportes, 1, 0, 'C');

					// Ponderado del promedio de los parciales
					$ponderado_aportes = ($promedio_aportes == 0) ? " " : number_format(truncar(0.8 * $promedio_aportes, 2), 2);
					$promedios_ponderados[$contador_periodos - 1] += $ponderado_aportes;
					$pdf->Cell(9.5, 5, $ponderado_aportes, 1, 0, 'C');

					// Examen Quimestral
					$pdf->Cell(9.5, 5, number_format($examen_quimestral, 2), 1, 0, 'C');

					// Ponderado del examen quimestral
					$ponderados_examenes[$contador_periodos - 1] += 0.2 * $examen_quimestral;
					$ponderado_examen = truncar(0.2 * $examen_quimestral, 2);
					$ponderados_examenes[$contador_periodos - 1] += $ponderado_examen;
					$pdf->Cell(9.5, 5, number_format($ponderado_examen, 2), 1, 0, 'C');

					// Nota Quimestral
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
					$suma_periodos += $calificacion_quimestral;
					$pdf->SetFillColor(204, 204, 204);
					$pdf->SetFont('Arial', 'B', 6);

					$pdf->Cell(9.5, 5, number_format($calificacion_quimestral, 2), 1, 0, 'C', 1);
					// Escala Cualitativa
					$promedios_quimestrales[$contador_periodos - 1] += $calificacion_quimestral;
					$pdf->Cell(9.5, 5, equiv_rendimiento($id_periodo_lectivo, $calificacion_quimestral), 1, 0, 'C', 1);

					$total_periodos_completados++;
				} else {
					// Desplegar las columnas en blanco
					$pdf->Cell(9.5, 5, " ", 1, 0, 'C'); // Columna del promedio de los parciales
					$pdf->Cell(9.5, 5, " ", 1, 0, 'C'); // Columna del ponderado del promedio de los parciales
					$pdf->Cell(9.5, 5, " ", 1, 0, 'C'); // Columna de la nota del examen quimestral
					$pdf->Cell(9.5, 5, " ", 1, 0, 'C'); // Columna del ponderado del examen quimestral
					$pdf->SetFillColor(204, 204, 204);
					$pdf->Cell(9.5, 5, " ", 1, 0, 'C', 1); // Columna de la calificación quimestral
					$pdf->Cell(9.5, 5, " ", 1, 0, 'C', 1); // Columna de la equivalencia de la calificación quimestral
				}
			} // fin while($periodo = $db->fetch_assoc($periodos_evaluacion))

			$pdf->SetFont('Arial', '', 6);
			// Promedio Anual
			if ($total_periodos_completados == $contador_periodos) {
				$promedio_anual = truncar($suma_periodos / $contador_periodos, 2);
				$pdf->Cell(9.5, 5, number_format($promedio_anual, 2), 1, 0, 'C');
				$pdf->Cell(9.5, 5, equiv_rendimiento($id_periodo_lectivo, $promedio_anual), 1, 0, 'C');

				$suma_anual += $promedio_anual;
			} else {
				$pdf->Cell(9.5, 5, " ", 1, 0, 'C'); // Columna del promedio anual
				$pdf->Cell(9.5, 5, " ", 1, 0, 'C'); // Columna de la equivalencia del promedio anual
			}

			$pdf->Ln();
		}

		// Aqui vienen los promedios generales
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->Cell(87, 5, "PROMEDIO GENERAL", 1, 0, 'C');

		$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion
													FROM sw_periodo_evaluacion 
												   WHERE pe_principal = 1
													 AND id_periodo_lectivo = $id_periodo_lectivo");
		$contador_periodos = 0;
		while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
			$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
			$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
													  FROM sw_aporte_evaluacion 
													 WHERE id_periodo_evaluacion = $id_periodo_evaluacion
													   AND ap_tipo = 1");
			$contador_aportes = 0;
			while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
				$promedio_aporte = $promedios_parciales[$contador_periodos][$contador_aportes] / $contador_asignaturas;
				$pdf->Cell(9.5, 5, $promedio_aporte == 0 ? " " : number_format($promedio_aporte, 2), 1, 0, 'C');
				$contador_aportes++;
			}

			$promedio_aportes = $promedios_aportes[$contador_periodos] / $contador_asignaturas;
			$pdf->Cell(9.5, 5, $promedio_aportes == 0 ? " " : number_format($promedio_aportes, 2), 1, 0, 'C');

			$ponderado_aportes = $ponderados_aportes[$contador_periodos] / $contador_asignaturas;
			$pdf->Cell(9.5, 5, $ponderado_aportes == 0 ? " " : number_format($ponderado_aportes, 2), 1, 0, 'C');

			$promedio_examenes = $promedios_examenes[$contador_periodos] / $contador_asignaturas;
			$pdf->Cell(9.5, 5, $promedio_examenes == 0 ? " " : number_format($promedio_examenes, 2), 1, 0, 'C');

			$ponderado_examenes = $ponderados_examenes[$contador_periodos] / $contador_asignaturas;
			$pdf->Cell(9.5, 5, $ponderado_examenes == 0 ? " " : number_format($ponderado_examenes, 2), 1, 0, 'C');

			$pdf->SetFillColor(204, 204, 204);

			$promedio_quimestrales = $promedios_quimestrales[$contador_periodos] / $contador_asignaturas;
			$pdf->Cell(9.5, 5, $promedio_quimestrales == 0 ? " " : number_format($promedio_quimestrales, 2), 1, 0, 'C', 1);
			$pdf->Cell(9.5, 5, " ", 1, 0, 'C', 1);

			$contador_periodos++;
		}

		// PROMEDIOS ANUALES
		if ($total_periodos_completados == $contador_periodos) {
			$promedio_anual = $suma_anual / $contador_asignaturas;
			$pdf->Cell(9.5, 5, number_format($promedio_anual, 2), 1, 0, 'C');
		} else {
			$pdf->Cell(9.5, 5, ' ', 1, 0, 'C');
		}

		$pdf->Cell(9.5, 5, ' ', 1, 0, 'C');

		$pdf->Ln();
		$pdf->Ln(1);

		// Aqui viene lo del comportamiento
		$pdf->Cell(42, 5, ' ', 0, 0, 'C');
		// Cabeceras de los Quimestres
		$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion
													FROM sw_periodo_evaluacion 
												   WHERE pe_principal = 1
													 AND id_periodo_lectivo = $id_periodo_lectivo");
		$contador_periodos = 0;
		while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
			$contador_periodos++;
			$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
			$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
													  FROM sw_aporte_evaluacion 
													 WHERE id_periodo_evaluacion = $id_periodo_evaluacion
													   AND ap_tipo = 1");
			$total_parciales = $db->num_rows($aportes_evaluacion);
			$width = 9.5 * ($total_parciales + 1);

			$pdf->Cell($width, 5, "QUIMESTRE $contador_periodos", 1, 0, 'C');
		}

		$pdf->Ln();
		$pdf->Cell(42, 5, ' ', 0, 0, 'C');

		//Cabeceras de los parciales...
		$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion
													FROM sw_periodo_evaluacion 
												   WHERE pe_principal = 1
													 AND id_periodo_lectivo = $id_periodo_lectivo");
		$contador_periodos = 0;
		while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
			$contador_periodos++;
			$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
			$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
													  FROM sw_aporte_evaluacion 
													 WHERE id_periodo_evaluacion = $id_periodo_evaluacion
													   AND ap_tipo = 1");
			$contador_parciales = 0;
			while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
				$contador_parciales++;
				$pdf->Cell(9.5, 5, "P$contador_parciales", 1, 0, 'C');
			}
			$pdf->Cell(9.5, 5, "Q$contador_periodos", 1, 0, 'C');
		}

		$pdf->Ln();
		$pdf->Cell(42, 5, 'COMPORTAMIENTO', 1, 0, 'C');

		if ($quien_inserta_comportamiento == 0) { // Ingresan el comportamiento los docentes
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion 
													    FROM sw_periodo_evaluacion 
												       WHERE pe_principal = 1
												         AND id_periodo_lectivo = $id_periodo_lectivo");
			// Se utilizará un bucle while para los periodos de evaluación
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

					$pdf->Cell(9.5, 5, $promedio_comp == 0 ? " " : $comp_docentes, 1, 0, 'C');
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

				$pdf->Cell(9.5, 5, $dias < 0 ? " " : $comp_quimestral, 1, 0, 'C');
			}
		} else {
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion 
														FROM sw_periodo_evaluacion 
													WHERE pe_principal = 1
														AND id_periodo_lectivo = $id_periodo_lectivo");
			// Se utilizará un bucle while para los periodos de evaluación
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

					// Aqui imprimo el comportamiento del parcial
					$pdf->Cell(9.5, 5, $calificacion, 1, 0, 'C');

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

				$pdf->Cell(9.5, 5, $dias < 0 ? " " : $comp_quimestral, 1, 0, 'C');
			}
		}

		// Aqui viene lo de la asistencia

		// Aqui viene lo de proyectos escolares (asignaturas cualitativas)
		$asignaturas = $db->consulta("SELECT a.id_asignatura, 
												 as_nombre
											FROM sw_asignatura a, 
												 sw_asignatura_curso ac, 
												 sw_paralelo p 
										   WHERE a.id_asignatura = ac.id_asignatura 
											 AND p.id_curso = ac.id_curso 
											 AND a.id_tipo_asignatura = 2
											 AND p.id_paralelo = $id_paralelo");
		$num_total_registros = $db->num_rows($asignaturas);

		if ($num_total_registros > 0) {
			$pdf->Ln();
			$pdf->Ln(1);
			$pdf->Cell(42, 5, ' ', 0, 0, 'C');

			// Cabeceras de los Quimestres
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion
														FROM sw_periodo_evaluacion 
													   WHERE pe_principal = 1
														AND id_periodo_lectivo = $id_periodo_lectivo");
			$contador_periodos = 0;
			while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
				$contador_periodos++;
				$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
														  FROM sw_aporte_evaluacion 
														 WHERE id_periodo_evaluacion = $id_periodo_evaluacion
														   AND ap_tipo = 1");
				$total_parciales = $db->num_rows($aportes_evaluacion);
				$width = 9.5 * $total_parciales;

				if ($total_parciales > 2) {
					$pdf->Cell($width, 5, "QUIMESTRE $contador_periodos", 1, 0, 'C');
				} else {
					$pdf->Cell($width, 5, "QUIM. $contador_periodos", 1, 0, 'C');
				}
			}

			$pdf->Ln();
			$pdf->Cell(42, 5, 'ASIGNATURA', 1, 0, 'C');

			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion
														FROM sw_periodo_evaluacion 
													   WHERE pe_principal = 1
														AND id_periodo_lectivo = $id_periodo_lectivo");
			$contador_periodos = 0;
			while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
				$contador_periodos++;
				$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion
														  FROM sw_aporte_evaluacion 
														 WHERE id_periodo_evaluacion = $id_periodo_evaluacion
														   AND ap_tipo = 1");
				$contador_aportes = 0;
				while ($aporte = $db->fetch_assoc($aportes_evaluacion)) {
					$contador_aportes++;
					$pdf->Cell(9.5, 5, "P$contador_aportes", 1, 0, 'C');
				}
			}

			$pdf->Ln();
			while ($asignatura = $db->fetch_assoc($asignaturas)) {
				$id_asignatura = $asignatura["id_asignatura"];
				$nombreAsignatura = $asignatura["as_nombre"];
				$pdf->Cell(42, 5, $nombreAsignatura, 1, 0, 'C');
				// Aqui obtengo las escalas cualitativas de cada parcial
				$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion 
															FROM sw_periodo_evaluacion 
												   		   WHERE pe_principal = 1
															 AND id_periodo_lectivo = $id_periodo_lectivo");
				while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
					$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
					$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion, 
														    		ap_fecha_cierre
													   		   FROM sw_periodo_evaluacion p, 
														    	    sw_aporte_evaluacion a 
													  		  WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
													    		AND ap_tipo = 1 
																AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
					while ($aporte = $db->fetch_assoc($aportes_evaluacion)) {
						$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
						$qry = $db->consulta("SELECT rc_calificacion 
												    FROM sw_rubrica_cualitativa 
												   WHERE id_estudiante = $id_estudiante 
												     AND id_paralelo = $id_paralelo 
													 AND id_asignatura = $id_asignatura 
													 AND id_aporte_evaluacion = $id_aporte_evaluacion");
						$num_total_registros = $db->num_rows($qry);
						$rubrica_estudiante = $db->fetch_assoc($qry);
						if ($num_total_registros > 0) {
							$calificacion = $rubrica_estudiante["rc_calificacion"];
						} else {
							$calificacion = " ";
						}
						$pdf->Cell(9.5, 5, $calificacion, 1, 0, 'C');
					}
				}
				$pdf->Ln();
			}
		}

		// Aqui viene lo de la escala cualitativa


		// Aqui viene lo de las recomendaciones

	}

	/*

			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(100,6,"ESCALA DE RENDIMIENTO ACADEMICO",0,0,'L');
			$pdf->Ln();
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(100,6,"ESCALA CUALITATIVA",1,0,'C');
			$pdf->Cell(30,6,"SIMBOLOGIA",1,0,'C');
			$pdf->Cell(30,6,"ESCALA CUANTITATIVA",1,0,'C');
			$pdf->Ln();
			
			// Aqui se recuperan las escalas de calificaciones de la base de datos
			
			$escala_calificacion = $db->consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
			while ($escala = $db->fetch_assoc($escala_calificacion))
			{
				$ec_cualitativa = utf8_decode($escala["ec_cualitativa"]);
				$ec_equivalencia = $escala["ec_equivalencia"];
				$ec_cuantitativa = $escala["ec_cuantitativa"];
				$pdf->Cell(100,6,$ec_cualitativa,1,0,'L');
				$pdf->Cell(30,6,$ec_equivalencia,1,0,'C');
				$pdf->Cell(30,6,$ec_cuantitativa,1,0,'C');
				$pdf->Ln();
			}
			
*/
} else {
	$pdf->Cell(100, 10, "No se han matriculado estudiantes en este paralelo...", 0, 0, 'L');
}
$pdf->Output();
