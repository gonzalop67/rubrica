<?php
if (isset($_POST['accion']) && $_POST['accion'] == 'cargar_asignaturas') {
    require_once("scripts/clases/class.mysql.php");
    // Suponiendo que tu objeto hereda o instancia la función creada en el paso 2
    $db = new MySQL; 
    
    $id_paralelo = (int)$_POST['id_paralelo'];
    //
    $sql = "SELECT a.id_asignatura, a.as_nombre 
              FROM sw_paralelo p
              INNER JOIN sw_asignatura_curso ac ON p.id_curso = ac.id_curso
              INNER JOIN sw_asignatura a ON ac.id_asignatura = a.id_asignatura
             WHERE p.id_paralelo = " . (int)$id_paralelo . "
               AND a.id_tipo_asignatura = 1 
             ORDER BY ac.ac_orden ASC";

    $consulta = $db->consulta($sql);
    $num_total_registros = $db->num_rows($consulta);
    
    $cadena = "<option value=''>-- Seleccione una asignatura --</option>";

    if ($num_total_registros > 0) {
        while ($asignatura = $db->fetch_assoc($consulta)) {
            $code = $asignatura["id_asignatura"];
            $name = $asignatura["as_nombre"];
            $cadena .= "<option value='{$code}'>{$name}</option>";
        }
    }
    echo $cadena;
}
?>
