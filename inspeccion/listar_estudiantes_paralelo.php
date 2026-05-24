<?php
include("../scripts/clases/class.mysql.php");
// include("../scripts/clases/class.paralelos.php");
$paralelos = new paralelos();
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$db = new MySQL();
// Obtener quien inserta comportamiento
$consulta = $db->consulta("SELECT nombre 
	FROM sw_periodo_lectivo pl, 
	     sw_quien_inserta_comp co 
	WHERE co.id = pl.quien_inserta_comp_id 
	AND id_periodo_lectivo = $id_periodo_lectivo");
$quien_inserta_comp = $db->fetch_object($consulta)->nombre;
if ($quien_inserta_comp == "Tutor") {
	echo "Tutor";
} else {
	// echo $paralelos->listarEstudiantesComportamiento($id_paralelo, $id_periodo_evaluacion);
	echo "Docentes";
}

// $paralelos = new paralelos();
// $id_paralelo = $_POST["id_paralelo"];
// $id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
// echo $paralelos->listarEstudiantesComportamiento($id_paralelo, $id_periodo_evaluacion);
