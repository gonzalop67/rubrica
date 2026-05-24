<?php
session_start();
include_once("../scripts/clases/class.mysql.php");
require_once("../scripts/clases/class.encrypter.php");

// Recepción de valores enviados mediante GET
$id_menu = $_GET["id_menu"];

$id_usuario = $_GET["id_usuario"];
$id_perfil = $_GET["id_perfil"];
$id_menu2 = $_GET["id_menu2"];

$db = new MySQL();
$qry = "SELECT * FROM sw_menu WHERE mnu_padre = $id_menu";
$consulta = $db->consulta($qry);

if ($db->num_rows($consulta) > 0) {
    $_SESSION["msg"] = "No se puede eliminar el menú porque tiene menús asociados.";
} else {
    $qry = "DELETE FROM sw_menu WHERE id_menu = $id_menu";

    $consulta = $db->consulta($qry);
    if ($consulta) {
        echo "Menú eliminado exitosamente.";
        $_SESSION["msg"] = "Menú eliminado exitosamente.";
    } else {
        echo "No se pudo eliminar el men&uacute;...Error: " . mysqli_error($db->conexion);
        $_SESSION["msg"] = "No se pudo eliminar el men&uacute;...Error: " . mysqli_error($db->conexion);
    }
}

header("Location: ../admin.php?id_usuario=" . encrypter::encrypt($id_usuario) . "&id_perfil=$id_perfil&id_menu=$id_menu2&nivel=2");
