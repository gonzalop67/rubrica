<?php
// 1. Iniciar sesión y validar de inmediato antes de conectar a la BD
session_start();
$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;

if ($id_usuario === 0) {
    header('HTTP/1.1 403 Forbidden');
    exit("Acceso denegado: Sesión inválida.");
}

// 2. Validar parámetros obligatorios de entrada
if (!isset($_GET['id_perfil']) || empty($_GET['id_perfil'])) {
    header('HTTP/1.1 400 Bad Request');
    exit("Parámetro id_perfil requerido.");
}

include_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_perfil = intval($_GET['id_perfil']);
$id_menu_url = isset($_GET['id_menu_actual']) ? intval($_GET['id_menu_actual']) : 0;

$html = "";

// 3. Obtener menús de Nivel 1 (Padres)
$sqlNivel1 = "SELECT m.*, p.pe_nombre 
              FROM `sw_menu` m
              INNER JOIN `sw_menu_perfil` mp ON m.id_menu = mp.id_menu 
              INNER JOIN `sw_perfil` p ON p.id_perfil = mp.id_perfil 
              WHERE mp.id_perfil = $id_perfil AND m.mnu_padre = 0 
              ORDER BY m.mnu_orden ASC";

$menusNivel1 = $db->consulta($sqlNivel1);

if ($db->num_rows($menusNivel1) > 0) {
    $html .= '<ol class="dd-list">'; 

    while ($menu = $db->fetch_assoc($menusNivel1)) {
        
        $id_menu_padre = intval($menu["id_menu"]);
        $perfil = "(" . htmlspecialchars($menu["pe_nombre"], ENT_QUOTES, 'UTF-8') . ") ";
        $texto_menu = htmlspecialchars($menu["mnu_texto"], ENT_QUOTES, 'UTF-8');

        // ESTRUCTURA CORREGIDA NIVEL 1
        $html .= '<li class="dd-item dd3-item" data-id="' . $id_menu_padre . '">';
        $html .= '  <div class="dd-handle dd3-handle"></div>'; // El manejador se queda afuera del contenido
        $html .= '  <div class="dd3-content">'; // Removida clase "menu_link" sobrante si causaba conflicto
        // Envolvemos el texto en un span para aislarlo del botón flotante derecho
        $html .= '    <span class="menu-texto"><a href="#" onclick="obtenerDatos(' . $id_menu_padre . ')" data-toggle="modal" data-target="#editarMenuModal">' . $perfil . $texto_menu . '</a></span>';
        $html .= '    <a href="#" class="eliminar-menu pull-right" data-id="' . $id_menu_padre . '" title="Eliminar este menú" style="margin-top: -2px;"><i class="text-danger fa fa-trash"></i></a>';
        $html .= '  </div>'; 

        // 4. Obtener submenús de Nivel 2
        $sqlNivel2 = "SELECT * FROM sw_menu WHERE mnu_padre = " . $id_menu_padre . " ORDER BY mnu_orden ASC";
        
        $menusNivel2 = $db->consulta($sqlNivel2);

        if ($db->num_rows($menusNivel2) > 0) {
            $html .= '  <ol class="dd-list">';
            
            while ($menu2 = $db->fetch_assoc($menusNivel2)) {
                $id_sub_menu = intval($menu2["id_menu"]);
                $texto_sub = htmlspecialchars($menu2["mnu_texto"], ENT_QUOTES, 'UTF-8');

                // ESTRUCTURA CORREGIDA NIVEL 2
                $html .= '    <li class="dd-item dd3-item" data-id="' . $id_sub_menu . '">';
                $html .= '      <div class="dd-handle dd3-handle"></div>';
                $html .= '      <div class="dd3-content">';
                $html .= '        <span class="menu-texto"><a href="#" onclick="obtenerDatos(' . $id_sub_menu . ')" data-toggle="modal" data-target="#editarMenuModal">' . $texto_sub . '</a></span>';
                $html .= '        <a href="#" class="eliminar-menu pull-right" data-id="' . $id_sub_menu . '" title="Eliminar este menú" style="margin-top: -2px;"><i class="text-danger fa fa-trash"></i></a>';
                $html .= '      </div>';
                $html .= '    </li>';
            }
            
            $html .= '  </ol>'; 
        }

        $html .= '</li>'; 
    }

    $html .= '</ol>'; 
}

echo $html;
?>
