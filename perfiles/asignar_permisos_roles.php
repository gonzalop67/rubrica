<?php
// (Aquí debes incluir tu conexión a la base de datos como lo haces siempre, ej: $db)
include("../scripts/clases/class.mysql.php");
$db = new MySQL;

// Configurar la cabecera para indicar al navegador que responderemos JSON
header('Content-Type: application/json');

$perfil_id = isset($_POST['perfil_id']) ? intval($_POST['perfil_id']) : 0;

if ($perfil_id > 0) {
    $permisos_seleccionados = isset($_POST['permisos']) ? $_POST['permisos'] : [];

    // 1. Eliminar permisos anteriores
    $sql_delete = "DELETE FROM sw_perfil_permiso WHERE id_perfil = $perfil_id";
    $db->consulta($sql_delete);

    // 2. Insertar nuevos permisos
    foreach ($permisos_seleccionados as $id_permiso) {
        $id_permiso = intval($id_permiso);
        $sql_insert = "INSERT INTO sw_perfil_permiso (id_perfil, id_permiso) VALUES ($perfil_id, $id_permiso)";
        $db->consulta($sql_insert);
    }

    // 3. RETORNAR RESPUESTA EXITOSA EN JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Los permisos del perfil han sido actualizados con éxito.'
    ]);
    exit();
} else {
    // Retornar error si no se recibió un ID válido
    echo json_encode([
        'status' => 'error',
        'message' => 'ID de perfil no válido.'
    ]);
    exit();
}
?>
