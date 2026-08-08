<?php
// Define que la respuesta siempre será un JSON en formato UTF-8
header('Content-Type: application/json; charset=utf-8');

require_once("../scripts/clases/class.mysql.php");
$db = new MySQL;

// 1. Leer desde $_GET
// 2. Seguridad: Forzar que sea un número entero para evitar Inyección SQL
$id_oferta = isset($_GET['id_oferta']) ? (int)$_GET['id_oferta'] : 0;

// 3. Consulta SQL con alias estandarizados para el JavaScript
if ($id_oferta > 0) {
    $sql = "SELECT id_periodo_lectivo AS id, 
                   nombre, 
                   pe_fecha_inicio AS fecha_inicio, 
                   pe_fecha_fin AS fecha_fin, 
                   pe_estado AS estado 
              FROM sw_periodo_lectivo 
             WHERE oferta_educativa_id = $id_oferta 
             ORDER BY pe_fecha_inicio DESC";
} else {
    $sql = "SELECT id_periodo_lectivo AS id, 
                   nombre, 
                   pe_fecha_inicio AS fecha_inicio, 
                   pe_fecha_fin AS fecha_fin, 
                   pe_estado AS estado 
              FROM sw_periodo_lectivo 
             ORDER BY pe_fecha_inicio DESC";
}

$result = $db->consulta($sql);

// 4. Estructuración: Recorrer todas las filas de la base de datos
$lista_periodos = array();

while ($row = $db->fetch_assoc($result)) {
    // Control de estados por si tu BD usa números (1: Activo, 0: Inactivo)
    // Si ya guarda texto directamente, puedes dejar solo: $row['estado']
    $estadoTexto = ($row['estado'] == 'A' || strtolower($row['estado']) == 'activo') ? 'Activo' : 'Inactivo';

    $lista_periodos[] = array(
        "id"           => $row['id'],
        "nombre"       => $row['nombre'],
        "fecha_inicio" => date("d/m/Y", strtotime($row['fecha_inicio'])), // Formato de fecha legible
        "fecha_fin"    => date("d/m/Y", strtotime($row['fecha_fin'])),    // CORREGIDO: Ahora coincide con el alias SQL
        "estado"       => $estadoTexto
    );
}

// 5. Respuesta Correcta para el $.each() de jQuery
echo json_encode(array("data" => $lista_periodos));
exit;
?>
