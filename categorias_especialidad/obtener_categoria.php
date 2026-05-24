<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_categoria_especialidad WHERE id_categoria = " . $_POST['id']);
echo json_encode($db->fetch_assoc($consulta));