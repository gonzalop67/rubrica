<?php
require_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_usuario_perfil WHERE id_usuario = $_POST[id_usuario]");
$records = [];

while ($row = $db->fetch_object($consulta)) {
    array_push($records, $row->id_perfil);
}

echo json_encode($records);