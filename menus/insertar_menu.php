<?php
session_start();
include_once("../scripts/clases/class.mysql.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Se esperaba una petición POST.");
}

$db = new MySQL();

// Ahora sí llegará correctamente desde el formulario
$id_perfil     = $_POST["id_perfil"] ?? null; 
$mnu_texto     = $_POST["nombre"] ?? null;
$mnu_enlace    = $_POST["url"] ?? null;
$mnu_icono     = $_POST["icono"] ?? null;
$mnu_padre     = !empty($_POST["padre_id"]) ? $_POST["padre_id"] : 0;
$mnu_publicado = 1; 

if (!$id_perfil || !$mnu_texto || !$mnu_enlace) {
    die("Error: Faltan datos mandatorios (Perfil, Nombre o Enlace).");
}

// 3. Sanitización de datos con tu método de clase
$id_perfil     = $db->filtrar($id_perfil);
$mnu_texto     = $db->filtrar($mnu_texto);
$mnu_enlace    = $db->filtrar($mnu_enlace);
$mnu_publicado = $db->filtrar($mnu_publicado);
$mnu_icono     = $db->filtrar($mnu_icono);
$mnu_padre     = $db->filtrar($mnu_padre);

// Tu consulta ahora guardará el mnu_padre real seleccionado de la lista dinámica
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
$qry .= "'" . $mnu_publicado . "', 0, '" . $mnu_padre . "', '" . $mnu_icono . "')";

try {
    $consulta = $db->consulta($qry);

    // Obtener el ID generado usando el método que agregamos en el paso anterior
    $max_id_menu = $db->ultimo_id(); 

    if (!$max_id_menu) {
        throw new Exception("No se pudo obtener el ID del menú insertado.");
    }

    // Insertar en la tabla relacional de perfiles
    $qry = "INSERT INTO sw_menu_perfil SET id_perfil = '$id_perfil', id_menu = '$max_id_menu'";
    $consulta = $db->consulta($qry);

    echo "Menú insertado exitosamente.";
    $_SESSION["msg"] = "Menú insertado exitosamente.";
} catch (Exception $e) {
    echo "No se pudo insertar el men&uacute;...Error: " . $e->getMessage();
    $_SESSION["msg"] = "No se pudo insertar el men&uacute;...Error: " . $e->getMessage();
}
