<?php
sleep(1);
session_start();

include_once("../scripts/clases/class.mysql.php");
// Recepción de valores enviados mediante POST
$id_menu = $_POST["id_menu"];
$id_perfil = $_POST["id_perfil"];
$mnu_texto = $_POST["mnu_texto"];
$mnu_enlace = $_POST["mnu_enlace"];
$mnu_icono = $_POST["mnu_icono"];
$mnu_publicado = $_POST["mnu_publicado"];

$db = new MySQL();
$qry = "UPDATE sw_menu SET mnu_texto = '$mnu_texto', mnu_enlace = '$mnu_enlace', mnu_icono = '$mnu_icono', mnu_publicado = $mnu_publicado WHERE id_menu = $id_menu";

$consulta = $db->consulta($qry);
if ($consulta) {
    echo "Menú actualizado exitosamente.";
    $_SESSION["msg"] = "Menú actualizado exitosamente.";
} else {
    echo "No se pudo actualizar el men&uacute;...Error: " . mysqli_error($db->conexion);
    $_SESSION["msg"] = "No se pudo actualizar el men&uacute;...Error: " . mysqli_error($db->conexion);
}
