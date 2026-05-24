<?php

	require_once "upload_image.php";

	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	if($_FILES["edit_us_foto"]["name"]==""){
        $image = $_POST["bd_us_avatar"];
    }else{ 
		$image = upload_image("edit_us_foto");
	} 
	$usuario = new usuarios();
	$usuario->code = $_POST["edit_id_usuario"];
	$usuario->us_titulo = $_POST["edit_us_titulo"];
	$usuario->us_titulo_descripcion = $_POST["edit_us_titulo_descripcion"];
	$usuario->us_apellidos = $_POST["edit_us_apellidos"];
	$usuario->us_nombres = $_POST["edit_us_nombres"];
	$usuario->us_shortname = $_POST["edit_us_shortname"];
	$usuario->us_fullname = $usuario->us_apellidos . " " . $usuario->us_nombres;
	$usuario->us_email = $_POST["edit_us_email"];
	$usuario->us_login = $_POST["edit_us_login"];
	$usuario->us_password = $_POST["edit_us_password"];
	$usuario->us_activo = $_POST["edit_us_activo"];
	$usuario->us_genero = $_POST["edit_us_genero"];
	$usuario->us_foto = $image;
	$usuario->us_perfiles = $_POST["edit_id_perfil"];
	echo $usuario->actualizarUsuario();
?>
