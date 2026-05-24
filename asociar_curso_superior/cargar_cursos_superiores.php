<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();
$consulta = $db->consulta("SELECT * FROM sw_curso_superior ORDER BY id_curso_superior ASC");
$num_total_registros = $db->num_rows($consulta);
$cadena = "";
if ($num_total_registros > 0) {
    while ($cursos = $db->fetch_assoc($consulta)) {
        $code = $cursos["id_curso_superior"];
        $name = $cursos["cs_nombre"];
        $cadena .= "<option value=\"$code\">$name</option>";
    }
}
echo $cadena;
