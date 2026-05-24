<?php
header('Content-Type: application/json');

// 1. Conexión a la base de datos
require_once "../scripts/clases/class.mysql.php";
$db = new MySQL();

// 2. Leer datos enviados por DataTables vía Fetch (JSON)
$input = json_decode(file_get_contents("php://input"), true);

$start = $input['start'] ?? 0;
$length = $input['length'] ?? 10;
$searchValue = $input['search']['value'] ?? '';
$draw = $input['draw'] ?? 1;

// PAPELERA: Contar el total de registros que SÍ han sido eliminados lógicamente
$sql = "SELECT COUNT(*) AS totalRecords FROM sw_usuario WHERE deleted_at IS NOT NULL";

// echo json_encode($sql);
// die();

$resTotal = $db->consulta($sql);
$totalRecords = $db->fetch_object($resTotal)->totalRecords;

// 3. Consulta con filtrado y paginación
// PAPELERA: Forzar a que las búsquedas operen únicamente sobre usuarios eliminados
$searchQuery = " WHERE u.deleted_at IS NOT NULL "; 
if (!empty($searchValue)) {
    // Al usar "AND", aislamos los filtros "OR" entre paréntesis para proteger el estado de la papelera
    $searchQuery .= " AND (u.us_apellidos LIKE '%$searchValue%' OR u.us_nombres LIKE '%$searchValue%' OR u.us_login LIKE '%$searchValue%' OR u.us_titulo LIKE '%$searchValue%') ";
}

// Obtener total filtrado respetando el filtro de la papelera
$resFiltered = $db->consulta("SELECT COUNT(*) AS totalFiltered FROM sw_usuario u $searchQuery");
$totalFiltered = $db->fetch_object($resFiltered)->totalFiltered;

// Obtener los datos finales de la papelera con sus perfiles asignados
$sql = "SELECT u.id_usuario, us_foto, CONCAT(us_apellidos, ' ', us_nombres) AS nombre, us_login, us_activo, u.deleted_at, GROUP_CONCAT(p.pe_nombre SEPARATOR ', ') AS perfiles 
        FROM sw_usuario u 
        LEFT JOIN sw_usuario_perfil up ON u.id_usuario = up.id_usuario 
        LEFT JOIN sw_perfil p ON up.id_perfil = p.id_perfil 
        $searchQuery 
        GROUP BY u.id_usuario 
        ORDER BY u.deleted_at DESC, us_apellidos, us_nombres ASC 
        LIMIT $start, $length";
        
$resultado = $db->consulta($sql);

$data = [];
while ($row = $resultado->fetch_assoc()) {
    // Si el usuario no tiene foto asignada, aseguramos el nombre por defecto
    if (empty($row['us_foto'])) {
        $row['us_foto'] = 'default.png';
    }
    $data[] = $row;
}

// 4. Respuesta en formato esperado por DataTables
echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
