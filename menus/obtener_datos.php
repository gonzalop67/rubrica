<?php
include_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
//Obtener variables enviadas mediante POST
$id = $_POST["id"];
$consulta = $db->consulta("SELECT m.*, mp.id_perfil FROM sw_menu m, sw_menu_perfil mp WHERE m.id_menu = mp.id_menu AND m.id_menu = $id");
echo json_encode($db->fetch_object($consulta));
