<?php
require_once "../usuarios/upload_image.php";
include("../scripts/clases/class.mysql.php");

if ($_FILES["us_foto"]["name"] == "") {
    $image = $_POST["bd_us_avatar"];
} else {
    $image = upload_image("us_foto");
}

$id_usuario = $_POST["edit_id_usuario"];
$us_titulo = $_POST["edit_us_titulo"];
$us_titulo_descripcion = $_POST["edit_us_titulo_descripcion"];
$us_apellidos = $_POST["edit_us_apellidos"];
$us_nombres = $_POST["edit_us_nombres"];
$us_genero = $_POST["edit_us_genero"];
$us_email = $_POST["edit_us_email"];
$us_foto = $image;

$qry = "UPDATE sw_usuario SET ";
$qry .= "us_titulo = '" . $us_titulo . "',";
$qry .= "us_titulo_descripcion = '" . $us_titulo_descripcion . "',";
$qry .= "us_apellidos = '" . $us_apellidos . "',";
$qry .= "us_nombres = '" . $us_nombres . "',";
$qry .= "us_email = '" . $us_email . "',";
$qry .= "us_genero = '" . $us_genero . "',";
$qry .= "us_foto = '" . $us_foto . "'";
$qry .= " WHERE id_usuario = $id_usuario";

$db = new MySQL;
try {
    $db->consulta($qry);

    $data = array(
        "titulo"         => "Operación exitosa.",
        "mensaje"        => "Usuario actualizado exitosamente...",
        "tipo_mensaje"   => "success"
    );
} catch (Exception $e) {
    $data = array(
        "titulo"         => "Ocurrió un error inesperado.",
        "mensaje"        => "No se pudo actualizar el usuario en la Base de Datos. Error: " . $e->getMessage(),
        "tipo_mensaje"   => "error"
    );
}

echo json_encode($data);
