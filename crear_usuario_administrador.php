<?php
require_once("scripts/clases/class.mysql.php");
require_once("scripts/clases/class.encrypter.php");

$db = new MySQL();

function existeAdministrador()
{
    global $db;

    $qry = "SELECT * FROM sw_usuario WHERE us_login = 'administrador'";
    $consulta = $db->consulta($qry);

    return $db->num_rows($consulta) > 0;
}

function existeAsociacionUsuarioPerfil()
{
    global $db;

    $qry = "SELECT * FROM sw_usuario WHERE us_login = 'administrador'";
    $consulta = $db->consulta($qry);

    $id_usuario = $db->fetch_object($consulta)->id_usuario;

    $qry = "SELECT * FROM sw_usuario_perfil WHERE id_usuario = $id_usuario";
    $consulta = $db->consulta($qry);

    return $db->num_rows($consulta) > 0;
}

if (!existeAdministrador()) {
    try {
        $qry = "INSERT INTO sw_usuario SET ";
        $qry .= "us_titulo = 'Ing.',";
        $qry .= "us_titulo_descripcion = 'Ingeniero en Sistemas',";
        $qry .= "us_apellidos = 'Peñaherrera Escobar',";
        $qry .= "us_nombres = 'Gonzalo Nicolás',";
        $qry .= "us_shortname = 'Ing. Gonzalo Peñaherrera',";
        $qry .= "us_fullname = 'Peñaherrera Escobar Gonzalo Nicolás',";
        $qry .= "us_login = 'Administrador',";
        $clave = encrypter::encrypt('gP67M24e$');
        $qry .= "us_password = '" . $clave . "',";
        $qry .= "us_genero = 'M',";
        $qry .= "us_foto = '855136358.jpg',";
        $qry .= "us_activo = 1";

        $consulta = $db->consulta($qry);

        $qry = "SELECT * FROM sw_usuario WHERE us_login = 'administrador'";
        $consulta = $db->consulta($qry);

        $id_usuario = $db->fetch_object($consulta)->id_usuario;

        $qry = "INSERT INTO sw_usuario_perfil SET ";
        $qry .= "id_usuario = $id_usuario,";
        $qry .= "id_perfil = 1";

        $consulta = $db->consulta($qry);

        echo "Usuario Administrador creado exitosamente.";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    if (!existeAsociacionUsuarioPerfil()) {
        $qry = "SELECT * FROM sw_usuario WHERE us_login = 'administrador'";
        $consulta = $db->consulta($qry);

        $id_usuario = $db->fetch_object($consulta)->id_usuario;

        $qry = "INSERT INTO sw_usuario_perfil SET ";
        $qry .= "id_usuario = $id_usuario,";
        $qry .= "id_perfil = 1";

        $consulta = $db->consulta($qry);

        echo "Usuario Administrador Asociado exitosamente.";
    } else {
        echo "Ya existe el usuario Administrador.";
    }
}
