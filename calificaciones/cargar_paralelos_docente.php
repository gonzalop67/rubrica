<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

session_start();
$id_usuario = $_SESSION["id_usuario"];
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$consulta = $db->consulta("SELECT DISTINCT(c.id_curso)
                             FROM sw_distributivo d,
                                  sw_paralelo pa,
                                  sw_curso c,
                                  sw_especialidad e
                            WHERE d.id_paralelo = pa.id_paralelo 
                              AND pa.id_curso = c.id_curso 
                              AND c.id_especialidad = e.id_especialidad 
                              AND d.id_usuario = $id_usuario
                              AND d.id_periodo_lectivo = $id_periodo_lectivo 
                            ORDER BY c.id_curso, pa.id_paralelo");

$num_total_registros = $db->num_rows($consulta);

$cadena = "<option value=\"0\">Seleccione...</option>";

if ($num_total_registros > 0) {
  while ($curso = $db->fetch_assoc($consulta)) {
    $id_curso = $curso["id_curso"];
    $qry = "SELECT DISTINCT(d.id_paralelo),
                   es_figura, 
                   cu_nombre, 
                   pa_nombre 
              FROM sw_distributivo d,
                   sw_paralelo pa,
                   sw_curso c,
                   sw_especialidad e
             WHERE d.id_paralelo = pa.id_paralelo 
               AND pa.id_curso = c.id_curso 
               AND c.id_especialidad = e.id_especialidad 
               AND d.id_usuario = $id_usuario
               AND d.id_periodo_lectivo = $id_periodo_lectivo 
               AND c.id_curso = $id_curso
             ORDER BY pa_orden";
    $consulta2 = $db->consulta($qry);
    while ($paralelo = $db->fetch_assoc($consulta2)) {
        $id_paralelo = $paralelo["id_paralelo"];
        $name = $paralelo["cu_nombre"] . " " . $paralelo["pa_nombre"] . " - " . $paralelo["es_figura"];
        $cadena .= "<option value=\"$id_paralelo\">$name</option>";
    }
  }
}

echo $cadena;
