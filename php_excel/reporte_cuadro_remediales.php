<?php
/*
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

function truncar($numero, $digitos)
{
    $truncar = pow(10, $digitos);
    return intval($numero * $truncar) / $truncar;
}

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

set_time_limit(0);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

/* PHPExcel_IOFactory */

require_once '../php_excel/Classes/PHPExcel/IOFactory.php';
require_once '../funciones/funciones_sitio.php';
require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.cursos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.funciones.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';

$funciones = new funciones();

// Variables enviadas mediante POST	
$id_paralelo = $_POST["cboParalelos"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

$db = new MySQL();

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

$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
$nomParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

$cursos = new cursos();
$bol_proyectos = $cursos->obtenerBolProyectos($id_curso);

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo, $id_curso);

switch ($numAsignaturas) {
    case 6:
        $colPromedioGeneral = 'V';
        $colProyectoEscolar = 'W';
        $colComportamiento = 'X';
        $colObservaciones = 'Y';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        $colParalelo = 'R';
        break;
    case 7:
        $colPromedioGeneral = 'Y';
        $colProyectoEscolar = 'Z';
        $colComportamiento = 'AA';
        $colObservaciones = 'AB';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        $colParalelo = 'S';
        break;
    case 8:
        $colPromedioGeneral = 'AB';
        $colProyectoEscolar = 'Z';
        $colComportamiento = 'AC';
        $colObservaciones = 'AD';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        $colParalelo = 'S';
        break;
    case 9:
        $colPromedioGeneral = 'AE';
        $colProyectoEscolar = 'AF';
        $colComportamiento = 'AF';
        $colObservaciones = 'AG';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        $colParalelo = 'S';
        break;
    case 10:
        $colPromedioGeneral = 'AH';
        $colProyectoEscolar = 'AI';
        $colComportamiento = 'AI';
        $colObservaciones = 'AJ';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        $colParalelo = 'S';
        break;
    case 12:
        $colPromedioGeneral = 'AN';
        $colProyectoEscolar = 'AO';
        $colComportamiento = 'AO';
        $colObservaciones = 'AP';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1);
        $colParalelo = 'S';
        break;
    case 13:
        $colPromedioGeneral = 'AQ';
        $colProyectoEscolar = 'AR';
        $colComportamiento = 'AR';
        $colObservaciones = 'AS';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2);
        $colParalelo = 'S';
        break;
    case 14:
        $colPromedioGeneral = 'AT';
        $colProyectoEscolar = 'AU';
        $colComportamiento = 'AU';
        $colObservaciones = 'AV';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2);
        $colParalelo = 'S';
        break;
    case 15:
        $colPromedioGeneral = 'AW';
        $colProyectoEscolar = 'AX';
        $colComportamiento = 'AX';
        $colObservaciones = 'AY';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2);
        $colParalelo = 'S';
        break;
    case 16:
        $colPromedioGeneral = 'AZ';
        $colProyectoEscolar = 'BA';
        $colComportamiento = 'BA';
        $colObservaciones = 'BB';
        $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2);
        $colParalelo = 'S';
        break;
}

$objReader = PHPExcel_IOFactory::createReader('Excel5');

$baseFilename = "CUADRO REMEDIAL - ";

