<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../scripts/clases/class.mysql.php");
$db = new MySQL;

// Validación: Verificar que se envíe el identificador del horario estructurado
if (!isset($_GET['id_horario_def']) || empty($_GET['id_horario_def'])) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "Falta el parámetro id_horario_def"]);
    exit;
}

// Desinfectar el parámetro usando el método de tu propia clase
$id_horario_def = intval($db->filtrar($_GET['id_horario_def']));

try {
    // 3. Definir la consulta utilizando los nombres exactos de tu tabla sw_hora_clase
    $sql = "SELECT 
                id_hora_clase, 
                hc_nombre, 
                CONCAT(TIME_FORMAT(hc_hora_inicio, '%H:%i'), ' - ', TIME_FORMAT(hc_hora_fin, '%H:%i')) AS rango_tiempo 
            FROM sw_hora_clase 
            WHERE id_horario_def = '$id_horario_def' 
            ORDER BY hc_orden ASC";

    // 4. Ejecutar la consulta con tu método personalizado
    $resultado_query = $db->consulta($sql);

    $horas = [];

    // 5. Recorrer los registros usando tu método fetch_assoc
    while ($fila = $db->fetch_assoc($resultado_query)) {
        $horas[] = $fila;
    }

    // 6. Retornar el resultado codificado en formato JSON limpio
    echo json_encode($horas, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "mensaje" => "Error en el servidor: " . $e->getMessage()
    ]);
}