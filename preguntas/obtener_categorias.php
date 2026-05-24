<?php
include_once "../scripts/clases/class.mysql.php";

$id_usuario = $_POST['id_usuario'];

$db = new MySQL();
$categorias = $db->consulta("SELECT id, category FROM sw_exam_category WHERE id_usuario = " . $id_usuario . " ORDER BY category ASC");
$num_total_registros = $db->num_rows($categorias);
$cadena = "";
if ($num_total_registros > 0) {
    while ($categoria = $db->fetch_object($categorias)) {
        $code = $categoria->id;
        $name = $categoria->category;
        $cadena .= "<option value=\"$code\">$name</option>\n";
    }
}
echo $cadena;
