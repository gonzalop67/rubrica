<?php
// periodos_lectivos/listar_parciales.php

// 1. Configurar cabeceras para retornar JSON y evitar problemas de caché
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");

// 2. Inclusión de la clase e instancia
require_once "../scripts/clases/class.mysql.php";
$db = new MySQL;

// 3. Validar el parámetro enviado por el AJAX
$id_bloque = isset($_GET['id_bloque']) ? intval($_GET['id_bloque']) : 0;

if ($id_bloque <= 0) {
    echo json_encode(["data" => [], "message" => "Bloque académico no válido."]);
    exit;
}

// 4. Consulta SQL adaptada a la estructura de sw_aporte_evaluacion
// Mapeamos los campos físicos a los nombres requeridos por el JS
$sql = "SELECT id_aporte_evaluacion AS id,
               ap_nombre AS descripcion,
               ROUND(COALESCE(ap_ponderacion, 0) * 100, 2) AS peso_nota,
               ap_fecha_apertura AS fecha_inicio,
               ap_fecha_cierre AS fecha_fin,
               ap_fecha_cierre AS fecha_cierre
        FROM sw_aporte_evaluacion
        WHERE id_periodo_evaluacion = $id_bloque
        ORDER BY ap_orden ASC";

$res_consulta = $db->consulta($sql);
$parciales = [];

// 5. Recorrer registros con tu método fetch_assoc
while ($fila = $db->fetch_assoc($res_consulta)) {
    $parciales[] = $fila;
}

// 6. Retorno de datos estructurados en formato JSON
echo json_encode(["data" => $parciales]);
exit;
