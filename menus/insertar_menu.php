<?php
sleep(1);
session_start();
include_once("../scripts/clases/class.mysql.php");
// Recepción de valores enviados mediante POST
$id_perfil = $_POST["id_perfil"];
$mnu_texto = $_POST["mnu_texto"];
$mnu_enlace = $_POST["mnu_enlace"];
$mnu_publicado = $_POST["mnu_publicado"];
$mnu_icono = $_POST["mnu_icono"];
$db = new MySQL();
// Calcular el máximo id_menu
$qry = "INSERT INTO sw_menu (
mnu_texto, 
mnu_enlace, 
mnu_link, 
mnu_nivel, 
mnu_publicado, 
mnu_orden, 
mnu_padre, 
mnu_icono) VALUES (";
$qry .= "'" . $mnu_texto . "', ";
$qry .= "'" . $mnu_enlace . "', ";
$qry .= "'', 1, ";
$qry .= $mnu_publicado . ", 0, 0, '" . $mnu_icono . "')";

try {
    $consulta = $db->consulta($qry);

    $qry = "SELECT MAX(id_menu) AS max_id_menu FROM sw_menu";
    $registro = $db->consulta($qry);
    $max_id_menu = $db->fetch_object($registro)->max_id_menu;

    $qry = "INSERT INTO sw_menu_perfil SET id_perfil = $id_perfil, id_menu = $max_id_menu";
    $consulta = $db->consulta($qry);

    echo "Menú insertado exitosamente.";
    $_SESSION["msg"] = "Menú insertado exitosamente.";
} catch (Exception $e) {
    echo "No se pudo insertar el men&uacute;...Error: " . $e->getMessage();
    $_SESSION["msg"] = "No se pudo insertar el men&uacute;...Error: " . $e->getMessage();
}
