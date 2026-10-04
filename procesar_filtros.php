<?php
if (isset($_POST['accion']) && $_POST['accion'] == 'cargar_asignaturas') {
    require_once("scripts/clases/class.mysql.php");
    $db = new MySQL; 
    
    $id_paralelo = (int)$_POST['id_paralelo'];

    // Consulta adaptada con us_shortname según la estructura de tu base de datos
    $sql = "SELECT a.id_asignatura, a.as_nombre, u.us_shortname
              FROM sw_paralelo p
              INNER JOIN sw_asignatura_curso ac ON p.id_curso = ac.id_curso
              INNER JOIN sw_asignatura a ON ac.id_asignatura = a.id_asignatura
              LEFT JOIN sw_distributivo d ON (d.id_paralelo = p.id_paralelo AND d.id_asignatura = a.id_asignatura)
              LEFT JOIN sw_usuario u ON d.id_usuario = u.id_usuario
             WHERE p.id_paralelo = " . $id_paralelo . "
               AND a.id_tipo_asignatura = 1 
             ORDER BY ac.ac_orden ASC";

    $consulta = $db->consulta($sql);
    $num_total_registros = $db->num_rows($consulta);
    
    $cadena = "<option value=''>-- Seleccione una asignatura --</option>";

    if ($num_total_registros > 0) {
        while ($asignatura = $db->fetch_assoc($consulta)) {
            $code = $asignatura["id_asignatura"];
            $name = $asignatura["as_nombre"];
            
            // Validamos si la consulta trajo el shortname del docente o si está vacío
            $docente = (!empty($asignatura["us_shortname"])) 
                ? $asignatura["us_shortname"] 
                : "Por asignar";

            // Guardamos el shortname en la propiedad data-docente para el JS
            $cadena .= "<option value='{$code}' data-docente='{$docente}'>{$name}</option>";
        }
    }
    echo $cadena;
}
?>
