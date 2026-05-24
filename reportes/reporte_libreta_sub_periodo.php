<?php
require_once('../fpdf186/fpdf.php');
require_once('../scripts/clases/class.mysql.php');

function truncateFloat($number, $digitos)
{
    $raiz = 10;
    $multiplicador = pow($raiz, $digitos);
    $resultado = ((int)($number * $multiplicador)) / $multiplicador;
    return $resultado;
}

/* function equiv_rendimiento($id_periodo_lectivo, $calificacion)
{
    $db = new MySQL();
    // Determinacion de la letra de equivalencia que corresponde a la calificacion dada
    $escala_calificacion = $db->consulta("SELECT ec_cualitativa FROM sw_escala_calificaciones WHERE $calificacion >= ec_nota_minima AND $calificacion <= ec_nota_maxima AND id_periodo_lectivo = $id_periodo_lectivo");
    $escala = $db->fetch_object($escala_calificacion);
    return $escala->ec_cualitativa;
} */

class PDF extends FPDF
{
    var $nombreInstitucion = "";
    var $direccionInstitucion = "";
    var $telefonoInstitucion = "";
    var $AMIEInstitucion = "";
    var $logoInstitucion = "";
    var $nombrePeriodoLectivo = "";
    var $nombrePeriodoEvaluacion = "";
    var $nombreCurso = "";
    var $jornada = "";
    var $paralelo = "";
    var $es_intensivo = false;
    var $periodo = "";

    //Cabecera de pagina
    function Header()
    {
        //Logo Izquierda
        $this->Image('logo-presidencia-noboa.jpg', 10, 5, 33);
        //Logo Derecha
        $logoInstitucion = dirname(dirname(__FILE__)) . '/public/uploads/' . $this->logoInstitucion;
        $this->Image($logoInstitucion, 210 - 40, 5, 23);
        //Nombre de la IE
        $this->SetFont('Times', 'B', 14);
        $title = $this->nombreInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //Direccion de la IE
        $this->SetFont('Arial', 'I', 10);
        $title = $this->direccionInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //Telefono de la IE
        $this->SetFont('Arial', 'I', 9);
        $title = mb_convert_encoding("Teléfono: ", 'ISO-8859-1', 'UTF-8') . $this->telefonoInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //AMIE de la IE
        $this->SetFont('Arial', 'I', 10);
        $title = "AMIE: " . $this->AMIEInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(10);
        //Linea de division
        $this->Line(10, 35, 210 - 10, 35); // 20mm from each edge
        //Año Lectivo
        $this->SetFont('Times', '', 11);
        $title = mb_convert_encoding("AÑO LECTIVO: ", 'ISO-8859-1', 'UTF-8') . $this->nombrePeriodoLectivo;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //Periodo educativo
        if ($this->es_intensivo) {
            $this->SetFont('Arial', '', 9);
            $title = "PERIODO: " . $this->periodo;
            $w = $this->GetStringWidth($title);
            $this->SetX((210 - $w) / 2);
            $this->Cell($w, 10, $title, 0, 0, 'C');
            $this->Ln(5);
        }
        $this->Ln(2);
        //Titulo del Reporte
        $this->SetFont('Times', 'B', 12);
        $title = mb_convert_encoding("RENDIMIENTO ACADÉMICO - ", 'ISO-8859-1', 'UTF-8') . $this->nombrePeriodoEvaluacion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
    }

    //Pie de pagina
    function Footer()
    {
        //Posicion: a 2 cm del final
        $this->SetY(-20);
        //Dirección del Ministerio de Educación
        $this->SetFont('Arial', '', 6);
        $text = mb_convert_encoding("Dirección: Av. Amazonas N34-451 y Av. Atahualpa.", 'ISO-8859-1', 'UTF-8');
        $w = $this->GetStringWidth($text);
        $this->Cell($w, 7, $text, 0, 0, 'L');
        $this->Ln(3);
        $text = mb_convert_encoding("Código postal: 170507 / Quito-Ecuador", 'ISO-8859-1', 'UTF-8');
        $this->Cell($w, 7, $text, 0, 0, 'L');
        $this->Ln(3);
        $text = mb_convert_encoding("Teléfono: 593-2-396-1300 / www.educacion.gob.ec", 'ISO-8859-1', 'UTF-8');
        $this->Cell($w, 7, $text, 0, 0, 'L');
        //Logo Izquierda
        $this->Image('escudo-ecuador.jpg', 155, 268, 36);
    }
}

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

