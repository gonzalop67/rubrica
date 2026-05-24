<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$id_categoria = $_POST['id_categoria'];

$records = $db->consulta("SELECT cp.id, 
                                 category, 
                                 es_figura, 
                                 cu_nombre,
                                 pa_nombre,
                                 as_nombre 
                            FROM sw_cuestionario_paralelo cp,
                                 sw_exam_category ec,
                                 sw_paralelo p,
                                 sw_curso c,
                                 sw_especialidad e,   
                                 sw_asignatura a 
                           WHERE ec.id = cp.id_category 
                             AND p.id_paralelo = cp.id_paralelo 
                             AND c.id_curso = p.id_curso
                             AND e.id_especialidad = c.id_especialidad 
                             AND a.id_asignatura = cp.id_asignatura
                             AND cp.id_category = $id_categoria 
                           ORDER BY category, as_nombre, pa_orden");

$num_total_registros = $db->num_rows($records);

$cadena = "";

if ($num_total_registros > 0) {
  while ($record = $db->fetch_object($records)) {
    $cadena .= '<tr>';
    $cadena .= '<td>' . $record->id . '</td>';
    $cadena .= '<td>' . $record->category . '</td>';
    $cadena .= '<td>' . $record->es_figura . " - " . $record->cu_nombre . " " . $record->pa_nombre . '</td>';
    $cadena .= '<td>' . $record->as_nombre . '</td>';
    $cadena .= '<td>';
    $cadena .= '<div class="btn-group">';
    $cadena .= '<a href="#" class="btn btn-danger btn-sm item-delete" data="' . $record->id .
      '" title="Eliminar"><span class="fa fa-remove"></span></a>';
    $cadena .= '</div>';
    $cadena .= '</td>';
    $cadena .= '</tr>';
  }
} else {
  $cadena .= "<tr><td colspan='5' align='center'>No se han asociado paralelos a este cuestionario...</td></tr>";
}

echo $cadena;
