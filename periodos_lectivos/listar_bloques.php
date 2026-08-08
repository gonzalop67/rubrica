<?php
// periodos_lectivos/listar_bloques.php

// 1. Configurar cabeceras para retornar JSON y evitar problemas de caché
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");

// 2. Inclusión de la clase e instancia
require_once "../scripts/clases/class.mysql.php";
$db = new MySQL;

// 3. Validar que se reciba el parámetro esperado por el AJAX
$id_lectivo = isset($_GET['id_lectivo']) ? intval($_GET['id_lectivo']) : 0;

if ($id_lectivo <= 0) {
    echo json_encode(["data" => [], "message" => "Año lectivo no válido."]);
    exit;
}

// 4. Consulta SQL optimizada
// Enlaza con 'sw_tipo_periodo' para el nombre y calcula las fechas con un sub-query de sus aportes
$sql = "SELECT 
            p.id_periodo_evaluacion AS id, 
            p.pe_nombre AS nombre, 
            t.tp_descripcion AS tipo, 
            p.pe_orden AS orden,
            COALESCE((SELECT MIN(ap_fecha_apertura) FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = p.id_periodo_evaluacion), 'S/F') AS fecha_inicio,
            COALESCE((SELECT MAX(ap_fecha_cierre) FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = p.id_periodo_evaluacion), 'S/F') AS fecha_fin
        FROM sw_periodo_evaluacion p
        INNER JOIN sw_tipo_periodo t ON p.id_tipo_periodo = t.id_tipo_periodo
        WHERE p.id_periodo_lectivo = $id_lectivo 
        ORDER BY p.pe_orden ASC";

// Ejecuta la consulta usando el método de tu clase
$res_consulta = $db->consulta($sql);

$bloques = [];

// 5. Recorrer los registros usando tu método fetch_assoc
while ($fila = $db->fetch_assoc($res_consulta)) {
    $bloques[] = $fila;
}

// 6. Retornar los datos en el formato JSON esperado por el JS
echo json_encode(["data" => $bloques]);
exit;