//Abreviaturas de los meses del año
$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_estudiante = $_POST["id_estudiante"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_institucion WHERE id_institucion = 1");
$institucion = $db->fetch_object($consulta);

//Creacion del objeto de la clase heredada
$pdf = new PDF('P');

//mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8')
$pdf->nombreInstitucion = mb_convert_encoding($institucion->in_nombre, 'ISO-8859-1', 'UTF-8');
$pdf->direccionInstitucion = mb_convert_encoding($institucion->in_direccion, 'ISO-8859-1', 'UTF-8');
$pdf->telefonoInstitucion = $institucion->in_telefono;
$pdf->AMIEInstitucion = $institucion->in_amie;
$pdf->logoInstitucion = $institucion->in_logo;
$nombreRector = $institucion->in_nom_rector;
$nombreSecretario = $institucion->in_nom_secretario;

// Obtener el nombre del periodo de evaluacion
$qry = "SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion";
$consulta = $db->consulta($qry);
$periodo_evaluacion = $db->fetch_object($consulta);
$nombrePeriodoEvaluacion = $periodo_evaluacion->pe_nombre;

$pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;

// Obtener Periodo Lectivo
$consulta = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$periodo_lectivo = $db->fetch_object($consulta);
$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;

$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

// Determinar si se trata de oferta intensiva
$qry = "SELECT es_intensivo FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND id_paralelo = $id_paralelo";
$consulta = $db->consulta($qry);
$es_intensivo = $db->fetch_object($consulta)->es_intensivo;

$pdf->es_intensivo = $es_intensivo == 1 ? true : false;

// Para la cabecera de la pagina
//Periodo educativo
$qry = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$res = $db->fetch_object($qry);
$fecha_inicial = explode("-", $res->pe_fecha_inicio);
$fecha_final = explode("-", $res->pe_fecha_fin);

$periodo_educativo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];
$pdf->periodo = $periodo_educativo;

// Obtener los datos del estudiante
$consulta = $db->consulta("SELECT * FROM sw_estudiante WHERE id_estudiante = $id_estudiante");
$resultado = $db->fetch_object($consulta);
$nombreEstudiante = $resultado->es_apellidos . " " . $resultado->es_nombres;

$pdf->AliasNbPages();
$pdf->AddPage();

// Desplegar el nombre del estudiante
$pdf->SetFont('Arial', 'B', 11);
//mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8')
$text = "ESTUDIANTE: " . mb_convert_encoding($nombreEstudiante, 'ISO-8859-1', 'UTF-8');
$w = $pdf->GetStringWidth($text);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell(150, 10, $text, 0, 0, 'L');
$pdf->Ln(5);

//Aqui obtengo la jornada
$qry = $db->consulta("SELECT es_figura, 
                             cu_nombre,
                             pa_nombre,
                             jo_nombre
                        FROM sw_especialidad e, 
                             sw_curso c,
                             sw_jornada j, 
                             sw_paralelo p
                       WHERE e.id_especialidad = c.id_especialidad
                         AND c.id_curso = p.id_curso
                         AND j.id_jornada = p.id_jornada  
                         AND id_paralelo = $id_paralelo");
$res = $db->fetch_object($qry);
$especialidad = $res->es_figura;
$curso = $res->cu_nombre;
$paralelo = $res->pa_nombre;
$jornada = $res->jo_nombre;

$pdf->SetFont('Arial', 'B', 9.5);
$text = $curso . " DE " . $especialidad;
$w = $pdf->GetStringWidth($text);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $text, 0, 0, 'L');
$pdf->Ln(7);
//Jornada
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(15);
$pdf->Cell(60, 10, "JORNADA " . $jornada, 0, 0, 'L');
//Paralelo
$pdf->SetX(155);
$pdf->Cell(60, 10, "PARALELO \"" . $paralelo . "\"", 0, 0, 'L');
$pdf->Ln(7);

$pdf->SetFont('Arial', 'B', 8);

