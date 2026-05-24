<?php
require_once "../scripts/clases/class.mysql.php";

// Variables pasadas a través del método POST
$search_pattern = $_POST["search_pattern"];

$db = new MySQL();

$consulta = $db->consulta("SELECT * 
                             FROM sw_usuario 
                            WHERE us_apellidos LIKE '%$search_pattern%'
                               OR us_nombres LIKE '%$search_pattern%' 
                               OR us_login LIKE '%$search_pattern%'
                         ORDER BY us_apellidos, us_nombres ASC");

$cadena = "";

$num_registros = $db->num_rows($consulta);

if ($num_registros > 0) {
    $contador = 0;
    while ($usuario = $db->fetch_object($consulta)) {
        $contador++;
        $cadena .= "<tr>\n";
        $cadena .= "<td>$contador</td>\n";
        $cadena .= "<td>$usuario->id_usuario</td>\n";
        $us_foto = ($usuario->us_foto == '') ? 'public/uploads/no-disponible.png' : 'public/uploads/' . $usuario->us_foto;
        $cadena .= "<td><img class=\"img-thumbnail\" width=\"50\" src=\"$us_foto\" alt=\"Avatar del Usuario\"></td>\n";
        $cadena .= "<td>$usuario->us_fullname</td>\n";
        $cadena .= "<td>$usuario->us_login</td>\n";
        $us_activo = ($usuario->us_activo == 1) ? 'Sí' : 'No';
        $cadena .= "<td>$us_activo</td>\n";
        $perfiles = $db->consulta("SELECT u.id_usuario, p.pe_nombre FROM sw_usuario u, sw_perfil p, sw_usuario_perfil up WHERE u.id_usuario = up.id_usuario AND p.id_perfil = up.id_perfil AND u.id_usuario = $usuario->id_usuario  ORDER BY p.pe_nombre ASC");
        $cadena_perfiles = "";
        while ($perfil = $db->fetch_object($perfiles)) {
            $cadena_perfiles .= $perfil->pe_nombre . ", ";
        }
        $cadena_perfiles = rtrim($cadena_perfiles, ", ");
        $cadena .= "<td>$cadena_perfiles</td>\n";
        $cadena .= "<td>\n";
        $cadena .= "<div class=\"btn-group\">\n";
        $cadena .= "<button type=\"button\" class=\"btn btn-warning item-edit\" title=\"Editar\" onclick=\"obtenerDatos($usuario->id_usuario)\" data-toggle=\"modal\" data-target=\"#editarUsuarioModal\"><span class=\"fa fa-pencil\"></span></button>\n";
        $cadena .= "<button type=\"button\" class=\"btn btn-danger\" onclick=\"eliminarUsuario($usuario->id_usuario)\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
        $cadena .= "</div>\n";
        $cadena .= "</td>\n";
        $cadena .= "</tr>\n";
    }
} else {
    $cadena .= "<tr class=\"text-center\">\n";
    $cadena .= "<td colspan=\"8\">No se han encontrado registros coincidentes...</td>\n";
    $cadena .= "</tr>\n";
}

echo $cadena;
