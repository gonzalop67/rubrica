<?php
sleep(1);
session_start();

require_once("../scripts/clases/class.mysql.php");
require_once("../scripts/clases/class.encrypter.php");

//recibo las variables de tipo post de la pagina login.php
$login = $_POST['uname'];
$clave = $_POST['passwd'];

//consultar a la tabla usuario si existe un usuario en la tabla
$_SESSION['usuario_logueado'] = false;

$db = new MySQL();

$clave = encrypter::encrypt($clave);
$consulta = $db->consulta("SELECT u.id_usuario, us_activo "
    . " FROM sw_usuario u, "
    . "      sw_perfil p, "
    . "      sw_usuario_perfil up "
    . "WHERE u.id_usuario = up.id_usuario "
    . "  AND p.id_perfil = up.id_perfil "
    . "  AND us_login = '$login' "
    . "  AND us_password = '$clave' "
    . "  AND p.id_perfil = 2");

$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
    $row = $db->fetch_object($consulta);
    $activo = $row->us_activo;
    $id_usuario = $row->id_usuario;

    $_SESSION['usuario_logueado'] = true;
    $_SESSION['id_usuario'] = $id_usuario;

    echo json_encode(array(
        'id_usuario' => $id_usuario,
        'error' => false,
        'activo' => $activo
    ));
} else {
    echo json_encode(array(
        'id_usuario' => 0,
        'error' => true,
        'activo' => 0
    ));
}
