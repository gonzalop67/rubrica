<?php
require_once("scripts/clases/class.mysql.php");

$db = new MySQL();
session_start();

$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
$id_paralelo = $_POST['id_paralelo'];

$cadena = "<table class='table table-bordered'>\n";
$cadena .= "<thead style='background-color: #b2dafa'>\n";
$cadena .= "<tr>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Nro. Estudiantes</th>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Nro. Mujeres</th>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Nro. Hombres</th>\n";
$cadena .= "</tr>\n";
$cadena .= "</thead>\n";
$cadena .= "<tbody>\n";

// Obtener el numero de estudiantes
$query = "SELECT COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND es_retirado = 'N'";
$consulta = $db->consulta($query);

$registro = $db->fetch_assoc($consulta);
$num_estudiantes = $registro["numero"];

// Obtener el numero de mujeres
$query = "SELECT COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND id_def_genero = 1";
$consulta = $db->consulta($query);

$registro = $db->fetch_assoc($consulta);
$num_mujeres = $registro["numero"];

// Obtener el numero de hombres
$query = "SELECT COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND id_def_genero = 2";
$consulta = $db->consulta($query);

$registro = $db->fetch_assoc($consulta);
$num_hombres = $registro["numero"];

$cadena .= "<tr>\n";
$cadena .= "<td>$num_estudiantes</td>\n";
$cadena .= "<td>$num_mujeres</td>\n";
$cadena .= "<td>$num_hombres</td>\n";
$cadena .= "</tr>\n";

$cadena .= "</tbody>\n";
$cadena .= "</table>\n";

echo $cadena;