$objPHPExcel = $objReader->load("../templates/" . $baseFilename . $numAsignaturas . " ASIGNATURAS.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('D1', $nombreInstitucion)
    ->setCellValue('D2', $nombreCurso)
    ->setCellValue('O4', $nombrePeriodoLectivo)
    ->setCellValue($colParalelo . '4', 'PARALELO ' . $nomParalelo)
    ->setCellValue('D61', $nombreRector)
    ->setCellValue('D62', $genero_rector)
    ->setCellValue('O61', $nombreSecretario)
    ->setCellValue('O62', $genero_secretario);

// Renombrar la hoja de calculo
$objPHPExcel->getActiveSheet()->setTitle('CUADRO REMEDIALES');

// Vectores de configuracion para las columnas
$colAsignaturas = array('D', 'G', 'J', 'M', 'P', 'S', 'V', 'Y', 'AB', 'AE', 'AH', 'AK', 'AN', 'AQ', 'AT', 'AW');
$colSupletorio = array('E', 'H', 'K', 'N', 'Q', 'T', 'W', 'Z', 'AC', 'AF', 'AI', 'AL', 'AO', 'AR', 'AU', 'AX');
$colPromedioFinal = array('F', 'I', 'L', 'O', 'R', 'U', 'X', 'AA', 'AD', 'AG', 'AJ', 'AM', 'AP', 'AS', 'AV', 'AY');

// Aquí va el código para calcular los promedios anuales, supletorios y finales de cada estudiante

$estudiantes = $db->consulta("SELECT e.id_estudiante, 
                                     es_apellidos, 
                                     es_nombres 
                                FROM sw_estudiante e, 
                                     sw_estudiante_periodo_lectivo p 
                               WHERE e.id_estudiante = p.id_estudiante 
                                 AND p.id_paralelo = $id_paralelo 
                                 AND es_retirado = 'N' 
                                 AND activo = 1 
                            ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
    $row = 7;
    $filaBase = $row; // fila base 
    while ($estudiante = $db->fetch_assoc($estudiantes)) {
        $id_estudiante = $estudiante["id_estudiante"];
        $apellidos = $estudiante["es_apellidos"];
        $nombres = $estudiante["es_nombres"];

        $query = $db->consulta("SELECT contar_remediales($id_periodo_lectivo, $id_estudiante, $id_paralelo) AS total");
        $resultado = $db->fetch_assoc($query);
        $total_remediales = $resultado["total"];

        // Aca voy a desplegar el registro solo si el estudiante se ha quedado a algun remedial
        if ($total_remediales > 0) {
            $asignaturas = $db->consulta("SELECT as_abreviatura, 
                                                 a.id_asignatura, 
                                                 as_nombre 
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
                $rowAsignatura = 5;
                $contAsignatura = 0;
                $sumaPromedios = 0;
                $num_asignaturas_aprobadas = 0;
                while ($asignatura = $db->fetch_assoc($asignaturas)) {
                    // Aqui proceso los promedios de cada asignatura
                    $id_asignatura = $asignatura["id_asignatura"];
                    $nombreAsignatura = $asignatura["as_nombre"];

                    $objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $rowAsignatura, $nombreAsignatura);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $apellidos . " " . $nombres);

                    $query = $db->consulta("SELECT calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
                    $calificacion = $db->fetch_assoc($query);
                    $promedio_anual = $calificacion["promedio"];

                    $promedio_anual_truncado = truncar($promedio_anual, 2);

                    $objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, $promedio_anual_truncado);

                    if ($promedio_anual_truncado >= 7) {
                        $num_asignaturas_aprobadas++;
                    } else if ($promedio_anual_truncado < 5) {
                        // Aqui obtengo la calificación del examen remedial
                        $remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                        if ($remedial == 0) {
                            $objPHPExcel->getActiveSheet()->setCellValue($colSupletorio[$contAsignatura] . $row, "SE");
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue($colSupletorio[$contAsignatura] . $row, $remedial);
                            if ($remedial >= 7) {
                                $num_asignaturas_aprobadas++;
                                $promedio_anual_truncado = 7;
                            }
                        }
                    } else if ($promedio_anual_truncado < 7) {
                        $supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
                        if ($supletorio < 7) {
                            $remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                            if ($remedial == 0) {
                                $objPHPExcel->getActiveSheet()->setCellValue($colSupletorio[$contAsignatura] . $row, "SE");
                            } else {
                                $objPHPExcel->getActiveSheet()->setCellValue($colSupletorio[$contAsignatura] . $row, $remedial);
                                if ($remedial >= 7) {
                                    $num_asignaturas_aprobadas++;
                                    $promedio_anual_truncado = 7;
                                }
                            }
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura] . $row, 7);
                            $objPHPExcel->getActiveSheet()->setCellValue($colPromedioFinal[$contAsignatura] . $row, 7);
                            $promedio_anual_truncado = 7;
                            $num_asignaturas_aprobadas++;
                        }
                    }

                    $sumaPromedios += $promedio_anual_truncado;

                    if ($bol_proyectos == 0) {
                        // Aqui obtengo el id_club del estudiante
                        $qry = $db->consulta("SELECT id_club FROM sw_estudiante_club WHERE id_estudiante = $id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo");
                        $total_registros = $db->num_rows($qry);
                        if ($total_registros > 0) {
                            $registro = $db->fetch_assoc($qry);
                            $id_club = $registro["id_club"];

                            // Aca calculo el promedio anual de proyectos escolares al que pertenece el estudiante
                            $query = $db->consulta("SELECT calcular_promedio_anual_proyectos($id_periodo_lectivo, $id_estudiante) AS promedio");
                            $calificacion = $db->fetch_assoc($query);
                            $promedio_anual = $calificacion["promedio"];

                            // Aqui obtengo la equivalencia cualitativa para el promedio quimestral de clubes
                            $qry = $db->consulta("SELECT ec_equivalencia FROM sw_escala_proyectos WHERE ec_nota_minima <= $promedio_anual AND ec_nota_maxima >= $promedio_anual");
                            $registro = $db->fetch_assoc($qry);
                            $equivalencia = $registro["ec_equivalencia"];

                            $objPHPExcel->getActiveSheet()->setCellValue($colProyectoEscolar . $row, $equivalencia);
                        }
                    }
                    $contAsignatura++;
                } //while ($asignatura = $db->fetch_assoc($asignaturas))
                if ($num_asignaturas_aprobadas == $numAsignaturas) {
                    $objPHPExcel->getActiveSheet()->setCellValue($colObservaciones . $row, "APRUEBA");
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($colObservaciones . $row, "NO APRUEBA");
                }
            } //if ($total_asignaturas > 0)

            $promedio_general = truncar($sumaPromedios / $total_asignaturas, 2);
            $objPHPExcel->getActiveSheet()->setCellValue($colPromedioGeneral . $row, $promedio_general);

            // Aca se calcula el promedio anual de comportamiento
            $query = $db->consulta("SELECT calcular_comp_final($id_periodo_lectivo, $id_estudiante, $id_paralelo) AS comportamiento");
            $calificacion = $db->fetch_assoc($query);
            $comportamiento = ceil($calificacion["comportamiento"]);

            $query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $comportamiento");
            $equivalencia = $db->fetch_assoc($query);
            $promedio_cualitativo = $equivalencia["ec_equivalencia"];

            $objPHPExcel->getActiveSheet()->setCellValue($colComportamiento . $row, $promedio_cualitativo);

            $row++;
        } //if($total_remediales > 0)
    } //while ($estudiante = $db->fetch_assoc($estudiantes))

    $objPHPExcel->getActiveSheet()
        ->getColumnDimension('C')
        ->setAutoSize(true);

    // Elimino las filas excedentes
    if ($num_total_estudiantes < 50)
        $objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);
} //if ($num_total_estudiantes > 0)

// fin del código para calcular los promedios anuales, supletorios y finales de cada estudiante

// aqui consulto el nombre del rector (a) y del secretario (a)
$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

$objPHPExcel->getActiveSheet()->setCellValue('D61', $nombreRector);
$objPHPExcel->getActiveSheet()->setCellValue('O61', $nombreSecretario);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($baseFilename . " " . str_replace('"', '', $nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=\"" . $baseFilename . " " . str_replace('"', '', $nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls" . "\"");
readfile($baseFilename . " " . str_replace('"', '', $nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");
