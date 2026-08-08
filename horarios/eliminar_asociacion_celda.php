<?php
// 1. Configurar cabeceras para salida JSON
header('Content-Type: application/json; charset=utf-8');

// 2. Incluir tu archivo de conexión real (ajusta la ruta según tu proyecto)
require_once "../scripts/clases/class.mysql.php"; 

// 3. Instanciar tu clase personalizada de base de datos
$db = new MySQL();

// 4. Capturar y desinfectar los parámetros enviados por POST desde la celda
$id_paralelo    = isset($_POST['id_paralelo']) ? intval($db->filtrar($_POST['id_paralelo'])) : 0;
$id_dia_semana  = isset($_POST['id_dia_semana']) ? intval($db->filtrar($_POST['id_dia_semana'])) : 0;
$id_hora_clase  = isset($_POST['id_hora_clase']) ? intval($db->filtrar($_POST['id_hora_clase'])) : 0;
$id_horario_def = isset($_POST['id_horario_def']) ? intval($db->filtrar($_POST['id_horario_def'])) : 0;

// Validación básica de seguridad
if ($id_paralelo === 0 || $id_dia_semana === 0 || $id_hora_clase === 0 || $id_horario_def === 0) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "Parámetros incompletos para ejecutar la eliminación."]);
    exit;
}

try {
    // 5. Definir la consulta de eliminación por coordenadas exactas
    // NOTA: Asegúrate de reemplazar 'sw_horario_clase_detalle' por el nombre real de tu tabla asociativa
    $sql = "DELETE FROM sw_horario 
            WHERE id_paralelo = '$id_paralelo' 
              AND id_dia_semana = '$id_dia_semana' 
              AND id_hora_clase = '$id_hora_clase' 
              AND id_horario_def = '$id_horario_def'";

    // 6. Ejecutar la consulta con tu método nativo
    $db->consulta($sql);

    // 7. Retornar éxito en formato JSON para que jQuery refresque el tablero
    echo json_encode(["success" => true, "mensaje" => "Asignatura retirada correctamente."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => true, "mensaje" => "Error al eliminar: " . $e->getMessage()]);
}
