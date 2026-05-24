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

set_time_limit(0);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

/* PHPExcel_IOFactory */

require_once '../php_excel/Classes/PHPExcel/IOFactory.php';
require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.cursos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.estudiantes.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
$nomParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

$cursos = new cursos();

$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo, $id_curso);

switch ($numAsignaturas) {
    case 6: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1); break;
    case 7: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1); break;
    case 8: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1); break;
    case 9: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1); break;
    case 10: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 1); break;
    case 12: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2); break;
    case 13: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2); break;
    case 14: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2); break;
    case 15: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2); break;
    case 16: $nombreCurso = $cursos->obtenerNombreCurso($id_curso, 2); break;
}

$objReader = PHPExcel_IOFactory::createReader('Excel5');

$objPHPExcel = $objReader->load("../templates/NOMINA MATRICULADOS.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A7', $nombreInstitucion)
                              ->setCellValue('A8', 'NOMINA DE PROMOVIDOS')
                              ->setCellValue('A10', $nombreCurso)
                              ->setCellValue('C9', $nombrePeriodoLectivo)
                              ->setCellValue('D11', 'PARALELO '.$nomParalelo)
                              ->setCellValue('B66', $nombreRector)
                              ->setCellValue('D66', $nombreSecretario);

// Renombrar la hoja de calculo
$objPHPExcel->getActiveSheet()->setTitle('NOMINA DE PROMOVIDOS');

$estudiante_obj = new estudiantes();

// Aquí va el código para desplegar la nomina de estudiantes

$db = new MySQL();
$estudiantes = $db->consulta("SELECT e.id_estudiante, es_cedula, es_apellidos, es_nombres, es_retirado, es_nro_matricula FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND activo = 1 ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
    $row = 13; // fila base
    $filaBase = $row; 
    while ($estudiante = $db->fetch_assoc($estudiantes)) {
        $id_estudiante = $estudiante["id_estudiante"];
        $apellidos = $estudiante["es_apellidos"];
        $nombres = $estudiante["es_nombres"];
        $retirado = $estudiante["es_retirado"];
        $nro_matricula = $estudiante["es_nro_matricula"];
        $cedula = $estudiante["es_cedula"];

        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $apellidos . " " . $nombres);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $cedula);
        
        // Aca desplego la observacion de retirado si fuere el caso
        
        if ($retirado == "S") {
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "DESERTOR");
        }else{
            $aprueba = $estudiante_obj->es_promocionado($id_estudiante, $id_periodo_lectivo, $id_paralelo) ? 'APRUEBA' : 'NO APRUEBA';
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $aprueba);
        }
    
        $row++;
    }

    // Elimino las filas excedentes
	if($num_total_estudiantes < 50)
        $objPHPExcel->getActiveSheet()->removeRow($row, $filaBase + 50 - $row);

}

// fin del código para calcular los promedios anuales, supletorios y finales de cada estudiante

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("NOMINA DE PROMOVIDOS DE " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
header ("Cache-Control: no-cache, must-revalidate");  
header ("Pragma: no-cache");  
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"" . "NOMINA DE PROMOVIDOS DE " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls" . "\"" );
readfile("NOMINA DE PROMOVIDOS DE " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

?>