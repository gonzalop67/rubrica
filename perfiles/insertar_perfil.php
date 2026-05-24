<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.perfiles.php");
	$perfil = new perfiles();
	$perfil->pe_nombre = $_POST["pe_nombre"];
	echo $perfil->insertarPerfil();
?>
