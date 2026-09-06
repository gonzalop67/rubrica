<?php
require_once('../fpdf186/fpdf.php');
require_once('../scripts/clases/class.mysql.php');

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

function truncarDosDecimales(float $valor)
{
    $float = (float)$valor;
    if ($float == 0) return 0;

    // Sumamos una millonésima (0.000001) para corregir el desfase binario de PHP (ej: 7.09999999 -> 7.100000)
    $comprobacion = $float + 0.000001;

    // Desplazamos la coma dos posiciones, truncamos el resto con floor y regresamos la coma
    return floor($comprobacion * 100) / 100;
}

function equiv_rendimiento($id_periodo_lectivo, $calificacion)
{
    $db = new MySQL();

    // Forzamos a flotante para mitigar riesgos de inyección y asegurar compatibilidad SQL
    $calificacion_num = floatval($calificacion);

    // 1. La base de datos busca directamente el rango correcto usando BETWEEN
    $consulta = $db->consulta("SELECT ec_cualitativa 
                                FROM sw_escala_calificaciones 
                                WHERE id_periodo_lectivo = $id_periodo_lectivo 
                                  AND $calificacion_num BETWEEN ec_nota_minima AND ec_nota_maxima 
                                LIMIT 1");

    // 2. Si encuentra el registro, lo retorna de inmediato
    if ($escala = $db->fetch_object($consulta)) {
        return $escala->ec_cualitativa;
    }

    return ""; // Retorno por defecto si la calificación no entra en ningún rango
}

class PDF extends FPDF
{
    public $nombreInstitucion = "";
    public $direccionInstitucion = "";
    public $telefonoInstitucion = "";
    public $AMIEInstitucion = "";
    public $logoInstitucion = "";
    public $es_intensivo = false;
    public $periodo = "";

    // CORRECCIÓN: Se declara la propiedad para que esté disponible en Header()
    public $nombrePeriodoLectivo = "";

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
        $title = mb_convert_encoding("RENDIMIENTO ACADÉMICO FINAL", 'ISO-8859-1', 'UTF-8');
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

//Abreviaturas de los meses del año
$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_estudiante = $_POST["id_estudiante"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_institucion WHERE id_institucion = 1");
$institucion = $db->fetch_object($consulta);

//Creacion del objeto de la clase heredada
$pdf = new PDF('P');

$pdf->nombreInstitucion = mb_convert_encoding($institucion->in_nombre, 'ISO-8859-1', 'UTF-8');
$pdf->direccionInstitucion = mb_convert_encoding($institucion->in_direccion, 'ISO-8859-1', 'UTF-8');
$pdf->telefonoInstitucion = $institucion->in_telefono;
$nombreRector = $institucion->in_nom_rector;
$nombreSecretario = $institucion->in_nom_secretario;
$in_genero_rector = $institucion->in_genero_rector;
$in_genero_secretario = $institucion->in_genero_secretario;
$pdf->AMIEInstitucion = $institucion->in_amie;
$pdf->logoInstitucion = $institucion->in_logo;

// Obtener Periodo Lectivo
$consulta = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$periodo_lectivo = $db->fetch_object($consulta);
$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;

// Se asigna la variable justo antes de AddPage() para que Header() pueda leerla
$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

// Determinar si se trata de oferta intensiva
$qry = "SELECT es_intensivo FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND id_paralelo = $id_paralelo";
$consulta = $db->consulta($qry);
$es_intensivo = $db->fetch_object($consulta)->es_intensivo;

$pdf->es_intensivo = $es_intensivo == 1 ? true : false;

// Para la cabecera de la pagina
// Periodo educativo
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
$pdf->Cell(88, 4, "REPORTE DE FIN DE PERIODO LECTIVO", 1, 0, 'C');

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

    // Convertimos el texto a ISO-8859-1 antes de medirlo o dibujarlo
    $nombreArea = mb_convert_encoding($asignatura["ar_nombre"], 'ISO-8859-1', 'UTF-8');

    $pdf->SetFont('Arial', '', 7);

    // 1. Guardamos la posición inicial de la fila
    $x_inicial = $pdf->GetX();
    $y_inicial = $pdf->GetY();

    $ancho_columna = 35;
    $alto_linea = 4; // El alto de cada línea de texto dentro de la celda

    // 2. Dibujamos el Nombre del Área usando MultiCell para que rompa automáticamente
    $pdf->MultiCell($ancho_columna, $alto_linea, $nombreArea, 1, 'L');

    // 3. Calculamos la posición final para saber qué tan alta quedó esta celda
    $y_final = $pdf->GetY();
    $alto_celda_real = $y_final - $y_inicial;

    // 4. Movemos el cursor al lado derecho de la celda que acabamos de crear y restauramos el Y inicial
    $pdf->SetXY($x_inicial + $ancho_columna, $y_inicial);

    // 5. AQUÍ CONTINÚAN TUS OTRAS CELDAS (Ejemplo: Nombre de Asignatura, Calificaciones)
    // Nota: Para que la tabla se vea perfecta, estas celdas deben tener el alto real ($alto_celda_real)
    $nombreAsignatura_iso = mb_convert_encoding($nombreAsignatura, 'ISO-8859-1', 'UTF-8');
    $pdf->Cell(65, $alto_celda_real, $nombreAsignatura_iso, 1, 0, 'L');

    // CORRECCIÓN: Forzamos a entero o usamos comparación débil (==) para evitar fallos de tipo string
    if ((int)$id_tipo_asignatura === 1) {
        $query = $db->consulta("SELECT calcular_promedio_final($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");

        $registro = $db->fetch_object($query);
        $promedio_final = $registro->promedio_final;

        $suma_promedios += $promedio_final;
        $contador++;

        $promedio_final_truncado = number_format(truncarDosDecimales(floatval($promedio_final)), 2);
        $promedio_final_truncado = str_replace(".", ",", $promedio_final_truncado);

        // Celda de nota cuantitativa
        $pdf->Cell(23, $alto_celda_real, $promedio_final_truncado, 1, 0, 'C');

        $cualitativa = equiv_rendimiento($id_periodo_lectivo, $promedio_final);
        $pdf->Cell(65, $alto_celda_real, mb_convert_encoding($cualitativa, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
    } else {
        // Se trata de una asignatura cualitativa
        $query = $db->consulta("SELECT calc_prom_periodo_cualitativa($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");
        $registro = $db->fetch_object($query);
        $promedio_final = $registro->promedio_final;

        $query = $db->consulta("SELECT ref_cualitativa, equivalencia_subnivel FROM sw_escala_referencial WHERE nota_cuantitativa = $promedio_final");
        $registro = $db->fetch_object($query);

        // Celda de nota cualitativa
        $pdf->Cell(23, $alto_celda_real, $registro->ref_cualitativa, 1, 0, 'C');
        // Celda de equivalencia
        $pdf->Cell(65, $alto_celda_real, mb_convert_encoding($registro->equivalencia_subnivel, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
    }

    // 6. Al finalizar todas las celdas de la fila, saltamos a la línea de abajo usando el Y final más alto
    $pdf->SetY($y_final);
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(100, 6, "PROMEDIO GENERAL", 1, 0, 'C');

$promedio_general = $suma_promedios / $contador;
$promedio_general_truncado = number_format(truncarDosDecimales($promedio_general), 2);
$promedio_general_truncado = str_replace(".", ",", $promedio_general_truncado);
$pdf->Cell(23, 6, $promedio_general_truncado, 1, 0, 'C');

$pdf->SetFont('Arial', 'B', 7);

$cualitativa = equiv_rendimiento($id_periodo_lectivo, number_format(truncarDosDecimales($promedio_general), 2));
$pdf->Cell(65, 6, mb_convert_encoding($cualitativa, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

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

$terminacion = ($in_genero_rector == 'M') ? '' : 'A';
$pdf->Cell($w, 10, 'RECTOR' . $terminacion, 0, 0, 'C');
$pdf->SetX(190 - $w);

$terminacion = ($in_genero_secretario == 'M') ? 'O' : 'A';
$pdf->Cell($w, 8, 'SECRETARI' . $terminacion, 0, 0, 'C');

$pdf->Output();
