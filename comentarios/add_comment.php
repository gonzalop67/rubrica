<?php 
// add_comment.php
date_default_timezone_set('America/Guayaquil');
require_once("../scripts/clases/class.mysql.php"); 
$db = new MySQL(); 

// 1. Capturar y sanitizar datos entrantes
$emisor_id   = isset($_POST["emisor_id"]) ? intval($_POST["emisor_id"]) : 0;
$receptor_id = isset($_POST["receptor_id"]) ? intval($_POST["receptor_id"]) : 0;

// Usamos mysqli_real_escape_string de la conexión interna de tu clase para evitar inyección SQL
$mensaje = isset($_POST["mensaje"]) ? mysqli_real_escape_string($db->conexion, trim($_POST["mensaje"])) : '';

$response = array('status' => 'error', 'msg' => 'Ocurrió un error inesperado.');

if ($emisor_id > 0 && $receptor_id > 0 && $mensaje !== '') {
    
    // 2. Obtener datos del emisor de forma segura
    $sql = "SELECT u.us_fullname, p.pe_nombre 
            FROM sw_usuario u, sw_usuario_perfil up, sw_perfil p 
            WHERE u.id_usuario = up.id_usuario 
              AND p.id_perfil = up.id_perfil 
              AND u.id_usuario = $emisor_id 
            LIMIT 0, 1";
            
    $result = $db->consulta($sql);
    
    if ($db->num_rows($result) > 0) {
        $usuario = $db->fetch_assoc($result);
        
        // Escapar los valores obtenidos por seguridad si contienen caracteres especiales
        $nombreCompleto = mysqli_real_escape_string($db->conexion, $usuario["us_fullname"]);
        $perfilEmisor   = mysqli_real_escape_string($db->conexion, $usuario["pe_nombre"]);
        $fechaActual    = date('Y-m-d H:i:s');
        
        // 3. Inserción limpia y segura en la base de datos
        $sql_insert = "INSERT INTO sw_mensajes (emisor_id, receptor_id, nombre_usuario, perfil_emisor, mensaje, leido, fecha) 
                       VALUES ($emisor_id, $receptor_id, '$nombreCompleto', '$perfilEmisor', '$mensaje', 0, '$fechaActual')";
        
        if ($db->consulta($sql_insert)) {
            // Respuesta de éxito estructurada para AJAX
            $response = array(
                'status' => 'success',
                'fecha'  => date('d M h:i a', strtotime($fechaActual))
            );
        } else {
            $response['msg'] = 'No se pudo guardar el mensaje en la base de datos.';
        }
    } else {
        $response['msg'] = 'El usuario emisor no existe o no tiene perfil asignado.';
    }
} else {
    $response['msg'] = 'Campos obligatorios vacíos o inválidos.';
}

// 4. Retornar siempre un JSON estructurado
header('Content-Type: application/json');
echo json_encode($response);
?>
