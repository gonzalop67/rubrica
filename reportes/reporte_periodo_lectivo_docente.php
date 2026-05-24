<?php
require('../fpdf186/fpdf.php');
require('../scripts/clases/class.mysql.php');
require('../scripts/clases/class.institucion.php');
require('../scripts/clases/class.tipos_educacion.php');
// require('../scripts/clases/class.periodos_lectivos.php');

$db = new MySQL();

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_usuario = $_SESSION["id_usuario"];

class PDF extends FPDF
{

    var $regimen = "";
    var $nombreParalelo = "";
    var $logoInstitucion = "";
    var $nombreAsignatura = "";
    var $nombreInstitucion = "";
    var $nomNivelEducacion = "";
    var $nombrePeriodoLectivo = "";

    //Cabecera de página
    function Header()
    {
        global $db, $id_paralelo, $id_periodo_lectivo;

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
        $title3 = "REPORTE DE FINAL DE PERIODO LECTIVO";
        $w = $this->GetStringWidth($title3);
        $this->SetX((297 - $w) / 2);
        $this->Cell($w, 10, $title3, 0, 0, 'C');

        $this->Ln(3);
        $this->SetFont('Arial', 'B', 7);
        $title4 = mb_convert_encoding("PERÍODO LECTIVO: " . $this->nombrePeriodoLectivo, 'ISO-8859-1', 'UTF-8');
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
        $this->Cell($w, 8, mb_convert_encoding($this->nombreParalelo, 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');

        $this->Cell(92, 6, " ", 0, 0, 'C');

        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(8, 6, 'Nro.', 1, 0, 'C');
        $this->Cell(84, 6, mb_convert_encoding('Nómina', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

        // Titulos de los periodos de evaluacion
        $qry = "SELECT pe.id_periodo_evaluacion, 
                       pe_abreviatura 
                  FROM sw_periodo_evaluacion pe,
                       sw_periodo_evaluacion_curso pc, 
                       sw_tipo_periodo tp 
                 WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
                   AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
                   AND tp.id_tipo_periodo = pe.id_tipo_periodo 
                   AND pe.id_tipo_periodo IN (1, 7, 8) 
                   AND pc.id_periodo_lectivo = $id_periodo_lectivo
                   AND pc.id_curso = (SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo) 
                 ORDER BY pc_orden ASC";

        $consulta = $db->consulta($qry);
        $num_total_registros = $db->num_rows($consulta);
        if ($num_total_registros > 0) {
            while ($periodo = $db->fetch_assoc($consulta)) {
                $abreviatura = $periodo["pe_abreviatura"];
                $this->Cell(13, 6, $abreviatura, 1, 0, 'C');
            }
        }

        $this->Cell(13, 6, "PROM.", 1, 0, 'C');
        $this->Cell(26, 6, "OBSERVACION", 1, 0, 'C');
        $this->Cell(13, 6, "SUPL.", 1, 0, 'C');
        $this->Cell(26, 6, "OBS. FINAL", 1, 0, 'C');

        $this->Ln();
    }
}

// Obtengo el nombre del docente
$consulta = $db->consulta("SELECT us_shortname FROM sw_usuario WHERE id_usuario = $id_usuario");
$resultado = $db->fetch_assoc($consulta);
$nombreDocente = mb_convert_encoding($resultado["us_shortname"], 'ISO-8859-1', 'UTF-8');

// Obtengo el tutor del grado/curso
$consulta = $db->consulta("SELECT us_shortname FROM sw_usuario u, sw_paralelo_tutor p WHERE u.id_usuario = p.id_usuario AND p.id_paralelo = $id_paralelo AND p.id_periodo_lectivo = $id_periodo_lectivo");
$resultado = $db->fetch_assoc($consulta);
$nombreTutor = mb_convert_encoding($resultado["us_shortname"], 'ISO-8859-1', 'UTF-8');

$institucion = new institucion();
$logoInstitucion = $institucion->obtenerLogoInstitucion();
$nombreInstitucion = mb_convert_encoding($institucion->obtenerNombreInstitucion(), 'ISO-8859-1', 'UTF-8');
$regimen = mb_convert_encoding($institucion->obtenerRegimenInstitucion(), 'ISO-8859-1', 'UTF-8');

//Obtengo el Nivel de Educación
$nivelEducacion = new tipos_educacion();
$nomNivelEducacion = mb_convert_encoding($nivelEducacion->obtenerNombreTipoEducacion($id_paralelo), 'ISO-8859-1', 'UTF-8');

// Obtener nota mínima de aprobación y nota mínima para no perder el periodo lectivo
$consulta = $db->consulta("SELECT pe_nota_aprobacion, pe_nota_minima FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$registro = $db->fetch_object($consulta);
$nota_minima_aprobacion = $registro->pe_nota_aprobacion;
$nota_minima_no_perder = $registro->pe_nota_minima;

// Obtener rango de notas para dar examen supletorio
$consulta = $db->consulta("SELECT rango_desde, rango_hasta FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo");
$registro = $db->fetch_object($consulta);
$rango_desde = $registro->rango_desde;
$rango_hasta = $registro->rango_hasta;

$consulta = $db->consulta("SELECT es_intensivo FROM sw_curso cu, sw_paralelo pa WHERE cu.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo");
$registro = $db->fetch_object($consulta);
$es_intensivo = $registro->es_intensivo;

$nombrePeriodoLectivo = "";

$meses = ['0', 'Ene.', 'Feb.', 'Mar.', 'Abr.', 'May.', 'Jun.', 'Jul.', 'Ago.', 'Sep.', 'Oct.', 'Nov.', 'Dic.'];

$qry = "SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";

$consulta = $db->consulta($qry);
$periodo_lectivo = $db->fetch_object($consulta);

$fecha_inicial = explode('-', $periodo_lectivo->pe_fecha_inicio);
$fecha_final = explode('-', $periodo_lectivo->pe_fecha_fin);

if ($es_intensivo) {
    if ($periodo_lectivo->pe_prefijo) {
        $nombrePeriodoLectivo = $periodo_lectivo->pe_prefijo . ": " . $meses[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses[(int)$fecha_final[1]] . " " . $fecha_final[0];
    } else {
        $nombrePeriodoLectivo = $meses[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses[(int)$fecha_final[1]] . " " . $fecha_final[0];
    }
} else {
    $nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
}

//Creación del objeto de la clase heredada
$pdf = new PDF('L');
$pdf->SetTopMargin(4);

$pdf->regimen = $regimen;
$pdf->logoInstitucion = $logoInstitucion;
$pdf->nombreInstitucion = $nombreInstitucion;
$pdf->nomNivelEducacion = $nomNivelEducacion;
$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

// Aqui va el codigo para obtener el nombre de la asignatura
$consulta = $db->consulta("SELECT as_nombre FROM sw_asignatura WHERE id_asignatura = $id_asignatura");
$resultado = $db->fetch_assoc($consulta);

$pdf->nombreAsignatura = mb_convert_encoding($resultado["as_nombre"], 'ISO-8859-1', 'UTF-8');

// Aquí va el código para obtener el nombre del curso y paralelo
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

$pdf->nombreParalelo = $nombreParalelo;

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

        $nombre_completo = mb_convert_encoding($paralelo["es_apellidos"] . " " . $paralelo["es_nombres"], 'ISO-8859-1', 'UTF-8');
        $pdf->Cell(84, 6, $nombre_completo, 1, 0, 'L');

        // Titulos de los periodos de evaluacion
        $qry = "SELECT pe.id_periodo_evaluacion, 
                       pe_abreviatura,  
                       pc.pe_ponderacion 
                  FROM sw_periodo_evaluacion pe,
                       sw_periodo_evaluacion_curso pc, 
                       sw_tipo_periodo tp 
                 WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
                   AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
                   AND tp.id_tipo_periodo = pe.id_tipo_periodo 
                   AND pe.id_tipo_periodo IN (1, 7, 8) 
                   AND pc.id_periodo_lectivo = $id_periodo_lectivo
                   AND pc.id_curso = (SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo) 
                 ORDER BY pc_orden ASC";

        $periodos_evaluacion = $db->consulta($qry);
        $num_total_registros = $db->num_rows($periodos_evaluacion);

        if ($num_total_registros > 0) {
            // Aqui calculo los promedios y desplegar en la tabla
            $suma_subperiodos = 0;
            while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
                $id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

                $qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion";
                $query = $db->consulta($qry);

                $record = $db->fetch_object($query);
                $promedio_sub_periodo = $record->calificacion;

                $suma_subperiodos += $promedio_sub_periodo;

                $nota_sub_periodo = $promedio_sub_periodo == 0 ? "" : substr($promedio_sub_periodo, 0, strpos($promedio_sub_periodo, '.') + 3);

                $pdf->Cell(13, 6, $nota_sub_periodo, 1, 0, 'C');
            }

            // Promedio Final del Periodo Lectivo
            $puntaje_final = $suma_subperiodos / $num_total_registros;

            $nota_final = $puntaje_final == 0 ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

            $pdf->Cell(13, 6, $nota_final, 1, 0, 'C');

            if ($puntaje_final >= $nota_minima_aprobacion) {
                $pdf->SetTextColor(0, 80, 0);
                $pdf->Cell(26, 6, "APRUEBA", 1, 0, 'C');
            } else if ($puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
                $pdf->SetTextColor(255, 120, 0);
                $pdf->Cell(26, 6, "SUPLETORIO", 1, 0, 'C');
            } else if ($puntaje_final >= $nota_minima_no_perder) {
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell(26, 6, "NO APRUEBA", 1, 0, 'C');
            } else {
                $pdf->SetTextColor(0, 0, 255);
                $pdf->Cell(26, 6, "DESERTOR", 1, 0, 'C');
            }

            $pdf->SetTextColor(0, 0, 0);

            // Obtener la calificacion del examen supletorio
            $qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio";

            $resultado = $db->consulta($qry);
            $calificacion = $db->fetch_assoc($resultado);
            $supletorio = $calificacion["supletorio"];

            $supletorio = $supletorio == 0 ? "" : substr($supletorio, 0, strpos($supletorio, '.') + 3);

            $pdf->Cell(13, 6, $supletorio, 1, 0, 'C');

            // Obtener el id_aporte_evaluacion del examen supletorio
            $qry = "SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND id_tipo_aporte = 3 AND p.id_periodo_lectivo = $id_periodo_lectivo";

            $resultado = $db->consulta($qry);
            $result = $db->fetch_object($resultado);
            $id_aporte_evaluacion = $result->id_aporte_evaluacion;

            // Obtener el estado de cierre del aporte de evaluación 
            $qry = "SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo";
            $resultado = $db->consulta($qry);
            $result = $db->fetch_object($resultado);
            $estado = $result->ap_estado;

            if ($puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
                if ($estado == 'A') {
                    if ($supletorio == "") {
                        $pdf->SetTextColor(255, 120, 0);
                        $pdf->Cell(26, 6, "SIN EXAMEN", 1, 0, 'C');
                    } else if ($supletorio >= $nota_minima_aprobacion) {
                        $pdf->SetTextColor(0, 80, 0);
                        $pdf->Cell(26, 6, "APRUEBA", 1, 0, 'C');
                    } else {
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->Cell(26, 6, "NO APRUEBA", 1, 0, 'C');
                    }
                } else {
                    if ($supletorio >= $nota_minima_aprobacion) {
                        $pdf->SetTextColor(0, 80, 0);
                        $pdf->Cell(26, 6, "APRUEBA", 1, 0, 'C');
                    } else {
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->Cell(26, 6, "NO APRUEBA", 1, 0, 'C');
                    }
                }
                $pdf->SetTextColor(0, 0, 0);        
            } else {
                $pdf->Cell(26, 6, "", 1, 0, 'C');
            }
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
