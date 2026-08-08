<?php // periodos_lectivos/insertar_ano_lectivo.php
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
require_once "../scripts/clases/class.mysql.php";

$db = new MySQL;

// 1. Validar que la petición sea estrictamente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método de petición no permitido."]);
    exit;
}

// 2. Recibir y sanitizar los datos usando el método filtrar() de tu clase
$id_oferta = isset($_POST['id_oferta']) ? intval($_POST['id_oferta']) : 0;
$nombre = isset($_POST['nombre']) ? $db->filtrar($_POST['nombre']) : '';
$anio_inicio = isset($_POST['anio_inicio']) ? intval($_POST['anio_inicio']) : 0;
$anio_fin = isset($_POST['anio_fin']) ? intval($_POST['anio_fin']) : 0;
$fecha_inicio = isset($_POST['fecha_inicio']) ? $db->filtrar($_POST['fecha_inicio']) : '';
$fecha_fin = isset($_POST['fecha_fin']) ? $db->filtrar($_POST['fecha_fin']) : '';
$nota_minima = isset($_POST['nota_minima']) ? floatval($_POST['nota_minima']) : 0;
$nota_aprobacion = isset($_POST['nota_aprobacion']) ? floatval($_POST['nota_aprobacion']) : 0;
$quien_inserta_comp = isset($_POST['quienInsertaComp']) ? intval($_POST['quienInsertaComp']) : 0;

// 3. Validar consistencia de los datos
if ($id_oferta <= 0 || empty($nombre) || empty($fecha_inicio) || empty($fecha_fin) || $anio_inicio <= 0 || $anio_fin <= 0 || $nota_minima <= 0 || $nota_aprobacion <= 0 || $quien_inserta_comp <= 0) {
    echo json_encode(["success" => false, "message" => "Existen campos vacíos o inválidos."]);
    exit;
}

// 4. Ejecutar la inserción en la base de datos
// NOTA IMPORTANTE: Asegúrate de que 'oferta_educativa_id' sea el nombre real en tu tabla 'sw_periodo_lectivo'
$sql = "INSERT INTO sw_periodo_lectivo (
            oferta_educativa_id, 
            id_modalidad, 
            id_periodo_estado, 
            nombre, 
            pe_anio_inicio, 
            pe_anio_fin, 
            pe_fecha_inicio, 
            pe_fecha_fin, 
            pe_nota_minima, 
            pe_nota_maxima, 
            pe_nota_aprobacion, 
            quien_inserta_comp_id, 
            pe_estado
        ) VALUES (
            $id_oferta, 
            $id_oferta, 
            1, 
            '$nombre', 
            $anio_inicio, 
            $anio_fin, 
            '$fecha_inicio', 
            '$fecha_fin', 
            $nota_minima, 
            10, 
            $nota_aprobacion, 
            $quien_inserta_comp, 
            'A'
        )";

$db->consulta($sql);

// 5. Cerrar el periodo lectivo anterior si existe
// CORRECCIÓN 1: Se cambió '$this->id_oferta_educativa' por '$id_oferta'
// CORRECCIÓN 2: Se cambió 'id_oferta_educativa' por 'oferta_educativa_id' para mantener coherencia con la línea de INSERT de arriba
$sql_anterior = "SELECT id_periodo_lectivo 
                 FROM sw_periodo_lectivo 
                 WHERE oferta_educativa_id = $id_oferta 
                 ORDER BY id_periodo_lectivo DESC 
                 LIMIT 1, 1";

$consulta = $db->consulta($sql_anterior);
$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros == 1) {
    $periodo_lectivo = $db->fetch_object($consulta);
    $id_periodo_lectivo = $periodo_lectivo->id_periodo_lectivo;
    
    // Ejecuta el procedimiento almacenado para cerrar el periodo previo
    $db->consulta("CALL sp_cerrar_periodo_lectivo($id_periodo_lectivo)");
}

// 6. Responder al AJAX si todo salió bien
echo json_encode([
    "success" => true,
    "message" => "El año lectivo se ha creado exitosamente."
]);
exit;
