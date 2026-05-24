<?php
header('Content-Type: application/json');

// 1. Conexión a la base de datos
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// 2. Leer datos enviados por Fetch (JSON)
$input = json_decode(file_get_contents("php://input"), true);

$start = $input['start'] ?? 0;
$length = $input['length'] ?? 10;
$searchValue = $input['search']['value'] ?? '';
$draw = $input['draw'] ?? 1;

// 3. Contar total de registros originales
$resTotal = $db->consulta("SELECT COUNT(*) AS totalRecords FROM sw_estudiante e, sw_def_genero dg, sw_def_nacionalidad dn WHERE dg.id_def_genero = e.id_def_genero AND dn.id_def_nacionalidad = e.id_def_nacionalidad");
$totalRecords = $db->fetch_object($resTotal)->totalRecords;

// 4. Consulta con filtrado y paginación
$searchQuery = " WHERE dg.id_def_genero = e.id_def_genero AND dn.id_def_nacionalidad = e.id_def_nacionalidad ";
if (!empty($searchValue)) {
    $searchQuery .= " AND (es_apellidos LIKE '%$searchValue%' OR es_nombres LIKE '%$searchValue%' OR es_cedula LIKE '%$searchValue%') ";
}

// Obtener total filtrado
$resFiltered = $db->consulta("SELECT COUNT(*) AS totalFiltered FROM sw_estudiante e, sw_def_genero dg, sw_def_nacionalidad dn $searchQuery");
$totalFiltered = $db->fetch_object($resFiltered)->totalFiltered;

// Obtener datos finales
$sql = "SELECT id_estudiante, es_apellidos, es_nombres, es_cedula, es_fec_nacim, TIMESTAMPDIFF(YEAR, es_fec_nacim, CURDATE()) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(es_fec_nacim, '%m%d')) AS edad, dg_nombre, dn_nombre FROM sw_estudiante e, sw_def_genero dg, sw_def_nacionalidad dn $searchQuery ORDER BY es_apellidos, es_nombres ASC LIMIT $start, $length";
$resultado = $db->consulta($sql);

$data = [];
while ($row = $resultado->fetch_assoc()) {
    $data[] = $row;
}

// 5. Respuesta en formato esperado por DataTables
echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
