<?php
// require('../fpdf16/fpdf.php');
require('../fpdf186/fpdf.php');
require('../scripts/clases/class.mysql.php');
require('../scripts/clases/class.institucion.php');

$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$dias = array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");
$mes = $meses[date("n")];
$dia = $dias[date("w")];

function fecha_actual($ciudad)
{
    global $mes, $dia;
    $fecha_string = "$ciudad,  " . date("j") . " de $mes, " . date("Y");
    return $fecha_string;
}

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo_certificado"];
$id_estudiante = $_POST["id_estudiante"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = utf8_decode($institucion->obtenerNombreInstitucion());
$direccionInstitucion = utf8_decode($institucion->obtenerDireccionInstitucion());
$telefonoInstitucion = utf8_decode($institucion->obtenerTelefonoInstitucion());
$AMIEInstitucion = $institucion->obtenerAMIEInstitucion();
$ciudadInstitucion = $institucion->obtenerCiudadInstitucion();
$nombreRector = utf8_decode($institucion->obtenerNombreRector());
$logoInstitucion = $institucion->obtenerLogoInstitucion();

//Creación del objeto FPDF
$pdf = new FPDF();
$pdf->AddPage();

//Logo Izquierda
$pdf->Image('ministerio.png', 10, 18, 33);

//Logo Derecha
$pdf->Image("../public/uploads/" . $logoInstitucion, 210 - 40, 5, 23);

$pdf->SetFont('Times', 'B', 14);
$title = $nombreInstitucion;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'I', 12);
$title = $direccionInstitucion;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'I', 11);
$title = utf8_decode("Teléfono: ") . $telefonoInstitucion;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'I', 11);
$title = "AMIE: " . $AMIEInstitucion;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(15);

$pdf->Line(10, 35, 210 - 10, 35); // 20mm from each edge

$pdf->SetFont('Times', 'B', 14);
$title = "CERTIFICADO DE MATRICULA";
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$text = utf8_decode("El Rector(a) de la Institución, previo cumplimiento de las respectivas disposiciones de la Ley Orgánica");
$pdf->Cell(200, 10, $text, 0, 0, 'L');
$pdf->Ln(5);

$text = utf8_decode("de Educación Intercultural y su Reglamento vigentes.");
$pdf->Cell(200, 10, $text, 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(200, 10, "CERTIFICA:", 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$pdf->Cell(200, 10, utf8_decode("Que en los archivos de Secretaría, está inscrita la siguiente MATRICULA:"), 0, 0, 'L');
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$pdf->Cell(50, 10, "ESTUDIANTE:", 0, 0, 'L');

//Aqui obtengo el nombre completo del estudiante
$db = new mysql();
$qry = $db->consulta("SELECT es_apellidos, 
                                 es_nombres, 
                                 nro_matricula
                            FROM sw_estudiante e, 
                                 sw_estudiante_periodo_lectivo ep 
                           WHERE e.id_estudiante = ep.id_estudiante 
                             AND ep.id_estudiante = $id_estudiante 
                             AND ep.id_periodo_lectivo = $id_periodo_lectivo");
$res = $db->fetch_assoc($qry);
$nombreEstudiante = $res["es_apellidos"] . " " . $res["es_nombres"];
$matricula = str_pad($res["nro_matricula"], 4, "0", STR_PAD_LEFT);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(150, 10, $nombreEstudiante, 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$pdf->Cell(50, 10, "NIVEL DE EDUCACION:", 0, 0, 'L');

//Aqui obtengo el nivel de educación del estudiante
$qry = $db->consulta("SELECT te_nombre, 
                                 te_bachillerato 
                            FROM sw_tipo_educacion t, 
                                 sw_especialidad e, 
                                 sw_curso c, 
                                 sw_paralelo p 
                           WHERE t.id_tipo_educacion = e.id_tipo_educacion 
                             AND e.id_especialidad = c.id_especialidad 
                             AND c.id_curso = p.id_curso 
                             AND id_paralelo = $id_paralelo");
$res = $db->fetch_assoc($qry);
$nivelEducacion = utf8_decode($res["te_nombre"]);
$es_bachillerato = $res["te_bachillerato"];

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(150, 10, $nivelEducacion, 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$pdf->Cell(50, 10, "JORNADA:", 0, 0, 'L');

//Aqui obtengo la jornada
$qry = $db->consulta("SELECT jo_nombre, 
                                 cu_nombre,
                                 pa_nombre
                            FROM sw_jornada j, 
                                 sw_curso c, 
                                 sw_paralelo p
                           WHERE c.id_curso = p.id_curso 
                             AND j.id_jornada = p.id_jornada 
                             AND id_paralelo = $id_paralelo");
$res = $db->fetch_assoc($qry);
$jornada = $res["jo_nombre"];
$curso = $res["cu_nombre"];
$paralelo = $res["pa_nombre"];

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(30, 10, $jornada, 0, 0, 'L');

$pdf->SetFont('Times', '', 12);
$pdf->Cell(30, 10, utf8_decode("AÑO LECTIVO:"), 0, 0, 'L');

//Aqui obtengo el año lectivo
$qry = $db->consulta("SELECT pe_anio_inicio, 
                                 pe_anio_fin 
                            FROM sw_periodo_lectivo pl, 
                                 sw_estudiante_periodo_lectivo ep 
                           WHERE pl.id_periodo_lectivo = ep.id_periodo_lectivo 
                             AND ep.id_estudiante = $id_estudiante 
                             AND ep.id_periodo_lectivo = $id_periodo_lectivo");
$res = $db->fetch_assoc($qry);
$anio_lectivo = $res["pe_anio_inicio"] . "-" . $res["pe_anio_fin"];

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(30, 10, $anio_lectivo, 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$pdf->Cell(50, 10, utf8_decode("MATRÍCULA:"), 0, 0, 'L');

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(30, 10, $matricula, 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$pdf->Cell(50, 10, utf8_decode("GRADO/CURSO:"), 0, 0, 'L');

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(30, 10, utf8_decode($curso), 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$pdf->Cell(50, 10, utf8_decode("PARALELO:"), 0, 0, 'L');

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(30, 10, "\"" . $paralelo . "\"", 0, 0, 'L');
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Times', '', 12);
$title = fecha_actual($ciudadInstitucion);
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Times', '', 14);
$title = $nombreRector;
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln(5);

$pdf->SetFont('Times', 'B', 14);
$title = "RECTOR(A) DEL PLANTEL";
$w = $pdf->GetStringWidth($title);
$pdf->SetX((210 - $w) / 2);
$pdf->Cell($w, 10, $title, 0, 0, 'C');
$pdf->Ln();

header('Content-Type: application/pdf');
$pdf->Output(utf8_decode($nombreEstudiante) . ".pdf", "I");
