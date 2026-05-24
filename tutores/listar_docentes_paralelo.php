<?php
require_once '../scripts/clases/class.mysql.php';

$id_paralelo = $_POST["id_paralelo"];

$db = new MySQL();
$consulta = $db->consulta("SELECT cu.id_curso FROM sw_curso cu, sw_paralelo pa WHERE cu.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo");
$resultado = $db->fetch_object($consulta);
$id_curso =  $resultado->id_curso;
$docentes = $db->consulta("SELECT us_titulo, 
								  us_apellidos, 
								  us_nombres, 
								  as_nombre 
							 FROM sw_distributivo di,
							      sw_malla_curricular ma, 
							 	  sw_usuario u, 
								  sw_asignatura a 
							WHERE u.id_usuario = di.id_usuario 
							  AND a.id_asignatura = di.id_asignatura
							  AND ma.id_asignatura = di.id_asignatura
							  AND ma.id_curso = $id_curso 
							  AND id_paralelo = $id_paralelo
							ORDER BY ma_orden");
$num_total_docentes = $db->num_rows($docentes);
$cadena = "";
if ($num_total_docentes > 0) {
    $contador = 1;
    while ($docente = $db->fetch_object($docentes)) {
        $asignatura = $docente->as_nombre;
        $profesor = $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;
        $cadena .= "<tr>\n";
        $cadena .= "<td>$contador</td>\n";
        $cadena .= "<td>$asignatura</td>\n";
        $cadena .= "<td>$profesor</td>\n";
        $cadena .= "</tr>\n";
        $contador++;
    }
} else {
    $cadena .= "<tr>\n";
    $cadena .= "<td colspan='3' align='center'>No se han asociado Docentes a este Paralelo...</td>\n";
    $cadena .= "</tr>\n";
}
echo $cadena;
