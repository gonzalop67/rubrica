<?php
	include("../scripts/clases/class.mysql.php");
	$id_paralelo = $_POST["id_paralelo"];
	$db = new MySQL();
	$consulta = $db->consulta("SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 GROUP BY id_def_genero ORDER BY id_def_genero");
	$cadena = ""; $suma_generos = 0;
	while($registro = $db->fetch_assoc($consulta))
	{
		$genero = $registro["id_def_genero"];
		$numero = $registro["numero"];
		if($genero == 1){
			$cadena .= "&nbsp;Mujeres: " . $numero . ", ";
		} else {
			$cadena .= "Hombres: " . $numero;
		}
		$suma_generos += $numero;
	}
	echo $cadena . " - Total estudiantes: " . $suma_generos;
?>
