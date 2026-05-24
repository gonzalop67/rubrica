<?php
require_once "../scripts/clases/class.mysql.php";
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';

require_once "../vendor/autoload.php";

//load phpspreadsheet class using namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;

/* Error reporting */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

$db = new MySQL();

function contarAsignaturas($id_paralelo, $id_curso)
{
    global $db;

    $consulta = $db->consulta("SELECT COUNT(*) AS total_registros FROM sw_malla_curricular m, sw_asignatura a WHERE a.id_asignatura = m.id_asignatura AND id_curso = $id_curso");
    $registro = $db->fetch_assoc($consulta);
    $num_asignaturas = $registro["total_registros"];
    if ($num_asignaturas == 0) {
        $consulta = $db->consulta("SELECT COUNT(*) AS total_registros FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo");
        $registro = $db->fetch_assoc($consulta);
        $num_asignaturas = $registro["total_registros"];
        if ($num_asignaturas == 0) {
            $consulta = $db->consulta("SELECT COUNT(*) AS total_registros FROM sw_malla_curricular m, sw_asignatura a WHERE a.id_asignatura = m.id_asignatura AND id_curso = $id_curso AND id_tipo_asignatura = 1");
            $registro = $db->fetch_assoc($consulta);
            $num_asignaturas = $registro["total_registros"];
        }
    }
    return $num_asignaturas;
}

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

//
// $paralelo = new paralelos();
// $nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);
//

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
$nombreParalelo = $paralelo->cu_nombre . " " . $paralelo->pa_nombre . " - " . $paralelo->es_figura . " - " . $paralelo->jo_nombre;

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

