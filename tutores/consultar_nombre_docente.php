<?php
	require_once("../scripts/clases/class.mysql.php");
	$db = new mysql();
  $id_asignatura = $_POST["id_asignatura"];
  $id_paralelo = $_POST["id_paralelo"];
  $consulta = $db->consulta("SELECT us_titulo,
                                    us_apellidos,
                                    us_nombres
                               FROM sw_distributivo di,
                                    sw_usuario u
                              WHERE u.id_usuario = di.id_usuario
                                AND di.id_asignatura = $id_asignatura
                                AND di.id_paralelo = $id_paralelo");
  $docente = $db->fetch_object($consulta);
	echo "DOCENTE: " . $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;
?>
