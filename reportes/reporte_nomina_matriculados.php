<?php
require_once '../fpdf186/fpdf.php';
require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';

class PDF extends FPDF
{
  var $nombreInstitucion = "";
  var $direccionInstitucion = "";
  var $telefonoInstitucion = "";
  var $AMIEInstitucion = "";
  var $ciudadInstitucion = "";
  var $nombreRector = "";
  var $nombreSecretario = "";
  var $logoInstitucion = "";
  var $nombrePeriodoLectivo = "";
  var $nombreCurso = "";
  var $jornada = "";
  var $paralelo = "";
  var $periodo = "";

  //Cabecera de pagina
  function Header()
  {
    //Logo Izquierda
    // $this->Image('ministerio.png',10,18,33);
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
    $this->SetFont('Arial', 'I', 12);
    $title = $this->direccionInstitucion;
    $w = $this->GetStringWidth($title);
    $this->SetX((210 - $w) / 2);
    $this->Cell($w, 10, $title, 0, 0, 'C');
    $this->Ln(5);
    //Telefono de la IE
    $this->SetFont('Arial', 'I', 11);
    $title = utf8_decode("Teléfono: ") . $this->telefonoInstitucion;
    $w = $this->GetStringWidth($title);
    $this->SetX((210 - $w) / 2);
    $this->Cell($w, 10, $title, 0, 0, 'C');
    $this->Ln(5);
    //AMIE de la IE
    $this->SetFont('Arial', 'I', 11);
    $title = "AMIE: " . $this->AMIEInstitucion;
    $w = $this->GetStringWidth($title);
    $this->SetX((210 - $w) / 2);
    $this->Cell($w, 10, $title, 0, 0, 'C');
    $this->Ln(10);
    //Linea de division
    $this->Line(10, 35, 210 - 10, 35); // 20mm from each edge
    //Titulo del Reporte
    $this->SetFont('Times', 'B', 14);
    $title = "NOMINA DE MATRICULADOS";
    $w = $this->GetStringWidth($title);
    $this->SetX((210 - $w) / 2);
    $this->Cell($w, 10, $title, 0, 0, 'C');
    $this->Ln(5);
    //Año Lectivo
    $this->SetFont('Times', '', 12);
    $title = utf8_decode("AÑO LECTIVO: " . $this->nombrePeriodoLectivo);
    $w = $this->GetStringWidth($title);
    $this->SetX((210 - $w) / 2);
    $this->Cell($w, 10, $title, 0, 0, 'C');
    $this->Ln(5);
    //Periodo educativo
    // $this->SetFont('Times', '', 11);
    // $title = "PERIODO: " . $this->periodo;
    // $w = $this->GetStringWidth($title);
    // $this->SetX((210 - $w) / 2);
    // $this->Cell($w, 10, $title, 0, 0, 'C');
    $this->Ln(5);
    //Nombre del Curso
    $this->SetFont('Times', '', 12);
    $title = utf8_decode($this->nombreCurso);
    $w = $this->GetStringWidth($title);
    $this->SetX((210 - $w) / 2);
    $this->Cell($w, 10, $title, 0, 0, 'C');
    $this->Ln(7);
    //Jornada
    $this->SetFont('Arial', 'B', 10);
    $this->SetX(15);
    $this->Cell(60, 10, "JORNADA " . $this->jornada, 0, 0, 'L');
    //Paralelo
    $this->SetX(155);
    $this->Cell(60, 10, "PARALELO \"" . $this->paralelo . "\"", 0, 0, 'L');
    $this->Ln(7);
    //Cabeceras de las columnas
    $this->SetFont('Arial', 'B', 9);
    $y = $this->GetY();
    $this->Cell(14, 8, utf8_decode("N°"), 1, 0, 'C');
    $this->Multicell(80, 4, utf8_decode("NÓMINA DE ESTUDIANTES") . "\n(apellidos y nombres)", 1, 'C');
    $this->SetXY(104, $y);
    $this->Multicell(24, 4, utf8_decode("N° DE\nMATRÍCULA"), 1, 'C');
    $this->SetXY(128, $y);
    $this->Cell(62, 8, utf8_decode("CÉDULA/PASAPORTE"), 1, 0, 'C');
    $this->Ln();
  }

  //Pie de pagina
  function Footer()
  {
    //Posicion: a 3 cm del final
    $this->SetY(-30);
    //Dirección del Ministerio de Educación
    $this->SetFont('Arial', '', 6);
    $text = "Dirección: Av. Amazonas N34-451 y Av. Atahualpa.";
    $w = $this->GetStringWidth($text);
    $this->Cell($w, 7, utf8_decode($text), 0, 0, 'L');
    $this->Ln(3);
    $text = "Código postal: 170507 / Quito-Ecuador";
    $this->Cell($w, 7, utf8_decode($text), 0, 0, 'L');
    $this->Ln(3);
    $text = "Teléfono: 593-2-396-1300 / www.educacion.gob.ec";
    $this->Cell($w, 7, utf8_decode($text), 0, 0, 'L');
    //Logo Izquierda
    $this->Image('escudo-ecuador.jpg', 145, 258, 36);
  }
}

//Abreviaturas de los meses del año
$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

