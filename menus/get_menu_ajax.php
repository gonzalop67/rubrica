<?php
// menus/get_menu_ajax.php
include_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

// AdminLTE envía el id del Perfil seleccionado desde la vista
$id_perfil = isset($_POST['perfil_id']) ? (int)$_POST['perfil_id'] : 0;

if ($id_perfil === 0) {
    echo '<div id="nestable-placeholder">
            <div class="text-muted text-center" style="padding: 30px 0;">
                <i class="fa fa-exclamation-triangle text-warning" style="font-size: 24px; display: block; margin-bottom: 10px;"></i> 
                Perfil no válido o no seleccionado.
            </div>
          </div>';
    exit;
}

// Sanitizamos el parámetro
$id_perfil_filtrado = (int)$db->filtrar($id_perfil);

/**
 * EXPLICACIÓN DEL SQL ADAPTADO A PERMISOS:
 * 1. Selecciona los menús cuyo permiso asociado está concedido al perfil actual.
 * 2. Hace un UNION para traer también los menús "públicos" (aquellos que no requieren ningún permiso, como el Dashboard o Inicio).
 */
// SQL CORREGIDO: Ambas partes del UNION filtran estrictamente por el perfil seleccionado
$sql = "SELECT m.id_menu, m.mnu_texto, m.mnu_link, m.mnu_padre, m.mnu_orden, m.mnu_icono 
        FROM `sw_menu` m 
        INNER JOIN `sw_menu_perfil` mp ON m.id_menu = mp.id_menu
        INNER JOIN `sw_permiso` p ON m.permiso_slug = p.slug 
        INNER JOIN `sw_perfil_permiso` pp ON p.id_permiso = pp.id_permiso 
        WHERE mp.id_perfil = {$id_perfil_filtrado} 
          AND pp.id_perfil = {$id_perfil_filtrado}
          AND m.mnu_publicado = 1 
        
        UNION 
        
        SELECT m.id_menu, m.mnu_texto, m.mnu_link, m.mnu_padre, m.mnu_orden, m.mnu_icono 
        FROM `sw_menu` m 
        INNER JOIN `sw_menu_perfil` mp ON m.id_menu = mp.id_menu
        WHERE mp.id_perfil = {$id_perfil_filtrado}
          AND (m.permiso_slug IS NULL OR m.permiso_slug = '')
          AND m.mnu_publicado = 1
        
        ORDER BY mnu_padre ASC, mnu_orden ASC";

$queryResult = $db->consulta($sql);
$rows = [];

if ($queryResult) {
    while ($row = $db->fetch_assoc($queryResult)) {
        // Evitamos duplicidad de registros generada por el UNION si la data es idéntica
        $rows[$row['id_menu']] = $row;
    }
}

if (empty($rows)) {
    echo '<div class="dd-empty text-muted text-center" style="padding: 30px 0; background-color: #f4f4f4; border: 2px dashed #bbb;">
            <i class="fa fa-lock text-muted" style="font-size: 28px; display: block; margin-bottom: 10px;"></i> 
            Este perfil no tiene permisos asignados que otorguen acceso a menús.
          </div>';
    exit;
}

// 2. CONSTRUCCIÓN DEL ÁRBOL JERÁRQUICO
$menuTree = [];
$submenus = [];

foreach ($rows as $row) {
    if ((int)$row['mnu_padre'] === 0) {
        $row['submenu'] = [];
        $menuTree[$row['id_menu']] = $row;
    } else {
        $submenus[] = $row;
    }
}

foreach ($submenus as $sub) {
    $padreId = $sub['mnu_padre'];
    if (isset($menuTree[$padreId])) {
        $menuTree[$padreId]['submenu'][] = $sub;
    } else {
        $asignado = false;
        foreach ($menuTree as &$padreRaiz) {
            if (insertarEnHijo($padreRaiz, $sub)) {
                $asignado = true;
                break;
            }
        }
        if (!$asignado) {
            $sub['submenu'] = [];
            $menuTree[$sub['id_menu']] = $sub;
        }
    }
}

// 3. Renderizar y retornar el HTML directo
echo renderNestableTree(array_values($menuTree));
exit;


// ==========================================
// FUNCIONES DE SOPORTE INTEGRADAS
// ==========================================

function insertarEnHijo(&$nodoPadre, $subnode)
{
    if ($nodoPadre['id_menu'] == $subnode['mnu_padre']) {
        if (!isset($nodoPadre['submenu'])) {
            $nodoPadre['submenu'] = [];
        }
        $subnode['submenu'] = [];
        $nodoPadre['submenu'][] = $subnode;
        return true;
    }

    if (isset($nodoPadre['submenu']) && is_array($nodoPadre['submenu'])) {
        foreach ($nodoPadre['submenu'] as &$hijo) {
            if (insertarEnHijo($hijo, $subnode)) {
                return true;
            }
        }
    }
    return false;
}

function renderNestableTree(array $menus)
{
    if (empty($menus)) return '';

    $html = '<ol class="dd-list">';

    foreach ($menus as $menu) {
        $hasChildren = !empty($menu['submenu']);

        // Se adapta el atributo data-id al campo real id_menu
        $html .= '<li class="dd-item dd3-item" data-id="' . $menu["id_menu"] . '">';
        $html .= '  <div class="dd-handle dd3-handle"><i class="fa fa-bars text-muted"></i></div>';

        // Estilos de AdminLTE 2 para las barras del árbol
        $html .= '  <div class="dd3-content menu_link" style="background: #fafafa; border: 1px solid #ddd; font-weight: 500; color: #444;">';

        // Adaptación de Icono: de mr-2 a estilo nativo
        $iconoHtml = !empty($menu['mnu_icono']) ? '<i class="' . htmlspecialchars($menu['mnu_icono']) . '" style="margin-right: 8px; width: 16px; text-align: center;"></i> ' : '';

        // Enlace de edición (Abre tu modal mediante JS puro en obtenerDatos)
        $html .= '      <a href="#" onclick="obtenerDatos(' . $menu["id_menu"] . '); return false;" style="color: #444; text-decoration: none;">' . $iconoHtml . htmlspecialchars($menu["mnu_texto"]) . '</a>';

        // Adaptación del botón eliminar: de float-right a pull-right, y de fas fa-trash-alt a fa fa-trash
        $html .= '      <a href="menus/delete/' . $menu["id_menu"] . '" class="eliminar-menu pull-right" title="Eliminar este menú" style="margin-top: 2px;">';
        $html .= '          <i class="fa fa-trash text-danger"></i>';
        $html .= '      </a>';

        $html .= '  </div>';

        // RECURSIÓN REAL: Mantiene la compatibilidad con niveles infinitos
        if ($hasChildren) {
            // Si es un método de clase añade $this->renderNestableTree, si es función de script déjala así:
            $html .= renderNestableTree($menu['submenu']);
        }

        $html .= '</li>';
    }

    $html .= '</ol>';
    return $html;
}
