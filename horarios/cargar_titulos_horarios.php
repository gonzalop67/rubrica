<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_periodo_lectivo = $_POST["id_periodo_lectivo"];

$consulta = $db->consulta("SELECT * FROM sw_horario_def WHERE id_periodo_lectivo = " . $id_periodo_lectivo . " ORDER BY fecha_inicial DESC");
$num_total_registros = $db->num_rows($consulta);
$cadena = "";

if ($num_total_registros > 0) {
    while ($horario = $db->fetch_assoc($consulta)) {
        $code = $horario["id_horario_def"];
        $titulo = $horario["ho_titulo"];
        $cadena .= "<option value='$code'>\n";
        $cadena .= "$titulo\n";
        $cadena .= "</option>\n";
    }
}

echo $cadena;
