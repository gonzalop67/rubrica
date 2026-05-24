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

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');
setlocale(LC_ALL,"es_ES");

/* PHPExcel_IOFactory */

require_once '../php_excel/Classes/PHPExcel/IOFactory.php';
require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.dias_semana.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.periodos_lectivos.php';

// Variables de sesión a utilizar
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Variables POST pasadas desde el formulario
$id_paralelo = $_POST["cboParalelos"];
$fecha = $_POST["fecha"];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos(); // Instanciamos la clase paralelos

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "LECCIONARIO.xls";
$objPHPExcel = $objReader->load("../templates/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);

$db = new MySQL();

$id_dia_semana = date('N', strtotime($fecha));
$fecha1 = date('d', strtotime($fecha))."/".date('n', strtotime($fecha))."/".date('Y', strtotime($fecha));
$fecha2 = date('d', strtotime($fecha))."-".date('n', strtotime($fecha))."-".date('Y', strtotime($fecha));

if($id_paralelo == 0){
    $nombreCurso = ""; 
    $nombreParalelo = "";
}else{
    $nombreCurso = $paralelo->getNombreCurso($id_paralelo);
    $nombreParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
}

//el parametro w en la funcion date indica que queremos el dia de la semana
//lo devuelve en numero 0 domingo, 1 lunes,....
switch ($id_dia_semana){
    case 0: $dia_semana = "DOMINGO"; break;
    case 1: $dia_semana = "LUNES"; break;
    case 2: $dia_semana = "MARTES"; break;
    case 3: $dia_semana = "MIÉRCOLES"; break;
    case 4: $dia_semana = "JUEVES"; break;
    case 5: $dia_semana = "VIERNES"; break;
    case 6: $dia_semana = "SABADO"; break;
}

if($id_dia_semana != 0 && $id_paralelo != 0){
    // Si se han seleccionado un día y un paralelo de la listas desplegables respectivas...
    $qry = "SELECT as_shortname,
                   us_shortname,
                   hc_nombre, 
                   hc_ordinal
              FROM sw_hora_clase hc,
                   sw_horario ho,
                   sw_asignatura a,
                   sw_usuario u,
                   sw_distributivo di
             WHERE hc.id_hora_clase = ho.id_hora_clase
               AND a.id_asignatura = ho.id_asignatura
               AND a.id_asignatura = di.id_asignatura
               AND u.id_usuario = di.id_usuario
               AND ho.id_paralelo = di.id_paralelo
               AND id_dia_semana = (SELECT id_dia_semana
                                      FROM sw_dia_semana
                                     WHERE ds_ordinal = $id_dia_semana
                                       AND id_periodo_lectivo = $id_periodo_lectivo)
               AND ho.id_paralelo = $id_paralelo
             ORDER BY hc_ordinal";
    $horarios = $db->consulta($qry);
    $num_total_registros = $db->num_rows($horarios);
    if($num_total_registros > 0)
    {
        $row = 7;  // fila base
        while($horario = $db->fetch_assoc($horarios))
        {
            $Asignatura = $horario["as_shortname"];
            $Docente = $horario["us_shortname"];
            $hora = $horario["hc_nombre"];
            $ordinal = $horario["hc_ordinal"];

            if ($ordinal == 4) $row = $row + 1; // esto es para saltar la línea que tiene la leyenda de RECREO

            $objPHPExcel->getActiveSheet()->setCellValue('B'.($row + $ordinal - 1), $Asignatura)
                                          ->setCellValue('F'.($row + $ordinal - 1), $Docente);
        }
    }
}

$numDiasLaborados = $periodo_lectivo->getDiasLaborados($id_periodo_lectivo, $fecha);
$valorMes = $periodo_lectivo->obtenerValorMes($id_periodo_lectivo, date('n'));
$inspectorCurso = $db->fetch_object($db->consulta("SELECT us_shortname 
                                                     FROM sw_usuario u, 
                                                          sw_paralelo_inspector pi
                                                    WHERE u.id_usuario = pi.id_usuario
                                                      AND id_paralelo = $id_paralelo"))->us_shortname;
$vicerrector = $db->fetch_object($db->consulta("SELECT in_nom_vicerrector
                                                  FROM sw_institucion"))->in_nom_vicerrector;
$tutorCurso = $db->fetch_object($db->consulta("SELECT us_shortname 
                                                 FROM sw_usuario u, 
                                                      sw_paralelo_tutor pt
                                                WHERE u.id_usuario = pt.id_usuario
                                                  AND id_paralelo = $id_paralelo"))->us_shortname;
$esBachilleratoTecnico = $db->fetch_object($db->consulta("SELECT es_bach_tecnico
                                                            FROM sw_curso c,
                                                                 sw_paralelo p
                                                           WHERE c.id_curso = p.id_curso 
                                                             AND p.id_paralelo = $id_paralelo"))
                                            ->es_bach_tecnico;

$tituloBloque = ($esBachilleratoTecnico == 1) ? "UNIDAD DE TRABAJO" : "UNIDAD DIDACTICA";

$objPHPExcel->getActiveSheet()->setCellValue('C2', $dia_semana)
                              ->setCellValue('D2', $fecha1)
                              ->setCellValue('C3', $nombreCurso)
                              ->setCellValue('C4', $nombreParalelo)
                              ->setCellValue('C6', $tituloBloque)
                              ->setCellValue('H2', $numDiasLaborados)
                              ->setCellValue('H3', $valorMes)
                              ->setCellValue('B16', $inspectorCurso)
                              ->setCellValue('E16', $vicerrector)
                              ->setCellValue('G16', $tutorCurso);

$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setCellValue('A4', $nombreCurso . " " . $nombreParalelo)
                              ->setCellValue('C2', $dia_semana)
                              ->setCellValue('F2', $fecha1);

$filaBase = 7; // fila base en la plantilla en Excel

if($id_dia_semana != 0 && $id_paralelo != 0){
    // Si se han seleccionado un día y un paralelo de la listas desplegables respectivas...
    $qry = "SELECT CONCAT(es_apellidos,' ',es_nombres) AS nombreEstudiante
              FROM sw_estudiante e,
                   sw_estudiante_periodo_lectivo ep
             WHERE e.id_estudiante = ep.id_estudiante
               AND id_paralelo = $id_paralelo
               AND es_retirado = 'N' 
               AND activo = 1 
             ORDER BY es_apellidos, es_nombres";
    $estudiantes = $db->consulta($qry);
    $num_total_registros = $db->num_rows($estudiantes);
    if($num_total_registros > 0)
    {
        $row = $filaBase;  // fila base
        while($estudiante = $db->fetch_assoc($estudiantes))
        {
            $nombreEstudiante = $estudiante["nombreEstudiante"];
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $nombreEstudiante);
            $row++;
        }
        // Elimino las filas excedentes
        if($num_total_registros < 44)
        $objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 44 - $row);
        // Elimino la numeración en las dos filas después del último estudiante
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, " ");
        $row++;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, " ");
    }
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("../php_excel/LECCIONARIO " . $dia_semana . " " . $nombreCurso . " " . str_replace('"','',$nombreParalelo) . " " . $fecha2 . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "LECCIONARIO " . $dia_semana . " " . $nombreCurso . " " . str_replace('"','',$nombreParalelo) . " " . $fecha2 . ".xls" . "\"" );
	readfile("LECCIONARIO " . $dia_semana . " " . $nombreCurso . " " . str_replace('"','',$nombreParalelo) . " " . $fecha2 . ".xls");

?>