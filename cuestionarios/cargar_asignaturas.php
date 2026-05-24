<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
$id_usuario = $_POST['id_usuario'];

$consulta = $db->consulta("SELECT DISTINCT(a.id_asignatura),
                                 as_nombre
                            FROM sw_asignatura a,
                                 sw_distributivo d
                           WHERE a.id_asignatura = d.id_asignatura
                             AND d.id_usuario = $id_usuario
                             AND d.id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY as_nombre");

$num_total_registros = $db->num_rows($consulta);

$cadena = "";
if ($num_total_registros > 0) {
    while ($asignatura = $db->fetch_assoc($consulta)) {
        $code = $asignatura["id_asignatura"];
        $name = $asignatura["as_nombre"];
        $cadena .= "<option value=\"$code\">$name</option>";
    }
}

echo $cadena;