// Obtener género de la autoridad
$query = $db->consulta("SELECT in_genero_rector FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_rector = $registro->in_genero_rector;

$terminacion = ($in_genero_rector == 'M') ? '' : 'A';

$genero_rector = 'RECTOR' . $terminacion;

// Obtener género del secretario
$query = $db->consulta("SELECT in_genero_secretario FROM sw_institucion WHERE id_institucion = 1");
$registro = $db->fetch_object($query);
$in_genero_secretario = $registro->in_genero_secretario;

$terminacion = ($in_genero_secretario == 'M') ? 'O' : 'A';

$genero_secretario  = 'SECRETARI' . $terminacion;

$consulta = $db->consulta("SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$id_curso = $db->fetch_object($consulta)->id_curso;

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = contarAsignaturas($id_paralelo, $id_curso);

// Determinar si es fin de subnivel
$consulta = $db->consulta("SELECT es_fin_subnivel FROM sw_curso WHERE id_curso = $id_curso");
$registro = $db->fetch_object($consulta);
$es_fin_subnivel = $registro->es_fin_subnivel;

// Determinar si es educación intensiva
$consulta = $db->consulta("SELECT es_intensivo FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
$registro = $db->fetch_object($consulta);
$es_intensivo = $registro->es_intensivo;

// Determinar si es bachillerato técnico
$consulta = $db->consulta("SELECT es_bach_tecnico FROM sw_curso WHERE id_curso = $id_curso");
$registro = $db->fetch_object($consulta);
$es_bach_tecnico = $registro->es_bach_tecnico;

switch ($numAsignaturas) {
    case 6:
        $colComportamiento = 'AC';
        break;
    case 7:
        $colComportamiento = 'AG';
        break;
    case 8:
        $colComportamiento = 'AK';
        break;
    case 9:
        if (!$es_fin_subnivel)
            $colComportamiento = 'AO';
        else
            $colComportamiento = 'AX';
        break;
    case 10:
        $colComportamiento = 'AS';
        break;
    case 11:
        $colComportamiento = 'AW';
        break;
    case 12:
        $colComportamiento = 'BA';
        break;
    case 13:
        $colComportamiento = 'AR';
        break;
    case 14:
        $colComportamiento = 'AU';
        break;
    case 15:
        if (!$es_bach_tecnico && !$es_fin_subnivel)
            $colComportamiento = 'BM';
        elseif (!$es_fin_subnivel)
            $colComportamiento = 'CC';
        else
            $colComportamiento = 'CR';
        break;

    case 16:
        if (!$es_bach_tecnico && !$es_fin_subnivel)
            $colComportamiento = 'BM';
        elseif (!$es_fin_subnivel)
            $colComportamiento = 'CH';
        break;
}

$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

$query = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$registro = $db->fetch_assoc($query);

$fecha_inicial = explode("-", $registro["pe_fecha_inicio"]);
$fecha_final = explode("-", $registro["pe_fecha_fin"]);

$nombreLargoPeriodoLectivo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];

//load the template
//load from xlsx template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO ANUAL - ";

if (!$es_fin_subnivel)
    $objPHPExcel = $objReader->load("../plantillas/" . $baseFilename . $numAsignaturas . " ASIGNATURAS.xls");
else
    $objPHPExcel = $objReader->load("../plantillas/" . $baseFilename . $numAsignaturas . " ASIGNATURAS - EFN.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
    ->setCellValue('A3', 'PERIODO LECTIVO: ' . $nombreLargoPeriodoLectivo)
    ->setCellValue('B60', $nombreRector)
    ->setCellValue('B61', $genero_rector)
	->setCellValue('S60', $nombreSecretario)
	->setCellValue('S61', $genero_secretario)
	->setCellValue('A5', $nombreParalelo);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'G', 'K', 'O', 'S', 'W', 'AA', 'AE', 'AI', 'AM', 'AQ', 'AU', 'AY', 'BC', 'BG');
$colAsignaturas2 = array('C', 'H', 'M', 'R', 'W', 'AB', 'AG', 'AL', 'AQ', 'AV', 'BA', 'BF', 'BK', 'BP', 'BU', 'BZ');
$colAsignaturas3 = array('C', 'I', 'O', 'U', 'AA', 'AG', 'AM', 'AS', 'AY', 'BE', 'BK', 'BQ', 'BW', 'CC', 'CI', 'BZ');

$colPrimerPeriodo = array('C', 'G', 'K', 'O', 'S', 'W', 'AA', 'AE', 'AI', 'AM', 'AQ', 'AU', 'AY', 'BC', 'BG');
$colPrimerPeriodo2 = array('C', 'H', 'M', 'R', 'W', 'AB', 'AG', 'AL', 'AQ', 'AV', 'BA', 'BF', 'BK', 'BP', 'BU', 'BZ');
$colPrimerPeriodo3 = array('C', 'I', 'O', 'U', 'AA', 'AG', 'AM', 'AS', 'AY', 'BE', 'BK', 'BQ', 'BW', 'CC', 'CI', 'BZ');

$colSegundoPeriodo = array('D', 'H', 'L', 'P', 'T', 'X', 'AB', 'AF', 'AJ', 'AN', 'AR', 'AV', 'AZ', 'BD', 'BH');
$colSegundoPeriodo2 = array('D', 'I', 'N', 'S', 'X', 'AC', 'AH', 'AM', 'AR', 'AW', 'BB', 'BG', 'BL', 'BQ', 'BV', 'CA');
$colSegundoPeriodo3 = array('D', 'J', 'P', 'V', 'AB', 'AH', 'AN', 'AT', 'AZ', 'BF', 'BL', 'BR', 'BX', 'CD', 'CJ', 'CA');

$colTercerPeriodo = array('E', 'I', 'M', 'Q', 'U', 'Y', 'AC', 'AG', 'AK', 'AO', 'AS', 'AW');
$colTercerPeriodo2 = array('E', 'J', 'O', 'T', 'Y', 'AD', 'AI', 'AN', 'AS', 'AX', 'BC', 'BH', 'BM', 'BR', 'BW', 'CB');
$colTercerPeriodo3 = array('E', 'K', 'Q', 'W', 'AC', 'AI', 'AO', 'AU', 'BA', 'BG', 'BM', 'BS', 'BY', 'CE', 'CK', 'CB');

$colCuartoPeriodo = array('F', 'K', 'P', 'U', 'Z', 'AE', 'AJ', 'AO', 'AT', 'AY', 'BD', 'BI', 'BN', 'BS', 'BX', 'CC');
$colCuartoPeriodo2 = array('F', 'L', 'R', 'X', 'AD', 'AJ', 'AP', 'AV', 'BB', 'BH', 'BN', 'BT', 'BZ', 'CF', 'CL', '');

$colQuintoPeriodo = array('G', 'M', 'S', 'Y', 'AE', 'AK', 'AQ', 'AW', 'BC', 'BI', 'BO', 'BU', 'CA', 'CG', 'CM', '');

$colPuntajeFinal = array('F', 'J', 'N', 'R', 'V', 'Z', 'AD', 'AH', 'AL', 'AP', 'AT', 'AX');
$colPuntajeFinal2 = array('G', 'L', 'Q', 'V', 'AA', 'AF', 'AK', 'AP', 'AU', 'AZ', 'BE', 'BJ', 'BO', 'BT', 'BY', 'CD');
$colPuntajeFinal3 = array('H', 'N', 'T', 'Z', 'AF', 'AL', 'AR', 'AX', 'BD', 'BJ', 'BP', 'BV', 'CB', 'CH', 'CN', '');




//
$filename = "result.xls";

//set the header first, so the result will be treated as an xlsx file.
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
