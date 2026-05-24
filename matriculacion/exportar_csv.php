<?php
//export.php  
if (isset($_POST["export"])) {
    include("../scripts/clases/class.mysql.php");
    $db = new MySQL();

    $id_paralelo = $_POST['id_paralelo'];

    header('Content-Type: text/csv; charset=utf-8');
    $fn = "csv_" . uniqid() . ".csv";
    header('Content-Disposition: attachment; filename=' . $fn);
    $output = fopen("php://output", "w");
    fputcsv($output, array('td_nombre', 'es_cedula', 'dn_nombre', 'es_apellidos', 'es_nombres', 'dg_nombre', 'es_email', 'es_sector', 'es_direccion', 'es_telefono', 'es_fec_nacim'));

    $query = "SELECT td_nombre, es_cedula, dn_nombre, es_apellidos, es_nombres, dg_nombre, es_email, es_sector, es_direccion, es_telefono, es_fec_nacim FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_def_genero dg, sw_tipo_documento td, sw_def_nacionalidad dn WHERE e.id_estudiante = ep.id_estudiante AND dg.id_def_genero = e.id_def_genero AND td.id_tipo_documento = e.id_tipo_documento AND dn.id_def_nacionalidad = e.id_def_nacionalidad AND activo = 1 and id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC";

    $result = $db->consulta($query);

    while ($row = $db->fetch_assoc($result)) {
        fputcsv($output, $row);
    }

    fclose($output);
}
