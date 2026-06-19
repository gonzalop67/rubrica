<?php
require_once "../vendor/autoload.php";

require_once '../scripts/clases/class.mysql.php';

$db = new MySQL();

//call iofactory instead of xlsx writer
use PhpOffice\PhpSpreadsheet\IOFactory;

function truncateFloat($number, $digitos)
{
    $raiz = 10;
    $multiplicador = pow($raiz, $digitos);
    $resultado = ((int)($number * $multiplicador)) / $multiplicador;
    return $resultado;
}

function obtenerFechaCierreSupRemGracia($id_paralelo, $pe_principal, $id_periodo_lectivo)
{
    global $db;

    // Obtencion de la fecha de cierre del aporte indicado por el campo pe_principal
    $qry = $db->consulta("SELECT ac.ap_fecha_cierre 
        						   FROM sw_periodo_evaluacion p, 
             							sw_aporte_evaluacion a, 
             							sw_aporte_paralelo_cierre ac,  
             							sw_paralelo pa 
       							  WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion 
         							AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion  
         							AND pa.id_paralelo = ac.id_paralelo 
         							AND pa.id_paralelo = $id_paralelo 
         							AND id_tipo_periodo = $pe_principal 
         							AND p.id_periodo_lectivo = $id_periodo_lectivo");
    $registro = $db->fetch_assoc($qry);
    return $registro["ap_fecha_cierre"];
}

function existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $id_tipo_periodo, $id_periodo_lectivo)
{
    global $db;

    $consulta = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $id_tipo_periodo AND p.id_periodo_lectivo = $id_periodo_lectivo");
    $registro = $db->fetch_assoc($consulta);
    $id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

    $qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
    return ($db->num_rows($qry) > 0);
}

function obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
{
    global $db;

    $qry = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo");
    $registro = $db->fetch_assoc($qry);
    $id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

    $qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

    if ($qry) {
        $rubrica_estudiante = $db->fetch_assoc($qry);
        $calificacion = $rubrica_estudiante["re_calificacion"];
    } else {
        $calificacion = 0;
    }

    return $calificacion;
}

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

set_time_limit(0);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

// Clases requeridas
require_once '../scripts/clases/class.cursos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Obtener el rango de calificaciones para acceder al examen supletorio según el periodo lectivo
$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 2";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$rango_desde_supletorio = $registro->rango_desde;
$rango_hasta_supletorio = $registro->rango_hasta;

// Obtener el rango de calificaciones para acceder al examen remedial según el periodo lectivo
$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 3";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
if (!empty($registro)) {
    $rango_desde_remedial = $registro->rango_desde;
    $rango_hasta_remedial = $registro->rango_hasta;
}

// Obtener la nota mínima para aprobar el periodo lectivo
$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$nota_aprobacion = $registro->pe_nota_aprobacion;

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

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nomParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

$consulta = $db->consulta("SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$resultado = $db->fetch_object($consulta);
$id_curso = $resultado->id_curso;

$cursos = new cursos();
//$bol_proyectos = $cursos->obtenerBolProyectos($id_curso);

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo, $id_curso, 1);