$y = $pdf->GetY();
//mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8')
$pdf->Cell(35, 12, mb_convert_encoding("ÁREAS", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
$pdf->Cell(65, 12, "ASIGNATURAS", 1, 0, 'C');
$pdf->Cell(88, 4, "REPORTE " . $nombrePeriodoEvaluacion, 1, 0, 'C');

$pdf->Ln();

$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(100, 4, " ", 0, 0, 'C');
//mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8')
$pdf->Multicell(23, 4, mb_convert_encoding("CALIFICACIÓN", 'ISO-8859-1', 'UTF-8') . "\nCUANTITATIVA", 1, 'C');
$pdf->SetXY(133, $y + 4);
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
    $nombreAsignatura = $asignatura["as_nombre"];
    $nombreArea = $asignatura["ar_nombre"];

    $w = $pdf->GetStringWidth($asignatura["ar_nombre"]);

    $pdf->SetFont('Arial', '', 7);

    $y = $pdf->GetY();
    $h = 4;

    if ($w > 35) {
        $nombreAreaLinea1 = substr($nombreArea, 0, 21);
        $nombreAreaLinea2 = substr($nombreArea, 21);
        $pdf->Multicell(35, 4, $nombreArea, 1, 'L');
        $pdf->SetXY(45, $y);
        $h = 8;
    } else {
        $pdf->Cell(35, $h, $nombreArea, 1, 0, 'L');
    }

    //mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8')
    $pdf->Cell(65, $h, mb_convert_encoding($nombreAsignatura, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');

    if ($id_tipo_asignatura == 1) {
        $query = $db->consulta("SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_asignatura");

        $registro = $db->fetch_object($query);
        $promedio_asignatura = $registro->promedio_asignatura;

        $suma_promedios += $promedio_asignatura;

        $promedio_asignatura_truncado = number_format(truncateFloat($promedio_asignatura, 2), 2);
        $promedio_asignatura_truncado = str_replace(".", ",", $promedio_asignatura_truncado);

        $pdf->Cell(23, $h, $promedio_asignatura_truncado, 1, 0, 'C');

        // Determinacion de la letra de equivalencia que corresponde a la calificacion dada

        $parte_entera = intval($promedio_asignatura);
        $parte_decimal = $promedio_asignatura - $parte_entera;

        $promedio_redondeado = $parte_decimal >= 0.5 ? $parte_entera + 1 : $parte_entera;

        $consulta = $db->consulta("SELECT ref_cualitativa FROM sw_escala_referencial WHERE nota_cuantitativa = $promedio_redondeado");
        $registro = $db->fetch_object($consulta);
        if (empty($registro)) {
            $ref_cualitativa = "";
        } else {
            $ref_cualitativa = $registro->ref_cualitativa;
        }

        $pdf->Cell(65, $h, $ref_cualitativa, 1, 0, 'C');
        $contador++;
    } else {
        //Aqui consulto las asignaturas cualitativas
        $consulta = $db->consulta("SELECT calc_prom_subperiodo_cualitativa($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_sub_periodo");

        $registro = $db->fetch_object($consulta);
        $promedio_sub_periodo = $registro->promedio_sub_periodo;

        $consulta = $db->consulta("SELECT ref_cualitativa FROM sw_escala_referencial WHERE nota_cuantitativa = $promedio_sub_periodo");
        $registro = $db->fetch_object($consulta);
        if (empty($registro)) {
            $ref_cualitativa = "";
        } else {
            $ref_cualitativa = $registro->ref_cualitativa;
        }

        $pdf->Cell(23, $h, " ", 1, 0, 'C');
        $pdf->Cell(65, $h, $ref_cualitativa, 1, 0, 'C');
    }

    $pdf->Ln();
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(100, 6, "PROMEDIO GENERAL", 1, 0, 'C');

$promedio_general = $suma_promedios / $contador;
$promedio_general_truncado = number_format(truncateFloat($promedio_general, 2), 2);
$promedio_general_truncado = str_replace(".", ",", $promedio_general_truncado);
$pdf->Cell(23, 6, $promedio_general_truncado, 1, 0, 'C');

$parte_entera = intval($promedio_general);
$parte_decimal = $promedio_general - $parte_entera;

$consulta = $db->consulta("SELECT equivalencia_subnivel FROM sw_escala_referencial WHERE nota_cuantitativa = $parte_entera");
$registro = $db->fetch_object($consulta);
if (empty($registro)) {
    $ref_cualitativa = "";
} else {
    $ref_cualitativa = $registro->equivalencia_subnivel;
}

//mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8')
$pdf->Cell(65, 6, mb_convert_encoding($ref_cualitativa, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

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
//mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8')
$pdf->Cell($w, 10, mb_convert_encoding($nombreRector, 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');

$titulo2 = $nombreSecretario;
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


$pdf->Output();
