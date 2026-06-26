<?php
require('../fpdf186/fpdf.php');
require('../scripts/clases/class.mysql.php');
require('../scripts/clases/class.institucion.php');

//función php para establecer el huso horario que se va a utilizar
date_default_timezone_set('America/Guayaquil');

$meses = array(0, "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
$dias = array("domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado");
$mes = $meses[date("n")];
$dia = $dias[date("w")];

function fecha_actual()
{
     global $mes;
     $fecha_string = date("j") . " de $mes de " . date("Y");
     return $fecha_string;
}

function truncateFloat($number, $digitos)
{
     $raiz = 10;
     $multiplicador = pow($raiz, $digitos);
     $resultado = ((int)($number * $multiplicador)) / $multiplicador;
     return $resultado;
}

function equiv_rendimiento($id_periodo_lectivo, $calificacion)
{
     $db = new MySQL();
     // Determinacion de la letra de equivalencia que corresponde a la calificacion dada
     $escala_calificacion = $db->consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
     $equivalencia = "";
     while ($escala = $db->fetch_object($escala_calificacion)) {
          $nota_minima = $escala->ec_nota_minima;
          $nota_maxima = $escala->ec_nota_maxima;
          if ($calificacion >= $nota_minima && $calificacion <= $nota_maxima) {
               $equivalencia = $escala->ec_cualitativa;
               break;
          }
     }
     return $equivalencia;
}

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_estudiante = $_POST["id_estudiante"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

//********************************************************************************** */

//Creación del objeto FPDF
$pdf = new FPDF();
$pdf->AddPage();

//Logo Izquierda
$pdf->Image('logo-presidencia-noboa.jpg', 10, 5, 33);

$pdf->SetFont('Arial', 'B', 6);
$institucion = new institucion();
$AMIEInstitucion = $institucion->obtenerAMIEInstitucion();
$nombreInstitucion = mb_convert_encoding($institucion->obtenerNombreInstitucion(), 'ISO-8859-1', 'UTF-8');
$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();
$ciudadInstitucion = $institucion->obtenerCiudadInstitucion();
$regimenInstitucion = $institucion->obtenerRegimenInstitucion();

$db = new mysql();
// Comprobar si es intensivo
$query = $db->consulta("SELECT es_intensivo FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
$registro = $db->fetch_object($query);
$es_intensivo = $registro->es_intensivo;

//Aqui obtengo la oferta educativa
$qry = $db->consulta("SELECT slug FROM sw_ofertas_educativas oe, sw_periodo_lectivo pl WHERE oe.id = pl.oferta_educativa_id AND pl.id_periodo_lectivo = $id_periodo_lectivo");
$slug = $db->fetch_object($qry)->slug;

//Aqui obtengo el año lectivo
$qry = $db->consulta("SELECT pe_anio_inicio, 
                             pe_anio_fin, 
                             pe_fecha_inicio,
                             pe_fecha_fin
                        FROM sw_periodo_lectivo pl, 
                             sw_estudiante_periodo_lectivo ep 
                       WHERE pl.id_periodo_lectivo = ep.id_periodo_lectivo 
                         AND ep.id_estudiante = $id_estudiante 
                         AND ep.id_periodo_lectivo = $id_periodo_lectivo");
$res = $db->fetch_object($qry);
$anio_lectivo = $res->pe_anio_inicio . " - " . $res->pe_anio_fin;
$anio_inicial = $res->pe_anio_inicio;
$anio_final = $res->pe_anio_fin;
$fecha_inicio = $res->pe_fecha_inicio;
$fecha_final = $res->pe_fecha_fin;

$fecha_inicio = explode("-", $fecha_inicio);
$mes_abrev_ini = (int)$fecha_inicio[1];
$mes_inicial = $meses_abrev[$mes_abrev_ini] . " " . $fecha_inicio[0];

$fecha_final = explode("-", $fecha_final);
$mes_abrev_fin = (int)$fecha_final[1];
$mes_final = $meses_abrev[$mes_abrev_fin] . " " . $fecha_final[0];

$periodo_educativo = $mes_inicial . " - " . $mes_final;

//Obtener la oferta educativa
$qry = $db->consulta("SELECT es_figura,
                             es_nombre 
                        FROM sw_tipo_educacion t, 
                             sw_especialidad e, 
                             sw_curso c, 
                             sw_paralelo p
                       WHERE t.id_tipo_educacion = e.id_tipo_educacion
                         AND e.id_especialidad = c.id_especialidad
                         AND c.id_curso = p.id_curso 
                         AND p.id_paralelo = $id_paralelo");
$res = $db->fetch_object($qry);
$nivelEducacion = mb_convert_encoding($res->es_figura, 'ISO-8859-1', 'UTF-8');

if ($es_intensivo == 1) {
     $pdf->SetXY(50, 16);
     $pdf->Cell(20, 6, "CODIGO AMIE:", 0, 0, 'C');

     $pdf->SetXY(80, 16);
     $pdf->Cell(20, 6, mb_convert_encoding("AÑO LECTIVO", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');

     $pdf->SetXY(120, 16);
     $pdf->Cell(20, 6, "PERIODO EDUCATIVO:", 0, 0, 'C');

     $pdf->SetXY(170, 16);
     $pdf->Cell(20, 6, "OFERTA EDUCATIVA:", 0, 0, 'C');

     $pdf->SetFont('Times', '', 6);
     $pdf->SetXY(50, 19);
     $pdf->Cell(20, 6, $AMIEInstitucion, 0, 0, 'C');

     $pdf->SetXY(80, 19);
     $pdf->Cell(20, 6, $anio_lectivo, 0, 0, 'C');

     $pdf->SetXY(120, 19);
     $pdf->Cell(20, 6, $periodo_educativo, 0, 0, 'C');

     $pdf->SetXY(170, 19);
     $pdf->Cell(20, 6, $nivelEducacion, 0, 0, 'C');
} else {
     $pdf->SetXY(50, 11);
     $pdf->Cell(20, 6, "CODIGO AMIE:", 0, 0, 'C');

     $pdf->SetXY(80, 11);
     $pdf->Cell(20, 6, mb_convert_encoding("AÑO LECTIVO", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');

     $pdf->SetXY(120, 11);
     $pdf->Cell(20, 6, "PERIODO EDUCATIVO:", 0, 0, 'C');

     $pdf->SetXY(170, 11);
     $pdf->Cell(20, 6, "OFERTA EDUCATIVA:", 0, 0, 'C');

     $pdf->SetFont('Times', '', 6);
     $pdf->SetXY(50, 14);
     $pdf->Cell(20, 6, $AMIEInstitucion, 0, 0, 'C');

     $pdf->SetXY(80, 14);
     $pdf->Cell(20, 6, $anio_lectivo, 0, 0, 'C');

     $pdf->SetXY(120, 14);
     $pdf->Cell(20, 6, $periodo_educativo, 0, 0, 'C');

     $pdf->SetXY(170, 14);
     $pdf->Cell(20, 6, $nivelEducacion, 0, 0, 'C');
}

$pdf->SetFont('Helvetica', 'B', 14);
$title = "CERTIFICADO DE PROMOCIÓN";
$w = $pdf->GetStringWidth($title);
$pdf->SetXY((210 - $w) / 2, 25);
$pdf->Cell($w, 10, mb_convert_encoding($title, 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');

if ($slug == 'epja-tecnica-intensiva') {
     $pdf->Ln(5);
} else {
     $pdf->Ln();
}

$pdf->SetFont('Helvetica', '', 10);
$text = mb_convert_encoding("El Rector(a)/Director(a) de la Institución Educativa:", 'ISO-8859-1', 'UTF-8');
$pdf->Cell(200, 10, $text, 0, 0, 'L');

if ($slug == 'epja-tecnica-intensiva') {
     $pdf->Ln(5);
} else {
     $pdf->Ln(9);
}

$pdf->SetFont('Times', 'B', 14);
$title = $nombreInstitucion;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(9);

if ($es_intensivo == 1) {
     // $pdf->SetFont('Arial', '', 9.5);
     // $text = mb_convert_encoding("De conformidad con lo prescrito en el Art. 197 del Reglamento General a la Ley Orgánica de Educación Intercultural", 'ISO-8859-1', 'UTF-8');
     // $pdf->Cell(200, 10, $text, 0, 0, 'L');
     // $pdf->Ln(5);

     // $text = mb_convert_encoding("y demás normativas vigentes, certifica que el/la estudiante:", 'ISO-8859-1', 'UTF-8');
     // $pdf->Cell(200, 10, $text, 0, 0, 'L');
     // $pdf->Ln(9);

     // } else if ($anio_inicial >= 2018) {
     $pdf->SetFont('Arial', '', 9.5);
     $text = mb_convert_encoding("De conformidad con lo prescrito en el Art. 197 del Reglamento General a la Ley Orgánica de Educación Intercultural, Acuerdo ", 'ISO-8859-1', 'UTF-8');
     $pdf->Cell(200, 10, $text, 0, 0, 'L');
     $pdf->Ln(5);

     $text = mb_convert_encoding("Ministerial Nro. MINEDUC-MINEDUC-2017-00040-A de 11 -05-2017 codificado a septiembre-2017, y demás normas vigentes, ", 'ISO-8859-1', 'UTF-8');
     $pdf->Cell(200, 10, $text, 0, 0, 'L');
     $pdf->Ln(5);

     $text = mb_convert_encoding("certifica que el/la estudiante:", 'ISO-8859-1', 'UTF-8');
     $pdf->Cell(200, 10, $text, 0, 0, 'L');
     
     $pdf->Ln(5);
     
} else {
     $pdf->SetFont('Arial', '', 9.5);
     $text = mb_convert_encoding("De conformidad con lo prescrito en el Art. 197 del Reglamento General a la Ley Orgánica de Educación Intercultural", 'ISO-8859-1', 'UTF-8');
     $pdf->Cell(0, 10, $text, 0, 0, 'L');
     $pdf->Ln(5);

     $text = mb_convert_encoding("y demás normas vigentes, certifica que el/la estudiante:", 'ISO-8859-1', 'UTF-8');
     $pdf->Cell(0, 10, $text, 0, 0, 'L');
     
     $pdf->Ln(5);
}

//Aqui obtengo el nombre completo del estudiante
$db = new mysql();
$qry = $db->consulta("SELECT es_apellidos, 
                             es_nombres, 
                             nro_matricula, 
                             es_cedula,
                             id_def_genero
                        FROM sw_estudiante e, 
                             sw_estudiante_periodo_lectivo ep 
                       WHERE e.id_estudiante = ep.id_estudiante 
                         AND ep.id_estudiante = $id_estudiante 
                         AND ep.id_periodo_lectivo = $id_periodo_lectivo");
$res = $db->fetch_object($qry);
$nombreEstudiante = $res->es_apellidos . " " . $res->es_nombres;
$cedula = $res->es_cedula;
$id_def_genero = $res->id_def_genero;
$query = $db->consulta("SELECT dg_abreviatura FROM sw_def_genero WHERE id_def_genero = $id_def_genero");
$registro = $db->fetch_object($query);
$terminacion = ($registro->dg_abreviatura == 'M') ? 'o' : 'a';

$pdf->SetFont('Arial', 'BI', 12);
$w = $pdf->GetStringWidth($nombreEstudiante);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell(150, 10, mb_convert_encoding($nombreEstudiante, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->Ln(7);

//Aqui obtengo la jornada
$qry = $db->consulta("SELECT es_figura, 
                             cu_nombre,
                             pa_nombre,
                             c.id_curso
                        FROM sw_especialidad e, 
                             sw_curso c, 
                             sw_paralelo p
                       WHERE e.id_especialidad = c.id_especialidad
                         AND c.id_curso = p.id_curso 
                         AND id_paralelo = $id_paralelo");
$res = $db->fetch_object($qry);
$especialidad = mb_convert_encoding($res->es_figura, 'ISO-8859-1', 'UTF-8');
$curso = $res->cu_nombre;
$paralelo = $res->pa_nombre;
$id_curso = $res->id_curso;

$pdf->SetFont('Arial', '', 9.5);
$pdf->Cell(5, 10, "del ", 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 9.5);
$w = $pdf->GetStringWidth($curso);
$pdf->Cell($w, 10, mb_convert_encoding($curso, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

$pdf->Cell(6, 10, " DE ", 0, 0, 'L');

$w = $pdf->GetStringWidth($especialidad);
$pdf->Cell($w + 1, 10, $especialidad, 0, 0, 'L');

// $text = ' "' . $paralelo . '",';
// $w = $pdf->GetStringWidth($text);
// $pdf->Cell($w + 1, 10, $text, 0, 0, 'L');

$pdf->SetFont('Arial', '', 9.5);
$text = 'con documento de identidad Nro. ';
$w = $pdf->GetStringWidth($text);
$pdf->Cell($w + 1, 10, $text, 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 9.5);
$w = $pdf->GetStringWidth($cedula);
$pdf->Cell($w + 1, 10, $cedula, 0, 0, 'L');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 9.5);
$text = "obtuvo la siguiente calificación durante el presente periodo educativo $anio_lectivo:";
$w = $pdf->GetStringWidth($text);
$pdf->Cell($w, 10, mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->Ln(7);

$contador_no_aprueba = 0;

if ($es_intensivo == 1) {
     $pdf->SetFont('Arial', 'B', 8);
     $pdf->SetFillColor(200, 200, 200);
     $pdf->Cell(50, 5, "ASIGNATURAS", 1, 0, 'C', 1);
     $pdf->Cell(50, 5, "CALIFICACIONES CUANTITATIVAS", 1, 0, 'C', 1);
     $pdf->Cell(80, 5, "CALIFICACIONES CUALITATIVAS", 1, 0, 'C', 1);
     $pdf->Ln();

     $asignaturas = $db->consulta("SELECT a.id_asignatura, 
                                     a.id_tipo_asignatura,
                                     as_nombre,
                                     ar_nombre 
                                FROM sw_asignatura_curso ac, 
                                     sw_paralelo p, 
                                     sw_asignatura a,
                                     sw_area ar
                               WHERE ac.id_curso = p.id_curso 
                                 AND ac.id_asignatura = a.id_asignatura
                                 AND ar.id_area = a.id_area
                                 AND a.id_tipo_asignatura = 1 
                                 AND id_paralelo = $id_paralelo 
                               ORDER BY ac_orden");
     $numero_asignaturas = $db->num_rows($asignaturas);
     $suma_promedios = 0;
     $contador_no_aprueba = 0;
     $contador = 0; // Contador de asignaturas cuantitativas
     while ($asignatura = $db->fetch_object($asignaturas)) {
          $id_asignatura = $asignatura->id_asignatura;
          $id_tipo_asignatura = $asignatura->id_tipo_asignatura;
          $nombreAsignatura = $asignatura->as_nombre;
          $nombreAsignatura = substr($nombreAsignatura, 0, 31);
          $pdf->SetFont('Arial', '', 7);
          $pdf->Cell(50, 6, mb_convert_encoding($nombreAsignatura, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');

          $query = $db->consulta("SELECT calcular_promedio_final($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");
          $registro = $db->fetch_object($query);
          $promedio_final = $registro->promedio_final;
          if ($promedio_final < 7) $contador_no_aprueba++;
          $suma_promedios += $promedio_final;
          $cualitativa = equiv_rendimiento($id_periodo_lectivo, truncateFloat($promedio_final, 2));
          $promedio_final_truncado = number_format(truncateFloat($promedio_final, 2), 2);
          $promedio_final_truncado = str_replace(".", ",", $promedio_final_truncado);
          $pdf->Cell(50, 6, $promedio_final_truncado, 1, 0, 'C');
          $pdf->Cell(80, 6, mb_convert_encoding($cualitativa, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

          $pdf->Ln();
     }

     $pdf->SetFont('Arial', 'B', 8);
     $pdf->SetFillColor(204, 204, 204);
     $pdf->Cell(100, 7, "PROMEDIO GENERAL", 1, 0, 'C', 1);

     $promedio_general = $suma_promedios / $numero_asignaturas;
     $promedio_general_truncado = number_format(truncateFloat($promedio_general, 2), 2);
     $promedio_general_truncado = str_replace(".", ",", $promedio_general_truncado);
     $pdf->Cell(80, 7, $promedio_general_truncado, 1, 0, 'C', 1);

     $pdf->Ln();

     $pdf->SetFont('Arial', 'B', 7);
     $text = mb_convert_encoding("EVALUACIÓN DE COMPORTAMIENTO (Cualitativo)", 'ISO-8859-1', 'UTF-8');
     $pdf->Cell(100, 7, $text, 1, 0, 'C', 1);

     // Calculo del comportamiento anual
     $periodo_eval_comp = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
     $num_total_registros = $db->num_rows($periodo_eval_comp);
     if ($num_total_registros > 0) {
          $suma_promedio = 0;
          while ($per_comp = $db->fetch_object($periodo_eval_comp)) {
               $id_periodo_evaluacion = $per_comp->id_periodo_evaluacion;

               $asignaturas = $db->consulta("SELECT a.id_asignatura 
								  FROM sw_asignatura a, 
									  sw_asignatura_curso ac, 
									  sw_paralelo p 
								 WHERE a.id_asignatura = ac.id_asignatura 
								   AND p.id_curso = ac.id_curso 
								   AND a.id_tipo_asignatura = 1
								   AND p.id_paralelo = $id_paralelo 
								 ORDER BY ac_orden");
               $total_asignaturas = $db->num_rows($asignaturas);

               if ($total_asignaturas > 0) {
                    $suma_comp_asignatura = 0;
                    $contador_asignaturas = 0;

                    while ($asignatura = $db->fetch_object($asignaturas)) {
                         $contador_asignaturas++;
                         $id_asignatura = $asignatura->id_asignatura;

                         // Aqui se consulta la calificacion del comportamiento ingresada por cada docente
                         $calificaciones = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
                         $calificaciones = $db->fetch_object($calificaciones);
                         $calificacion = ceil($calificaciones->calificacion);

                         $suma_comp_asignatura += $calificacion;
                    }

                    $promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
               }

               $suma_promedio += $promedio_comp;
          }

          $promedio_anual = ceil($suma_promedio / $num_total_registros);
     }

     $query = $db->consulta("SELECT ec_equivalencia, ec_cualitativa FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_anual");
     $registro = $db->fetch_object($query);
     $equivalencia = $registro->ec_equivalencia;

     $pdf->Cell(80, 7, $equivalencia, 1, 0, 'C', 1);
} else {
     $pdf->SetFont('Arial', 'B', 8);

     $y = $pdf->GetY();
     $pdf->Cell(32, 12, mb_convert_encoding("ÁREAS", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
     $pdf->Cell(65, 12, "ASIGNATURAS", 1, 0, 'C');
     $pdf->Cell(88, 4, "PROMEDIO ANUAL", 1, 0, 'C');

     $pdf->Ln();

     $pdf->SetFont('Arial', 'B', 7);
     $pdf->Cell(97, 4, " ", 0, 0, 'C');
     $pdf->Multicell(23, 4, mb_convert_encoding("CALIFICACIÓN", 'ISO-8859-1', 'UTF-8') . "\nCUANTITATIVA", 1, 'C');
     $pdf->SetXY(130, $y + 4);
     $pdf->Cell(65, 8, mb_convert_encoding("CALIFICACIÓN CUALITATIVA", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

     $pdf->Ln();

     $asignaturas = $db->consulta("SELECT a.id_asignatura, 
									 a.id_tipo_asignatura,
									 as_nombre,
									 ar_nombre 
								FROM sw_asignatura_curso ac, 
									 sw_paralelo p, 
									 sw_asignatura a,
									 sw_area ar
							   WHERE ac.id_curso = p.id_curso 
							     AND ac.id_asignatura = a.id_asignatura
								 AND ar.id_area = a.id_area 
								 AND id_paralelo = $id_paralelo 
							 ORDER BY ac_orden");
     $numero_asignaturas = $db->num_rows($asignaturas);
     $suma_promedios = 0;
     $contador_no_aprueba = 0;
     $contador = 0; // Contador de asignaturas cuantitativas

     while ($asignatura = $db->fetch_assoc($asignaturas)) {
          $id_asignatura = $asignatura["id_asignatura"];
          $id_tipo_asignatura = $asignatura["id_tipo_asignatura"];
          $nombreAsignatura = substr($asignatura["as_nombre"], 0, 50);
          // $nombreAsignatura = $asignatura["as_nombre"];
          $nombreArea = substr($asignatura["ar_nombre"], 0, 21);
          // $nombreArea = $asignatura["ar_nombre"];

          $pdf->SetFont('Arial', '', 7);
          $pdf->Cell(32, 5, mb_convert_encoding($nombreArea, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
          $pdf->Cell(65, 5, mb_convert_encoding($nombreAsignatura, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');

          if ($id_tipo_asignatura == 1) {
               //Se trata de una asignatura cuantitativa
               $query = $db->consulta("SELECT calcular_promedio_final($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");
               $registro = $db->fetch_object($query);
               $promedio_final = $registro->promedio_final;
               if ($promedio_final < 7) $contador_no_aprueba++;
               $suma_promedios += $promedio_final;
               $cualitativa = equiv_rendimiento($id_periodo_lectivo, $promedio_final);
               $promedio_final_truncado = number_format(truncateFloat($promedio_final, 2), 2);
               $promedio_final_truncado = str_replace(".", ",", $promedio_final_truncado);
               $pdf->Cell(23, 5, $promedio_final_truncado, 1, 0, 'C');
               $pdf->Cell(65, 5, mb_convert_encoding($cualitativa, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
               $contador++;
          } else {
               //Se trata de una asignatura cualitativa
               $query = $db->consulta("SELECT calc_prom_periodo_cualitativa($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");
               $registro = $db->fetch_object($query);
               $promedio_final = $registro->promedio_final;
               $query = $db->consulta("SELECT ref_cualitativa, equivalencia_subnivel FROM sw_escala_referencial WHERE nota_cuantitativa = $promedio_final");
               $registro = $db->fetch_object($query);
               $pdf->Cell(23, 5, $registro->ref_cualitativa, 1, 0, 'C');
               $pdf->Cell(65, 5, mb_convert_encoding($registro->equivalencia_subnivel, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
          }

          $pdf->Ln();
     }

     $pdf->SetFont('Arial', 'B', 8);
     $pdf->Cell(97, 6, "PROMEDIO GENERAL", 1, 0, 'C');

     $promedio_general = $suma_promedios / $contador;
     $promedio_general_truncado = number_format(truncateFloat($promedio_general, 2), 2);
     $promedio_general_truncado = str_replace(".", ",", $promedio_general_truncado);
     $pdf->Cell(23, 6, $promedio_general_truncado, 1, 0, 'C');

     $pdf->SetFont('Arial', 'B', 7);

     $cualitativa = equiv_rendimiento($id_periodo_lectivo, number_format(truncateFloat($promedio_general, 2), 2));
     $pdf->Cell(65, 6, mb_convert_encoding($cualitativa, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

     $pdf->Ln();

     $pdf->SetFont('Arial', 'B', 8);

     $text = mb_convert_encoding("EVALUACIÓN DE COMPORTAMIENTO", 'ISO-8859-1', 'UTF-8');
     $pdf->Cell(97, 6, $text, 1, 0, 'C');

     // Calculo del comportamiento anual
     $periodo_eval_comp = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
     $num_total_registros = $db->num_rows($periodo_eval_comp);
     if ($num_total_registros > 0) {
          $suma_promedio = 0;
          while ($per_comp = $db->fetch_object($periodo_eval_comp)) {
               $id_periodo_evaluacion = $per_comp->id_periodo_evaluacion;

               $asignaturas = $db->consulta("SELECT a.id_asignatura 
								  FROM sw_asignatura a, 
									  sw_asignatura_curso ac, 
									  sw_paralelo p 
								 WHERE a.id_asignatura = ac.id_asignatura 
								   AND p.id_curso = ac.id_curso 
								   AND a.id_tipo_asignatura = 1
								   AND p.id_paralelo = $id_paralelo 
								 ORDER BY ac_orden");
               $total_asignaturas = $db->num_rows($asignaturas);

               if ($total_asignaturas > 0) {
                    $suma_comp_asignatura = 0;
                    $contador_asignaturas = 0;

                    while ($asignatura = $db->fetch_object($asignaturas)) {
                         $contador_asignaturas++;
                         $id_asignatura = $asignatura->id_asignatura;

                         // Aqui se consulta la calificacion del comportamiento ingresada por cada docente
                         $calificaciones = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
                         $calificaciones = $db->fetch_object($calificaciones);
                         $calificacion = ceil($calificaciones->calificacion);

                         $suma_comp_asignatura += $calificacion;
                    }

                    $promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
               }

               $suma_promedio += $promedio_comp;
          }

          $promedio_anual = ceil($suma_promedio / $num_total_registros);
     }

     $query = $db->consulta("SELECT ec_equivalencia, ec_cualitativa, ec_relacion FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_anual");
     $registro = $db->fetch_object($query);
     $equivalencia = $registro->ec_equivalencia;
     $relacion = $registro->ec_relacion;

     $pdf->Cell(23, 6, $equivalencia, 1, 0, 'C');
     $pdf->Cell(65, 6, $relacion, 1, 0, 'C');
}

//Obtener el nombre del curso superior
$query = $db->consulta("SELECT cs_nombre FROM sw_curso_superior cs, sw_asociar_curso_superior ac WHERE cs.id_curso_superior = ac.id_curso_superior AND ac.id_curso_inferior = $id_curso");
$registro = $db->fetch_object($query);

$num_rows = $db->num_rows($query);

if ($num_rows > 0) {
     $curso_superior = $registro->cs_nombre;
} else {
     $curso_superior = "";
}

$pdf->Ln();

if ($curso_superior != "") {
     $pdf->SetFont('Arial', '', 9.5);
     $cadena_aprueba = ($contador_no_aprueba > 0) ? " NO" : "";
     $text = "Por lo tanto," . $cadena_aprueba . " es promovid" . $terminacion . " al: ";
     $w = $pdf->GetStringWidth($text);
     $pdf->Cell($w, 10, $text, 0, 'L', false);

     $pdf->SetFont('Arial', 'B', 9.5);
     $pdf->Cell(0, 10, mb_convert_encoding($curso_superior, 'ISO-8859-1', 'UTF-8'), 0, 'L', false);

     // $pdf->Ln();
}

$pdf->SetFont('Arial', '', 9.5);

if ($es_intensivo == 1) {
     $text = mb_convert_encoding("Para certificar suscriben el/la Rector/a y Secretaría General del Plantel.", 'ISO-8859-1', 'UTF-8');
} else {
     $text = mb_convert_encoding("Para certificar suscriben en unidad de acto el/la Director/a-Rector/a con el Secretario/a General del plantel.", 'ISO-8859-1', 'UTF-8');
}
$pdf->Cell(0, 10, $text, 0, 'L', false);

// $pdf->Ln();

$text = "Dado y firmado en: ";
$w = $pdf->GetStringWidth($text);
$pdf->Cell($w, 10, $text, 0, 0, 'L', false);

$pdf->SetFont('Arial', 'B', 9.5);
$w = $pdf->GetStringWidth($ciudadInstitucion);
$pdf->Cell($w, 10, $ciudadInstitucion . ", " . fecha_actual(), 0, 'L', false);

$pdf->Ln();
$pdf->Ln(8);

//Arial italic 8
$pdf->SetFont('Arial', '', 8);
//Aqui van las firmas de rector/a y secretaría
$pdf->SetX(10);
$titulo1 = '___________________________';
$pdf->Cell(27, 10, $titulo1, 0, 0, 'L');
$w = $pdf->GetStringWidth($titulo1);
$pdf->SetX(190 - $w);
$pdf->Cell($w, 8, $titulo1, 0, 0, 'L');

$pdf->Ln(5);
$pdf->SetX(10);
$pdf->Cell($w, 10, mb_convert_encoding($nombreRector, 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');

$titulo2 = mb_convert_encoding($nombreSecretario, 'ISO-8859-1', 'UTF-8');
$pdf->SetX(190 - $w);
$pdf->Cell($w, 8, $titulo2, 0, 0, 'C');

$pdf->Ln(5);
$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 8);

// Obtener género de la autoridad
$query = $db->consulta("SELECT in_genero_rector FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_rector = $registro->in_genero_rector;

$terminacion = ($in_genero_rector == 'M') ? '' : 'A';

$pdf->Cell($w, 10, 'RECTOR' . $terminacion, 0, 0, 'C');
$pdf->SetX(190 - $w);

// Obtener género del secretario
$query = $db->consulta("SELECT in_genero_secretario FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_secretario = $registro->in_genero_secretario;

$terminacion = ($in_genero_secretario == 'M') ? 'O' : 'A';

$pdf->Cell($w, 8, 'SECRETARI' . $terminacion, 0, 0, 'C');

//Logo Izquierda
$pdf->Image('escudo-ecuador.jpg', 145, 262, 43);

//Pie de página
// $pdf->Image('pie_pagina_hoja_formateada.jpg', 0, 264, 45);

$pdf->SetFillColor(204, 204, 204);


//Dirección del Ministerio de Educación
$pdf->SetFont('Arial', '', 6);
$pdf->SetXY(10, 262);
$text = "Dirección: Av. Amazonas N34-451 y Av. Atahualpa.";
$w = $pdf->GetStringWidth($text);
$pdf->Cell($w, 7, mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->Ln(3);
$text = "Código postal: 170507 / Quito-Ecuador";
$pdf->Cell($w, 7, mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->Ln(3);
$text = "Teléfono: 593-2-396-1300 / www.educacion.gob.ec";
$pdf->Cell($w, 7, mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

//********************************************************************************** */
header('Content-Type: application/pdf');
$pdf->Output(mb_convert_encoding($nombreEstudiante, 'ISO-8859-1', 'UTF-8') . ".pdf", "I");
