<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

session_start();

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$cadena = "<thead>\n";
$cadena .= "<th></th>\n";
$cadena .= "<th>Periodo</th>\n";

$qry = "SELECT cu_abreviatura 
          FROM sw_curso cu, 
               sw_especialidad es, 
               sw_tipo_educacion tp 
         WHERE es.id_especialidad = cu.id_especialidad 
           AND tp.id_tipo_educacion = es.id_tipo_educacion 
           AND tp.id_periodo_lectivo = $id_periodo_lectivo 
         ORDER BY cu_orden";
$cursos = $db->consulta($qry);
$num_cursos = $db->num_rows($cursos);

while ($curso = $db->fetch_object($cursos)) {
  $cadena .= "<th>\n";
  $cadena .= $curso->cu_abreviatura;
  $cadena .= "</th>\n";
}

$cadena .= "</thead>\n";

$cadena .= "<tbody>\n";

$qry = "SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo";
$periodos = $db->consulta($qry);

if ($db->num_rows($periodos) > 0) {
  while ($periodo = $db->fetch_object($periodos)) {
    $cadena .= "<tr>\n";
    $cadena .= "<td>\n";
    $id_periodo_evaluacion = $periodo->id_periodo_evaluacion;
    $cadena .= "<a href='javascript:;' class='btn btn-danger btn-sm item-delete' data='" . $id . "' title='Eliminar'><span class='fa fa-trash'></span></a>\n";
    $cadena .= "</td>\n";
    $cadena .= "<td class=\"text-center\">$periodo->pe_nombre</td>\n";
    //
    $qry = "SELECT id_curso 
              FROM sw_curso cu, 
                  sw_especialidad es, 
                  sw_tipo_educacion tp 
            WHERE es.id_especialidad = cu.id_especialidad 
              AND tp.id_tipo_educacion = es.id_tipo_educacion 
              AND tp.id_periodo_lectivo = $id_periodo_lectivo 
            ORDER BY cu_orden";
    $cursos = $db->consulta($qry);
    while ($curso = $db->fetch_object($cursos)) {
      $id_curso = $curso->id_curso;
      //
      $qry = "SELECT * 
                FROM sw_periodo_evaluacion_curso 
               WHERE id_periodo_evaluacion = $id_periodo_evaluacion 
                 AND id_curso = $id_curso";
      $periodo_evaluacion_curso = $db->consulta($qry);
      $checked = ($db->num_rows($periodo_evaluacion_curso) == 0) ? "" : "checked";
      //
      $cadena .= "<td align='center'><input type=\"checkbox\" name=\"chk_" . $id_periodo_evaluacion . "_" . $id_curso . "\" $checked  onclick=\"actualizar_asociacion_periodo_evaluacion_curso(this,". $id_periodo_evaluacion . ", " . $id_curso . ")\"></td>\n";
      // $cadena .= "<td class=\"text-center\">$checked</td>\n";
    }
    //
    $cadena .= "</tr>\n";
  }
} else {
  $num_cols = $num_cursos + 2;
  $cadena .= "<tr>\n";
  $cadena .= "<td colspan=\"$num_cols\" align=\"center\">No se han definido periodos de evaluación...</td>\n";
  $cadena .= "</tr>\n";
}

$cadena .= "</tbody>\n";
echo $cadena;
