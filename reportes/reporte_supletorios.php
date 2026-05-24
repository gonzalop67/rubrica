<?php
require_once('../fpdf16/fpdf.php');
require_once('../scripts/clases/class.mysql.php');
require_once('../scripts/clases/class.asignaturas.php');
require_once('../scripts/clases/class.paralelos.php');
require_once('../scripts/clases/class.periodos_lectivos.php');
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.funciones.php');

function truncateFloat($number, $digitos)
{
	$raiz = 10;
	$multiplicador = pow($raiz, $digitos);
	$resultado = ((int)($number * $multiplicador)) / $multiplicador;
	return $resultado;
}

class PDF extends FPDF
{
	var $nombreParalelo = "";
	var $nombrePeriodoLectivo = "";
	var $nombreInstitucion = "";

	//Cabecera de página
	function Header()
	{
		$this->SetFont('Arial', 'B', 16);
		$title1 = $this->nombreInstitucion;
		$w = $this->GetStringWidth($title1);
		$this->SetX((298 - $w) / 2);
		$this->Cell($w, 10, $title1, 0, 0, 'C');
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 12);
		$title2 = "REPORTE DE SUPLETORIOS PERIODO LECTIVO " . $this->nombrePeriodoLectivo;
		$w = $this->GetStringWidth($title2);
		$this->SetX((298 - $w) / 2);
		$this->Cell($w, 9, $title2, 0, 0, 'C');
		$this->Ln(5);
		$title3 = "CURSO: " . $this->nombreParalelo;
		$w = $this->GetStringWidth($title3);
		$this->SetX((298 - $w) / 2);
		$this->Cell($w, 9, $title3, 0, 0, 'C');
	}

	//Pie de página
	function Footer()
	{
		//Posición: a 1,5 cm del final
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial', 'I', 8);
		//Número de página
		$this->Cell(0, 10, 'Page ' . $this->PageNo() . ' de {nb}', 0, 0, 'C');
	}
}

// Variables enviadas mediante POST	
// $id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_paralelo = $_SESSION['id_paralelo_tutor'];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$pdf = new PDF('L');
$pdf->nombreParalelo = $nombreParalelo;
$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
$pdf->nombreInstitucion = $nombreInstitucion;

$pdf->AliasNbPages();
$pdf->AddPage();

// Impresion de los titulos de cabecera
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(8, 6, "Nro.", 1, 0, 'C');
$pdf->Cell(70, 6, "NOMINA", 1, 0, 'C');
// Aqui imprimo las cabeceras de cada asignatura
$db = new MySQL();
$funciones = new funciones();
$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");
while ($titulo_asignatura = $db->fetch_assoc($asignaturas))
	$pdf->Cell(14, 6, $titulo_asignatura["as_abreviatura"], 1, 0, 'C');
$pdf->Ln();

// Aqui va el codigo para imprimir las calificaciones de los estudiantes
$db = new MySQL();

