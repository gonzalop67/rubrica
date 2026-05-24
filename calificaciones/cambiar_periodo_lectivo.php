<?php
require_once("../scripts/clases/class.mysql.php");
require_once("../scripts/clases/class.encrypter.php");

$db = new MySQL();

// Variables POST
$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
$id_usuario = $_POST["id_usuario"];
$id_perfil = $_POST["id_perfil"];

// Obtengo el id_modalidad correspondiente al id_periodo_lectivo
$consulta = $db->consulta("SELECT id_modalidad FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
$modalidad = $db->fetch_object($consulta);
$id_modalidad = $modalidad->id_modalidad;

session_start();

$_SESSION['id_periodo_lectivo'] = $id_periodo_lectivo;
$_SESSION['id_modalidad'] = $id_modalidad;

$datos = [
    'id_usuario' => encrypter::encrypt($id_usuario),
    'id_perfil' => $id_perfil
];

echo json_encode($datos);