switch ($numAsignaturas) {
    case 6:
        $colPromedioGeneral = 'V';
        $colProyectoEscolar = 'W';
        $colComportamiento = 'W';
        $colObservaciones = 'X';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 7:
        $colPromedioGeneral = 'Y';
        $colProyectoEscolar = 'Z';
        $colComportamiento = 'AA';
        $colObservaciones = 'AB';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 8:
        $colPromedioGeneral = 'AB';
        $colProyectoEscolar = 'Z';
        $colComportamiento = 'AC';
        $colObservaciones = 'AD';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 9:
        $colPromedioGeneral = 'AE';
        $colProyectoEscolar = 'AF';
        $colComportamiento = 'AF';
        $colObservaciones = 'AG';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 10:
        $colPromedioGeneral = 'AH';
        $colProyectoEscolar = 'AF';
        $colComportamiento = 'AI';
        $colObservaciones = 'AJ';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 11:
        $colPromedioGeneral = 'AK';
        $colProyectoEscolar = 'AF';
        $colComportamiento = 'AL';
        $colObservaciones = 'AM';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 12:
        $colPromedioGeneral = 'AN';
        $colProyectoEscolar = 'AO';
        $colComportamiento = 'AO';
        $colObservaciones = 'AP';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 13:
        $colPromedioGeneral = 'AQ';
        $colProyectoEscolar = 'AR';
        $colComportamiento = 'AR';
        $colObservaciones = 'AS';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        break;
    case 14:
        $colPromedioGeneral = 'AT';
        $colProyectoEscolar = 'AU';
        $colComportamiento = 'AU';
        $colObservaciones = 'AV';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2);
        break;
    case 15:
        $colPromedioGeneral = 'AW';
        $colProyectoEscolar = 'AX';
        $colComportamiento = 'AX';
        $colObservaciones = 'AY';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2);
        break;
    case 16:
        $colPromedioGeneral = 'AZ';
        $colProyectoEscolar = 'BA';
        $colComportamiento = 'BA';
        $colObservaciones = 'BB';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2);
        break;
}

//$objReader = PHPExcel_IOFactory::createReader('Excel5');

//load the template
$objReader = IOFactory::createReader('Xls');

$baseFilename = "CUADRO FINAL - ";

// print_r($baseFilename . $numAsignaturas . " ASIGNATURAS.xls"); die();