// Consultar el anio de inicio del periodo lectivo
$consulta = $db->consulta("SELECT pe_anio_inicio FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$registro = $db->fetch_object($consulta);
$pe_anio_inicio = $registro->pe_anio_inicio;

$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
	$contador = 0;
	while ($paralelo = $db->fetch_assoc($consulta)) {

		$id_estudiante = $paralelo["id_estudiante"];
		$retirado = $paralelo["es_retirado"];

		//Primero determino si el estudiante aprobó todas las asignaturas
		$query = $db->consulta("SELECT aprueba_todas_asignaturas($id_periodo_lectivo,$id_estudiante,$id_paralelo) AS aprueba");
		$record = $db->fetch_assoc($query);
		$aprueba_todas_asignaturas = $record["aprueba"];
		if (!$aprueba_todas_asignaturas) {

			$contador++;

			if ($contador % 25 == 0) {
				$pdf->AddPage();
				$pdf->Ln(10);
				$pdf->SetFont('Arial', '', 8);
				$pdf->Cell(8, 6, "Nro.", 1, 0, 'C');
				$pdf->Cell(70, 6, "NOMINA", 1, 0, 'C');
				// Aqui imprimo las cabeceras de cada asignatura
				$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");
				while ($titulo_asignatura = $db->fetch_assoc($asignaturas))
					$pdf->Cell(14, 6, $titulo_asignatura["as_abreviatura"], 1, 0, 'C');
				$pdf->Ln();
			}

			$pdf->Cell(8, 5, $contador, 1, 0, 'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"]) . " " . utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(70, 5, $nombre_completo, 1, 0, 'L');

			// Aqui obtengo el recordset de las asignaturas del paralelo
			$id_asignaturas = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");
			$total_asignaturas = $db->num_rows($id_asignaturas);
			if ($total_asignaturas > 0) {

				while ($asignatura = $db->fetch_assoc($id_asignaturas)) {
					// Aqui proceso los promedios de cada asignatura
					$id_asignatura = $asignatura["id_asignatura"];

					$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion, pe_ponderacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo IN (1, 7)");
					$num_total_registros = $db->num_rows($periodo_evaluacion);
					if ($num_total_registros > 0) {
						$suma_ponderados = 0;
						while ($periodo = $db->fetch_assoc($periodo_evaluacion)) {

							$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
							$pe_ponderacion = $periodo["pe_ponderacion"];

							// Aca voy a llamar a una funcion almacenada que calcula el promedio quimestral de la asignatura
							$query = $db->consulta("SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
							$calificacion = $db->fetch_assoc($query);
							$promedio_sub_periodo = $calificacion["promedio"];
							$promedio_ponderado = $promedio_sub_periodo * $pe_ponderacion;

							$suma_ponderados += $promedio_ponderado;
						} // while($periodo = $db->fetch_assoc($periodo_evaluacion))
					} // if($num_total_registros>0)

					// Calculo la suma y el promedio de los dos quimestres
					$promedio_periodos = $suma_ponderados;

					if ($promedio_periodos >= 7) {
						$pdf->Cell(14, 5, " ", 1, 0, 'C');
					} else {
						if ($pe_anio_inicio < 2023) {
							if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
								// Obtencion de la calificacion del examen supletorio

								$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);

								// Aqui desplego la calificacion del examen supletorio
								$pdf->Cell(14, 5, number_format($calificacion, 2), 1, 0, 'C');
							} else if ($promedio_periodos < 5 && $retirado == 'N') {
								$observacion = ($promedio_periodos == 0) ? "S/N" : "REMED.";
								$pdf->Cell(14, 5, $observacion, 1, 0, 'C');
							}
						} else {
							if ($promedio_periodos > 4 && $promedio_periodos < 7) {
								// Obtencion de la calificacion del examen supletorio

								$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);

								$observacion = ($calificacion == 0) ? "S/E" : $calificacion;
								$pdf->Cell(14, 5, $observacion, 1, 0, 'C');
							} else {
								$pdf->Cell(14, 5, "(" . truncateFloat($promedio_periodos, 2) . ")", 1, 0, 'C');
							}
						}
					}
				} // while ($asignatura = $db->fetch_assoc($asignaturas))
				$pdf->Ln();
			} // if($total_asignaturas>0)
		} // if (!$aprueba_todas_asignaturas)

	} // while($paralelo = $db->fetch_assoc($consulta))

} // if($num_total_registros>0)

/*$pdf->AddPage();
	$pdf->Ln();
	$pdf->SetFont('Arial','B',14);
	$title1="LISTA DE ASIGNATURAS";
	$w=$pdf->GetStringWidth($title1);
	$pdf->SetX((298-$w)/2);
	$pdf->Cell($w,10,$title1,0,0,'C');
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(20,6,"Abreviatura",1,0,'C');
	$pdf->Cell(150,6,"Nombre",1,0,'C');
	$pdf->Ln();

	// Aqui obtengo el recordset de las asignaturas del paralelo
	$asignaturas = $db->consulta("SELECT as_nombre, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
	$contador = 0;
	while ($asignatura = $db->fetch_assoc($asignaturas)) {
		$contador++;
	    $pdf->Cell(8,5,$contador,1,0,'C');
		$pdf->Cell(20,5,$asignatura["as_abreviatura"],1,0,'C');
		$pdf->Cell(150,5,$asignatura["as_nombre"],1,0,'L');
		$pdf->Ln();
	}*/
$pdf->Output();
