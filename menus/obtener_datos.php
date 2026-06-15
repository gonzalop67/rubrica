<?php
// 1. Validar que la petición sea POST y que el ID exista
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST["id"])) {
    header('HTTP/1.1 400 Bad Request');
    exit(json_encode(["error" => "Parámetro ID requerido."]));
}

include_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

// 2. Casteo estricto a entero para prevenir Inyección SQL (SQLi)
$id = intval($_POST["id"]);

// 3. Consulta limpia utilizando JOINS modernos (ANSI SQL) en lugar de comas
$sql = "SELECT m.*, mp.id_perfil 
        FROM `sw_menu` m
        INNER JOIN `sw_menu_perfil` mp ON m.id_menu = mp.id_menu 
        WHERE m.id_menu = $id";

// 4. Ejecutar la consulta una sola vez
$consulta = $db->consulta($sql);

// 5. Validar si se encontró el registro antes de retornar el JSON
if ($db->num_rows($consulta) > 0) {
    echo json_encode($db->fetch_object($consulta));
} else {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(["error" => "No se encontraron datos para el menú especificado."]);
}
?>

