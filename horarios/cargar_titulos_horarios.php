<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

session_start();

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$sql = "SELECT * FROM sw_horario_def WHERE id_periodo_lectivo = " . $id_periodo_lectivo . " ORDER BY fecha_inicial DESC";

$consulta = $db->consulta($sql);
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
