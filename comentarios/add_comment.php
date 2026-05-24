<?php

//add_comment.php

date_default_timezone_set('America/Guayaquil');

require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$error = '';
$comment_content = '';
$id_usuario = '';

$mensaje = $_POST["mensaje"];
$emisor_id = $_POST["emisor_id"];
$receptor_id = $_POST["receptor_id"];

$error = false;

$sql = "SELECT u.us_fullname, p.pe_nombre FROM sw_usuario u, sw_usuario_perfil up, sw_perfil p WHERE u.id_usuario = up.id_usuario AND p.id_perfil = up.id_perfil AND u.id_usuario = $emisor_id LIMIT 0, 1";

$result = $db->consulta($sql);
$usuario = $db->fetch_assoc($result);
$nombreCompleto = $usuario["us_fullname"];
$perfilEmisor = $usuario["pe_nombre"];

$fechaActual = date('Y-m-d H:i:s');

if ($mensaje !== '') {
    $sql = "
    INSERT INTO sw_mensajes 
    (emisor_id, receptor_id, nombre_usuario, perfil_emisor, mensaje, leido, fecha) 
    VALUES ($emisor_id, $receptor_id, '$nombreCompleto', '$perfilEmisor', '$mensaje', 0, '$fechaActual')
    ";

    $consulta = $db->consulta($sql);
}

$data = array(
    'error'  => $fechaActual
);

echo json_encode($data);

?>