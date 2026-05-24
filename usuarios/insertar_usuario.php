<?php
require_once "upload_image.php";
require_once "../scripts/clases/class.mysql.php";
require_once "../scripts/clases/class.encrypter.php";

$db = new MySQL();

$us_fullname = $_POST["new_us_apellidos"] . " " . $_POST["new_us_nombres"];

$consulta = $db->consulta("SELECT * FROM sw_usuario WHERE us_fullname='" . $us_fullname . "'");
$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
	$data = array(
		"titulo"       => "Ocurrió un error inesperado.",
		"mensaje"      => "Ya existe el Usuario (" . $us_fullname . ") en la Base de Datos",
		"tipo_mensaje" => "error"
	);
} else {
	$consulta = $db->consulta("SELECT * FROM sw_usuario WHERE us_login='" . $_POST["new_us_login"] . "'");
	$num_total_registros = $db->num_rows($consulta);
	if ($num_total_registros > 0) {
		$data = array(
			"titulo"       => "Ocurrió un error inesperado.",
			"mensaje"      => "Ya existe el Login (" . $_POST["new_us_login"] . ") en la Base de Datos",
			"tipo_mensaje" => "error"
		);
	} else {
		$image = upload_image("new_us_foto");

		$datos = [
			'us_titulo' => trim($_POST["new_us_titulo"]),
			'us_titulo_descripcion' => trim($_POST["new_us_titulo_descripcion"]),
			'us_apellidos' => trim($_POST["new_us_apellidos"]),
			'us_nombres' => trim($_POST["new_us_nombres"]),
			'us_shortname' => trim($_POST["new_us_shortname"]),
			'us_fullname' => trim($_POST["new_us_apellidos"]) . " " . trim($_POST["new_us_nombres"]),
			'us_email' => trim($_POST["new_us_email"]),
			'us_login' => trim($_POST["new_us_login"]),
			'us_password' => trim($_POST["new_us_password"]),
			'us_genero' => $_POST["new_us_genero"],
			'us_activo' => $_POST["new_us_activo"],
			'us_perfiles' => $_POST["new_id_perfil"],
			'us_foto' => $image
		];

		try {

			$qry = "INSERT INTO sw_usuario SET ";
			$qry .= "us_titulo = '" . $datos["us_titulo"] . "',";
			$qry .= "us_titulo_descripcion = '" . $datos["us_titulo_descripcion"] . "',";
			$qry .= "us_apellidos = '" . $datos["us_apellidos"] . "',";
			$qry .= "us_nombres = '" . $datos["us_nombres"] . "',";
			$qry .= "us_shortname = '" . $datos["us_shortname"] . "',";
			$qry .= "us_fullname = '" . $datos["us_fullname"] . "',";
			$qry .= "us_email = '" . $datos["us_email"] . "',";
			$qry .= "us_login = '" . $datos["us_login"] . "',";
			$clave = encrypter::encrypt($datos["us_password"]);
			$qry .= "us_password = '" . $clave . "',";
			$qry .= "us_genero = '" . $datos["us_genero"] . "',";
			$qry .= "us_foto = '" . $datos["us_foto"] . "',";
			$qry .= "us_activo = " . $datos["us_activo"];

			$consulta = $db->consulta($qry);

			$qry = "SELECT MAX(id_usuario) AS max_id FROM sw_usuario";
			$consulta = $db->consulta($qry);
			$registro = $db->fetch_object($consulta);
			$max_id = $registro->max_id;

			for ($i = 0; $i < count($datos["us_perfiles"]); $i++) {
				$qry = "INSERT INTO sw_usuario_perfil VALUES (";
				$qry .= $max_id . ", ";
				$qry .= $datos["us_perfiles"][$i] . ")";
				$consulta = $db->consulta($qry);
			}

			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "Usuario insertado exitosamente...",
				"tipo_mensaje" => "success"
			);
		} catch (Exception $e) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "No se pudo insertar el usuario en la Base de Datos. Error: " . $e->getMessage(),
				"tipo_mensaje" => "error"
			);
		}
	}
}

echo json_encode($data);
