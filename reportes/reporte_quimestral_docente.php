<?php
require('../fpdf186/fpdf.php');
require('../scripts/clases/class.mysql.php');

$db = new MySQL();

require('../scripts/clases/class.institucion.php');
require('../scripts/clases/class.tipos_educacion.php');
require('../scripts/clases/class.periodos_lectivos.php');

function equiv_rendimiento($id_periodo_lectivo, $calificacion)
{
	global $db;
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
    var $nombreParalelo = "";
    var $nombreInstitucion = "";
    var $nomNivelEducacion = "";
    var $logoInstitucion = "";
    var $nombrePeriodoLectivo = "";
    var $nombrePeriodoEvaluacion = "";
    var $id_periodo_evaluacion = "";
    var $regimen = "";

    //Cabecera de página
    function Header()
    {
        global $db;

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
        $this->Cell(10, 10, $this->regimen, 0, 0, 'R');

        $this->Ln(3);
        $this->SetFont('Arial', '', 11);
        $title3 = "REPORTE DE " . $this->nombrePeriodoEvaluacion;
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

        $this->SetFont('Arial', 'B', 8);
        $asignatura = "ASIGNATURA: " . $this->nombreAsignatura;
        $w = $this->GetStringWidth($asignatura);
        $this->Cell($w, 8, $asignatura, 0, 0, 'L');

        // Aqui va el codigo para imprimir el nombre del curso y paralelo
        $w = $this->GetStringWidth($this->nombreParalelo);
        $this->SetX(287 - $w);
        $this->Cell($w, 8, utf8_decode($this->nombreParalelo), 0, 0, 'R');
        // $this->Ln();

        $this->Cell(92, 6, " ", 0, 0, 'C');

        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(8, 6, 'Nro.', 1, 0, 'C');
        $this->Cell(84, 6, utf8_decode('Nómina'), 1, 0, 'C');
        // Aqui imprimimos las etiquetas de las rubricas de cada aporte
        $aportes = $db->consulta("SELECT ap_abreviatura, 
										 id_tipo_aporte, 
										 ap_ponderacion 
									FROM sw_aporte_evaluacion 
	  							   WHERE id_tipo_aporte = 1  
	  								 AND id_periodo_evaluacion = $this->id_periodo_evaluacion");
        $suma_ponderacion = 0;
        while ($aporte = $db->fetch_assoc($aportes)) {
            $suma_ponderacion += $aporte["ap_ponderacion"];
            $this->Cell(12, 6, $aporte["ap_abreviatura"], 1, 0, 'C');
        }
        $this->Cell(12, 6, "PROM", 1, 0, 'C');
        $this->Cell(12, 6, ($suma_ponderacion * 100) . "%", 1, 0, 'C');
        // Aquí comprueba si existe examen de subperiodo
        $consulta = $db->consulta("SELECT ap_abreviatura, 
									      ap_ponderacion 
									 FROM sw_aporte_evaluacion 
									WHERE id_periodo_evaluacion = $this->id_periodo_evaluacion
									  AND id_tipo_aporte <> 1");
        $num_total_registros = $db->num_rows($consulta);
        if ($num_total_registros > 0) {
            while ($aporte = $db->fetch_assoc($consulta)) {
                $this->Cell(12, 6, $aporte["ap_abreviatura"], 1, 0, 'C');
                $this->Cell(12, 6, ($aporte["ap_ponderacion"] * 100) . "%", 1, 0, 'C');
            }
        }
        $this->SetFillColor(204, 204, 204);
        $periodo = $db->consulta("SELECT pe_abreviatura
									 FROM sw_periodo_evaluacion
									WHERE id_periodo_evaluacion = $this->id_periodo_evaluacion");
        $registro = $db->fetch_assoc($periodo);
        $this->Cell(12, 6, $registro["pe_abreviatura"], 1, 0, 'C', 1);
        $this->Cell(12, 6, "EQUI.", 1, 0, 'C', 1);
        $this->Ln();
    }
}

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_usuario = $_SESSION["id_usuario"];

// Obtengo el tutor del grado/curso
$consulta = $db->consulta("SELECT us_shortname FROM sw_usuario u, sw_paralelo_tutor p WHERE u.id_usuario = p.id_usuario AND p.id_paralelo = $id_paralelo AND p.id_periodo_lectivo = $id_periodo_lectivo");
$resultado = $db->fetch_assoc($consulta);
$nombreTutor = utf8_decode($resultado["us_shortname"]);

// Obtengo el nombre del docente
$consulta = $db->consulta("SELECT us_shortname FROM sw_usuario WHERE id_usuario = $id_usuario");
$resultado = $db->fetch_assoc($consulta);
$nombreDocente = utf8_decode($resultado["us_shortname"]);

// $paralelo = new paralelos();
// $nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

$consulta = $db->consulta("SELECT es_figura, 
								cu_nombre, 
								pa_nombre, 
								jo_nombre 
						FROM sw_especialidad es, 
								sw_curso cu, 
								sw_paralelo pa, 
								sw_jornada jo
						WHERE pa.id_curso = cu.id_curso 
							AND cu.id_especialidad = es.id_especialidad 
							AND jo.id_jornada = pa.id_jornada 
							AND pa.id_paralelo = $id_paralelo");
$paralelo = $db->fetch_object($consulta);
$nombreParalelo = $paralelo->cu_nombre . " " . $paralelo->pa_nombre . " - " . $paralelo->es_figura;

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

// Obtener el nombre del periodo de evaluación
$consulta = $db->consulta("SELECT pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
$resultado = $db->fetch_object($consulta);
$nombrePeriodoEvaluacion = $resultado->pe_nombre;

$institucion = new institucion();
$nombreInstitucion = utf8_decode($institucion->obtenerNombreInstitucion());
$logoInstitucion = $institucion->obtenerLogoInstitucion();
$nombreRector = utf8_decode($institucion->obtenerNombreRector());
$regimen = utf8_decode($institucion->obtenerRegimenInstitucion());

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
$pdf->nombreParalelo = $nombreParalelo;
$pdf->id_periodo_evaluacion = $id_periodo_evaluacion;
$pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;
$pdf->regimen = $regimen;

$fecha_actual = date_create(date('Y-m-d')); //fecha actual

// Obtengo el tutor del grado/curso
$consulta = $db->consulta("SELECT us_shortname FROM sw_usuario u, sw_paralelo_tutor p WHERE u.id_usuario = p.id_usuario AND p.id_paralelo = $id_paralelo AND p.id_periodo_lectivo = $id_periodo_lectivo");
$resultado = $db->fetch_assoc($consulta);
$nombreTutor = utf8_decode($resultado["us_shortname"]);

// Obtengo quien inserta el comportamiento (0 : Docentes, 1 : Tutor)
$consulta = $db->consulta("SELECT quien_inserta_comp FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
$resultado = $db->fetch_assoc($consulta);
$quien_inserta_comportamiento = $resultado["quien_inserta_comp"];

// Aqui va el codigo para imprimir el nombre de la asignatura
$consulta = $db->consulta("SELECT as_nombre FROM sw_asignatura WHERE id_asignatura = $id_asignatura");
$resultado = $db->fetch_assoc($consulta);
$pdf->nombreAsignatura = utf8_decode($resultado["as_nombre"]);

// Aqui va el codigo para imprimir las calificaciones de los estudiantes
$consulta = $db->consulta("SELECT e.id_estudiante, 
								  di.id_asignatura, 
								  e.es_apellidos, 
								  e.es_nombres, 
								  id_tipo_asignatura 
							 FROM sw_distributivo di, 
								  sw_estudiante_periodo_lectivo ep, 
								  sw_estudiante e, 
								  sw_asignatura a,
								  sw_paralelo p
							WHERE di.id_paralelo = ep.id_paralelo 
							  AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
							  AND ep.id_estudiante = e.id_estudiante 
							  AND di.id_asignatura = a.id_asignatura 
							  AND di.id_paralelo = p.id_paralelo 
							  AND di.id_paralelo = $id_paralelo
	                          AND di.id_asignatura = $id_asignatura
	                          AND es_retirado <> 'S'
							  AND activo = 1 
							ORDER BY es_apellidos, es_nombres ASC");
$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
    $contador = 0;
    $pdf->AddPage();

    while ($paralelo = $db->fetch_assoc($consulta)) {
        $contador++;

        $id_estudiante = $paralelo["id_estudiante"];

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(8, 6, $contador, 1, 0, 'C');
        $nombre_completo = utf8_decode($paralelo["es_apellidos"]) . " " . utf8_decode($paralelo["es_nombres"]);
        $pdf->Cell(84, 6, $nombre_completo, 1, 0, 'L');

        // Aqui va el codigo para imprimir las calificaciones de los estudiantes
        $aportes = $db->consulta("SELECT a.id_aporte_evaluacion, 
										 ap_ponderacion,
                                         id_tipo_aporte
									FROM sw_periodo_evaluacion p, 
										 sw_aporte_evaluacion a 
								   WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
									 AND p.id_periodo_evaluacion = $id_periodo_evaluacion
								     AND id_tipo_aporte = 1");
        $num_total_registros = $db->num_rows($aportes);

        //
        if ($num_total_registros > 0) {
            // Aqui calculo los promedios y desplegar en la tabla
            $suma_aportes = 0;
            $contador_aportes = 0;
            $suma_promedios = 0;
            $suma_ponderados = 0;

            while ($aporte = $db->fetch_assoc($aportes)) {
                $contador_aportes++;
                $tipo_aporte = $aporte["id_tipo_aporte"];
                $ponderacion = $aporte["ap_ponderacion"];
                $id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];

                $qry = "SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
                $resultado = $db->consulta($qry);
                $registro = $db->fetch_assoc($resultado);
                $promedio = $registro["promedio"];

                $promedio_ponderado = $promedio * $ponderacion;

                $pdf->Cell(12, 6, $promedio == 0 ? " " : substr($promedio, 0, strpos($promedio, '.') + 3), 1, 0, 'C');

                $suma_promedios += $promedio;
                $suma_ponderados += $promedio_ponderado;
            }

            // Aqui debo calcular el ponderado de los promedios parciales
            $promedio_aportes = $suma_promedios / $contador_aportes;
            $ponderado_aportes = $suma_ponderados;

            $pdf->Cell(12, 6, $promedio_aportes == 0 ? " " : substr($promedio_aportes, 0, strpos($promedio_aportes, '.') + 3), 1, 0, 'C');
            $pdf->Cell(12, 6, $ponderado_aportes == 0 ? " " : substr($ponderado_aportes, 0, strpos($ponderado_aportes, '.') + 3), 1, 0, 'C');

            // Aquí calcula los aportes de la evaluación sumativa
            $aportes = $db->consulta("SELECT a.id_aporte_evaluacion, 
										     ap_ponderacion,
                                             id_tipo_aporte
									    FROM sw_periodo_evaluacion p, 
										     sw_aporte_evaluacion a 
								       WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
									     AND p.id_periodo_evaluacion = $id_periodo_evaluacion
								         AND id_tipo_aporte <> 1");
            $num_total_registros = $db->num_rows($aportes);

            while ($aporte = $db->fetch_assoc($aportes)) {
                $ponderacion = $aporte["ap_ponderacion"];
                $id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];

                $qry = "SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
                $resultado = $db->consulta($qry);
                $registro = $db->fetch_assoc($resultado);
                $promedio = $registro["promedio"];

                $promedio_ponderado = $promedio * $ponderacion;

                $pdf->Cell(12, 6, $promedio == 0 ? " " : substr($promedio, 0, strpos($promedio, '.') + 3), 1, 0, 'C');
                $pdf->Cell(12, 6, $promedio_ponderado == 0 ? " " : substr($promedio_ponderado, 0, strpos($promedio_ponderado, '.') + 3), 1, 0, 'C');

                $suma_ponderados += $promedio_ponderado;
            }
            // Despliega la suma de los ponderados de los aportes que corresponde a la nota del sub periodo
            $pdf->Cell(12, 6, $suma_ponderados == 0 ? " " : substr($suma_ponderados, 0, strpos($suma_ponderados, '.') + 3), 1, 0, 'C');
            // Aqui va el codigo para imprimir la equivalencia de la calificacion
            $equivalencia = equiv_rendimiento($id_periodo_lectivo, $suma_ponderados);
            $pdf->Cell(12, 6, $equivalencia, 1, 0, 'C');
        }

        $pdf->Ln();

        if ($contador % 21 == 0) $pdf->AddPage();
    }
}

$pdf->Ln();
$pdf->SetFont('Arial', '', 8);
//Aqui van las firmas de docente y tutor
$pdf->Cell(0, 10, '___________________________', 0, 0, 'L');
$titulo1 = '___________________________';
$w = $pdf->GetStringWidth($titulo1);
$pdf->SetX(200 - $w);
$pdf->Cell($w, 8, $titulo1, 0, 0, 'R');
$pdf->Ln(5);
$pdf->Cell(0, 10, '      ' . $nombreDocente, 0, 0, 'L');
$titulo2 = '            ' . $nombreTutor;
$w = $pdf->GetStringWidth($titulo2);
$pdf->SetX(190 - $w);
$pdf->Cell($w, 8, $titulo2, 0, 0, 'R');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 10, '               DOCENTE', 0, 0, 'L');
$titulo3 = '            TUTOR(A)';
$w = $pdf->GetStringWidth($titulo3);
$pdf->SetX(185 - $w);
$pdf->Cell($w, 8, $titulo3, 0, 0, 'R');

$pdf->Output();
