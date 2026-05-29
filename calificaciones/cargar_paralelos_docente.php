<?php
header('Content-Type: text/html; charset=utf-8');
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Forzar a Edge a aceptar la cookie de sesión en entornos locales o HTTP convencionales
ini_set('session.cookie_samesite', 'Lax'); 
session_start();

// Validar si las variables existen; si no, les asignamos un valor por defecto (0) para evitar que rompan el SQL
$id_usuario = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : 0;
$id_periodo_lectivo = isset($_SESSION["id_periodo_lectivo"]) ? $_SESSION["id_periodo_lectivo"] : 0;

// Imprimimos el log para auditar desde la consola de Edge
echo "<!-- AUDITORÍA DE SESIÓN -> Usuario: $id_usuario | Periodo: $id_periodo_lectivo -->\n";

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

// SOLUCIÓN 1: Inicializar la variable para evitar errores de tipo Notice
$cadena = '<option value="0">Seleccione...</option>';

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

// Imprime todo el bloque limpio de una sola vez
echo $cadena;
