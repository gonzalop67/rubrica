<?php
include_once("../scripts/clases/class.mysql.php");
// Recepción de valores enviados mediante POST
$menus = $_POST['menu'];

$db = new MySQL();

foreach ($menus as $var => $value) {
    // $this->where('id', $value->id)->update(['menu_id' => 0, 'orden' => $var + 1]);
    $id_menu = $value['id'];
    $consulta = $db->consulta("UPDATE sw_menu SET mnu_padre = 0, mnu_orden = $var + 1, mnu_nivel = 1 WHERE id_menu = $id_menu");
    
    if (!empty($value['children'])) {
        foreach ($value['children'] as $key => $vchild) {
            $update_id = $vchild['id'];
            $parent_id = $value['id'];
            // $this->where('id', $update_id)->update(['menu_id' => $parent_id, 'orden' => $key + 1]);
            $consulta = $db->consulta("UPDATE sw_menu SET mnu_padre = $parent_id, mnu_orden = $key + 1, mnu_nivel = 2 WHERE id_menu = $update_id");
        }
    }
}
echo count($menus);