// Variables enviadas mediante POST
$id_paralelo = $_POST["cboParalelos"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = utf8_decode($institucion->obtenerNombreInstitucion());
$direccionInstitucion = utf8_decode($institucion->obtenerDireccionInstitucion());
$telefonoInstitucion = utf8_decode($institucion->obtenerTelefonoInstitucion());
$AMIEInstitucion = $institucion->obtenerAMIEInstitucion();
$ciudadInstitucion = $institucion->obtenerCiudadInstitucion();
$nombreRector = utf8_decode($institucion->obtenerNombreRector());
$nombreSecretario = utf8_decode($institucion->obtenerNombreSecretario());
$logoInstitucion = $institucion->obtenerLogoInstitucion();

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

// $paralelo = new paralelos();
// $id_curso = $paralelo->obtenerIdCurso($id_paralelo);

$db = new mysql();
$consulta = $db->consulta("SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$id_curso = $db->fetch_object($consulta)->id_curso;

// $cursos = new cursos();
// $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);

$consulta = $db->consulta("SELECT cu_nombre, es_nombre, es_figura, es_bach_tecnico FROM sw_curso cu, sw_especialidad es WHERE cu.id_especialidad = es.id_especialidad AND cu.id_curso = $id_curso");
$resultado = $db->fetch_object($consulta);
if ($resultado->es_bach_tecnico == 0) {
  $nombreCurso = $resultado->cu_nombre . " DE " . $resultado->es_figura;
} else {
  $nombreCurso = $resultado->cu_nombre . " DE " . $resultado->es_nombre . ": " . $resultado->es_figura;
}

//Creacion del objeto de la clase heredada
$pdf = new PDF('P');

$pdf->nombreInstitucion = $nombreInstitucion;
$pdf->direccionInstitucion = $direccionInstitucion;
$pdf->telefonoInstitucion = $telefonoInstitucion;
$pdf->AMIEInstitucion = $AMIEInstitucion;
$pdf->ciudadInstitucion = $ciudadInstitucion;
$pdf->nombreRector = $nombreRector;
$pdf->nombreSecretario = $nombreSecretario;
$pdf->logoInstitucion = $logoInstitucion;
$pdf->nombreCurso = $nombreCurso;
$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

// Para la cabecera de la pagina
//Periodo educativo
$qry = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$res = $db->fetch_object($qry);
$fecha_inicial = explode("-", $res->pe_fecha_inicio);
$fecha_final = explode("-", $res->pe_fecha_fin);

$periodo_educativo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];
$pdf->periodo = $periodo_educativo;

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
$paralelo = $res["pa_nombre"];

$pdf->jornada = $jornada;
$pdf->paralelo = $paralelo;

$pdf->AliasNbPages();
$pdf->AddPage();

// $pdf->SetY(100); // Inicio
//     $pdf->SetFont('Arial','B',12);
//     $pdf->Cell(40,10,'Columna1',1,0,'C');
//     $pdf->MultiCell(40,10,'palabras y mas palabras',1,'C');
//     $pdf->SetY(110); // Set 20 Eje Y
//     $pdf->Cell(40,10,'Columna3',1,0,'C');

$qry = $db->consulta("SELECT es_apellidos, 
                                 es_nombres, 
                                 nro_matricula,
                                 es_cedula
                            FROM sw_estudiante e, 
                                 sw_estudiante_periodo_lectivo ep 
                           WHERE e.id_estudiante = ep.id_estudiante
                             AND ep.id_paralelo = $id_paralelo   
                             AND ep.id_periodo_lectivo = $id_periodo_lectivo 
                             AND ep.activo = 1
                           ORDER BY es_apellidos, es_nombres");
$contador = 0;
$pdf->SetFont('Times', '', 9);
while ($row = $db->fetch_assoc($qry)) {
  $contador++;
  if ($contador % 27 == 0) $pdf->AddPage();
  $nombreEstudiante = $row["es_apellidos"] . " " . $row["es_nombres"];
  $matricula = str_pad($row["nro_matricula"], 4, "0", STR_PAD_LEFT);
  $cedula = $row["es_cedula"];
  $pdf->Cell(14, 6, $contador, 1, 0, 'C');
  if (strlen($nombreEstudiante) > 40) $pdf->SetFont('Times', '', 8);
  $pdf->Cell(80, 6, utf8_decode($nombreEstudiante), 1, 0, 'L');
  $pdf->SetFont('Times', '', 9);
  $pdf->Cell(24, 6, $matricula, 1, 0, 'C');
  $pdf->Cell(62, 6, $cedula, 1, 0, 'L');
  $pdf->Ln();
}

//Firmas de la Autoridad y/o del Secretario (a)
$y = $pdf->GetY() + 10;
//$pdf->Cell(14, 6, $y, 0, 0, 'C');
$pdf->Line(24, $y, 94, $y);
$pdf->Line(114, $y, 184, $y);
$pdf->SetY($y + 1);
$pdf->Cell(14, 6, "", 0, 0, 'C');
$pdf->Cell(70, 6, $nombreRector, 0, 0, 'C');
$pdf->Cell(20, 6, "", 0, 0, 'C');
$pdf->Cell(70, 6, $nombreSecretario, 0, 0, 'C');
$pdf->Ln(3);

// Obtener género de la autoridad
$query = $db->consulta("SELECT in_genero_rector FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_rector = $registro->in_genero_rector;

$terminacion_rector = ($in_genero_rector == 'M') ? '' : 'A';

// Obtener género del secretario
$query = $db->consulta("SELECT in_genero_secretario FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_secretario = $registro->in_genero_secretario;

$terminacion_secretario = ($in_genero_secretario == 'M') ? 'O' : 'A';

$pdf->Cell(14, 6, "", 0, 0, 'C');
$pdf->Cell(70, 6, 'RECTOR' . $terminacion_rector, 0, 0, 'C');
$pdf->Cell(20, 6, "", 0, 0, 'C');
$pdf->Cell(70, 6, 'SECRETARI' . $terminacion_secretario, 0, 0, 'C');

$pdf->Output();