$objPHPExcel = $objReader->load("../templates/" . $baseFilename . $numAsignaturas . " ASIGNATURAS.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('D1', $nombreInstitucion)
    ->setCellValue('D2', $nombreCurso)
    ->setCellValue('O4', $nombrePeriodoLectivo)
    ->setCellValue('S4', 'PARALELO ' . $nomParalelo)
    ->setCellValue('D91', $nombreRector)
    ->setCellValue('D92', $genero_rector)
    ->setCellValue('O91', $nombreSecretario)
    ->setCellValue('O92', $genero_secretario);

// Renombrar la hoja de calculo
$objPHPExcel->getActiveSheet()->setTitle('CUADRO FINAL Y SUPLETORIOS');

// Vectores de configuracion para las columnas
$colAsignaturas = array('D', 'G', 'J', 'M', 'P', 'S', 'V', 'Y', 'AB', 'AE', 'AH', 'AK', 'AN', 'AQ', 'AT', 'AW');
$colSupletorio = array('E', 'H', 'K', 'N', 'Q', 'T', 'W', 'Z', 'AC', 'AF', 'AI', 'AL', 'AO', 'AR', 'AU', 'AX');
$colPromedioFinal = array('F', 'I', 'L', 'O', 'R', 'U', 'X', 'AA', 'AD', 'AG', 'AJ', 'AM', 'AP', 'AS', 'AV', 'AW');

// Aquí va el código para calcular los promedios anuales, supletorios y finales de cada estudiante

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, dg_abreviatura, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p, sw_def_genero dg WHERE dg.id_def_genero = e.id_def_genero AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND activo = 1 ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
    $row = 7;
    $filaBase = $row; // fila base 
    while ($estudiante = $db->fetch_assoc($estudiantes)) {
        $id_estudiante = $estudiante["id_estudiante"];
        $apellidos = $estudiante["es_apellidos"];
        $nombres = $estudiante["es_nombres"];
        $retirado = $estudiante["es_retirado"];

        $genero = $estudiante["dg_abreviatura"];
        $terminacion = ($genero == "M") ? "" : "A";
        $observacion = "DESERTOR" . $terminacion;

        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $apellidos . " " . $nombres);

        $asignaturas = $db->consulta("SELECT as_abreviatura, 
                                             a.id_asignatura,
                                             a.id_tipo_asignatura,  
                                             as_nombre 
                                        FROM sw_asignatura a, 
                                             sw_asignatura_curso ac, 
                                             sw_paralelo p 
                                       WHERE a.id_asignatura = ac.id_asignatura 
                                         AND p.id_curso = ac.id_curso 
                                         AND a.id_tipo_asignatura = 1 
                                         AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
        $total_asignaturas = $db->num_rows($asignaturas);
        if ($total_asignaturas > 0) {
            $contAsignatura = 0;
            $rowAsignatura = 5;
            // $sumaPromedios = 0;
            $contAprobadas = 0;
            $num_supletorios = 0;
            $num_remediales = 0;
            $sumaComportamiento = 0;
            $contNoAprobadas = 0;
            while ($asignatura = $db->fetch_assoc($asignaturas)) {
                // Aqui proceso los promedios de cada asignatura
                $id_asignatura = $asignatura["id_asignatura"];
                $nombreAsignatura = $asignatura["as_nombre"];

                $objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, $nombreAsignatura);

                $query = $db->consulta("SELECT calcular_promedio_final($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");
                $registro = $db->fetch_assoc($query);
                $promedio_final = $registro["promedio_final"];

                if ($retirado != 'S') {
                    $query = $db->consulta("SELECT calcular_promedio_periodo_lectivo($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
                    $calificacion = $db->fetch_assoc($query);
                    $promedio_anual = $calificacion["promedio"];

                    $promedio_anual_truncado = $promedio_anual == 0 ? "" : substr($promedio_anual, 0, strpos($promedio_anual, '.') + 3);

                    // $promedio_anual_truncado = truncateFloat($promedio_anual, 2);

                    $nota_anual_truncada = $promedio_anual_truncado == 0 ? "" : substr($promedio_anual_truncado, 0, strpos($promedio_anual_truncado, '.') + 3);

                    $objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $nota_anual_truncada);

                    $query = $db->consulta("SELECT pe_anio_inicio FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
                    $pe_anio_inicio = $db->fetch_object($query)->pe_anio_inicio;

                    if ($promedio_anual_truncado >= $nota_aprobacion) {
                        $contAprobadas++;
                    } else {
                        if ($pe_anio_inicio < 2023) {
                            if ($promedio_anual >= $rango_desde_supletorio && $promedio_anual <= $rango_hasta_supletorio) {
                                if (existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
                                    $supletorio = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);

                                    if ($supletorio >= $nota_aprobacion) {
                                        $contAprobadas++;
                                    }

                                    $supletorio = $supletorio == 0 ? "" : substr($supletorio, 0, strpos($supletorio, '.') + 3);

                                    $objPHPExcel->getActiveSheet()->setCellValue($colSupletorio[$contAsignatura] . $row, $supletorio);
                                } else {
                                    $num_supletorios++;
                                }
                            } else if ($promedio_anual >= $rango_desde_remedial && $promedio_anual <= $rango_hasta_remedial) {
                                $objPHPExcel->getActiveSheet()->setCellValue($colSupletorio[$contAsignatura] . $row, "R");
                                $num_remediales++;
                            } else {
                                $contNoAprobadas++;
                            }
                        } else {
                            if ($promedio_anual >= $rango_desde_supletorio && $promedio_anual_truncado <= $rango_hasta_supletorio) {
                                if ($db->consulta("SELECT existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)")) {
                                    $consulta = $db->consulta("SELECT obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo) AS re_calificacion");
                                    $registro = $db->fetch_object($consulta);
                                    $supletorio = $registro->re_calificacion;

                                    if ($supletorio >= $nota_aprobacion) {
                                        $contAprobadas++;
                                    } else {
                                        $contNoAprobadas++;
                                    }

                                    $supletorio = $supletorio == 0 ? "" : substr($supletorio, 0, strpos($supletorio, '.') + 3);

                                    $objPHPExcel->getActiveSheet()->setCellValue($colSupletorio[$contAsignatura] . $row, $supletorio);
                                } else {
                                    $num_supletorios++;
                                }
                            } else {
                                $contNoAprobadas++;
                            }
                        }
                    }
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($colPromedioGeneral . $row, "");
                    $objPHPExcel->getActiveSheet()->setCellValue($colComportamiento . $row, "");
                }

                $promedio_final = $promedio_final == 0 ? "" : substr($promedio_final, 0, strpos($promedio_final, '.') + 3);

                if ($retirado != 'S') {
                    $objPHPExcel->getActiveSheet()->setCellValue($colPromedioFinal[$contAsignatura] . $row, $promedio_final);
                }

                $contAsignatura++;
            }

            // Aqui va la leyenda de Observaciones de acuerdo al número de supletorios y remediales
            if ($retirado != 'S') {
                if ($contNoAprobadas > 0) {
                    $observaciones = "NO APRUEBA";
                } else {
                    if ($contAprobadas == $contAsignatura) {
                        $observaciones = "APROBADO";
                    } else if ($num_supletorios > 0 && $num_remediales == 0) {
                        $observaciones = "SUPLETORIO";
                    } else if ($num_supletorios == 0 && $num_remediales > 0) {
                        $observaciones = "REMEDIAL";
                    } else if ($num_supletorios > 0 && $num_remediales > 0) {
                        $observaciones = "SUPLE./REME.";
                    } else {
                        $observaciones = $observacion;
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValue($colObservaciones . $row, $observaciones);
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue($colObservaciones . $row, $observacion);
            }

            // Calculo del comportamiento general de todas las materias
            // Determinar quien ingresa el comportamiento
            $consulta = $db->consulta("SELECT quien_inserta_comp_id FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
            $registro = $db->fetch_object($consulta);
            $quien_inserta_comp = $registro->quien_inserta_comp_id;

            if ($quien_inserta_comp == 2) {
                $periodos_eval_comp = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");

                $suma_comp_periodos = 0;
                $contador_periodos = 0;

                while ($periodo = $db->fetch_object($periodos_eval_comp)) {
                    $id_periodo_evaluacion = $periodo->id_periodo_evaluacion;

                    $qry = "SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion AND id_tipo_aporte = 1";
                    $aportes_eval_comp = $db->consulta($qry);

                    $suma_comp_aportes = 0;
                    $contador_aportes = 0;

                    while ($aporte = $db->fetch_object($aportes_eval_comp)) {
                        $id_aporte_evaluacion = $aporte->id_aporte_evaluacion;

                        $asignaturas_eval_comp = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");

                        $suma_comp_asignatura = 0;
                        $contador_asignaturas = 0;

                        while ($asignatura = $db->fetch_object($asignaturas_eval_comp)) {
                            $id_asignatura = $asignatura->id_asignatura;

                            $qry = "SELECT co_calificacion FROM sw_calificacion_comportamiento WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion AND $id_asignatura";

                            $registro = $db->consulta($qry);
                            $num_registros = $db->num_rows($registro);
                            $registro = $db->fetch_object($registro);

                            $co_calificacion = $num_registros == 0 ? 0 : $registro->co_calificacion;
                            $suma_comp_asignatura += $co_calificacion;

                            $contador_asignaturas++;
                        }

                        $suma_comp_asignatura = ceil($suma_comp_asignatura / $contador_asignaturas);
                        $suma_comp_aportes += $suma_comp_asignatura;
                        $contador_aportes++;
                    }

                    $suma_comp_aportes = ceil($suma_comp_aportes / $contador_aportes);
                    $suma_comp_periodos += $suma_comp_aportes;
                    $contador_periodos++;
                }

                $suma_comp_periodos = ceil($suma_comp_periodos / $contador_periodos);
            }

            $query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $suma_comp_periodos");
            $registro = $db->fetch_object($query);
            $comportamiento = $registro->ec_equivalencia;

            if ($retirado != 'S') {
                $objPHPExcel->getActiveSheet()->setCellValue($colComportamiento . $row, $comportamiento);
            }
        }

        $row++;
    }

    $objPHPExcel->getActiveSheet()
        ->getColumnDimension('C')
        ->setAutoSize(true);

    // Elimino las filas excedentes
    if ($num_total_estudiantes < 80)
        $objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 80 - $row);
}

$filename = $baseFilename . str_replace('"', '', $nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls";

header("Content-type: application/x-msexcel");

//make it an attachment so we can define filename
header('Content-Disposition: attachment;filename="' . $filename . '"');

//create IOFactory object
$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
//save into php output
$writer->save('php://output');
