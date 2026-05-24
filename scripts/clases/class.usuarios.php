<?php

include_once("class.encrypter.php");

class usuarios extends MySQL
{
	var $code = "";
	var $clave = "";
	var $us_foto = "";
	var $us_login = "";
	var $us_email = "";
	var $id_perfil = "";
	var $us_titulo = "";
	var $us_genero = "";
	var $us_activo = "";
	var $id_usuario = "";
	var $us_nombres = "";
	var $us_fullname = "";
	var $us_password = "";
	var $clave_actual = "";
	var $us_apellidos = "";
	var $us_shortname = "";
	var $id_periodo_lectivo = "";
	var $us_titulo_descripcion = "";
	var $us_perfiles = [];

	function cargarUsuarios()
	{
		$consulta = parent::consulta("SELECT id_usuario, us_fullname FROM sw_usuario WHERE id_perfil = " . $this->code . " ORDER BY us_fullname ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($usuarios = parent::fetch_assoc($consulta)) {
				$code = $usuarios["id_usuario"];
				$name = $usuarios["us_fullname"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function existeUsuario($login, $clave, $id_perfil)
	{
		$clave = encrypter::encrypt($clave);
		$consulta = parent::consulta("SELECT u.id_usuario "
			. " FROM sw_usuario u, "
			. "      sw_perfil p, "
			. "      sw_usuario_perfil up "
			. "WHERE u.id_usuario = up.id_usuario "
			. "  AND p.id_perfil = up.id_perfil "
			. "  AND us_login = '$login' "
			. "  AND us_password = '$clave' "
			. "  AND p.id_perfil = $id_perfil"
			. "  AND us_activo = 1");
		$num_total_registros = parent::num_rows($consulta);
		return $num_total_registros > 0;
	}

	function obtenerIdUsuario($login, $clave, $id_perfil)
	{
		$clave = encrypter::encrypt($clave);
		$consulta = parent::consulta("SELECT u.id_usuario "
			. " FROM sw_usuario u, "
			. "      sw_perfil p, "
			. "      sw_usuario_perfil up "
			. "WHERE u.id_usuario = up.id_usuario "
			. "  AND p.id_perfil = up.id_perfil "
			. "  AND us_login = '$login' "
			. "  AND us_password = '$clave' "
			. "  AND p.id_perfil = $id_perfil");
		$usuario = parent::fetch_object($consulta);
		return $usuario->id_usuario;
	}

	function obtenerDatosUsuario()
	{
		$consulta = parent::consulta("SELECT * FROM sw_usuario WHERE id_usuario = " . $this->code);
		$usuario = parent::fetch_assoc($consulta);
		$usuario["us_password"] = encrypter::decrypt($usuario["us_password"]);
		return json_encode($usuario);
	}

	function obtenerUsuario($id)
	{
		$consulta = parent::consulta("SELECT id_usuario, us_nombres, us_fullname, DES_DECRYPT(us_password) AS us_password, us_foto FROM sw_usuario WHERE id_usuario = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerUsuarios($valor)
	{
		$consulta = parent::consulta("SELECT id_usuario, CONCAT(us_apellidos,' ',us_nombres) AS nombre FROM sw_usuario WHERE us_nombres LIKE '%$valor%' OR us_apellidos LIKE '%$valor%'");
		$resultado = array();
		while ($dato = parent::fetch_assoc($consulta)) {
			$datos = array(
				'id' => $dato['id_usuario'],
				'value' => $dato['nombre']
			);
			array_push($resultado, $datos);
		}
		return json_encode($resultado);
	}

	function obtenerNivelAcceso()
	{
		$consulta = parent::consulta("SELECT pe_nivel_acceso FROM sw_perfil p, sw_usuario u WHERE u.id_perfil = p.id_perfil AND id_usuario = " . $this->code);
		$registro = parent::fetch_array($consulta);
		return json_encode($registro);
	}

	function obtenerNombreUsuario($id)
	{
		$consulta = parent::consulta("SELECT us_titulo, us_apellidos, us_nombres FROM sw_usuario WHERE id_usuario = $id");
		$usuario = parent::fetch_object($consulta);
		return $usuario->us_titulo . " " . $usuario->us_nombres . " " . $usuario->us_apellidos;
	}

	function actualizarClave()
	{
		$qry = "UPDATE sw_usuario SET ";
		$qry .= "us_password = '" . encrypter::encrypt($this->clave) . "'";
		$qry .= " WHERE id_usuario = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Reinicio de sesión requerido para aplicar los cambios. Por favor, cierre sesión y vuelva a iniciar sesión para ver los cambios reflejados.";
		if (!$consulta) {
			$mensaje = "No se pudo actualizar la clave del usuario...Error: " . mysqli_error($this->conexion) . ".Consulta: " . $qry;
			$respuesta = array("error" => true, "mensaje" => $mensaje);
		} else {
			$respuesta = array("error" => false, "mensaje" => $mensaje);
		}
		return json_encode($respuesta);
	}

	function listarUsuarios()
	{
		$cadena = "";
		$consulta = parent::consulta("SELECT up.id_usuario, "
			. "       us_login, "
			. "       us_shortname, "
			. "       pe_nombre, "
			. "       us_activo, "
			. "       us_foto "
			. "  FROM sw_usuario u, "
			. "       sw_perfil p,"
			. "       sw_usuario_perfil up"
			. " WHERE u.id_usuario = up.id_usuario "
			. "   AND p.id_perfil = up.id_perfil "
			. "   AND up.id_perfil = " . $this->id_perfil
			. " ORDER BY us_apellidos, us_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($usuario = parent::fetch_assoc($consulta)) {
				$contador++;
				$cadena .= "<tr>\n";
				$code = $usuario["id_usuario"];
				$login = $usuario["us_login"];
				$nombreCorto = $usuario['us_shortname'];
				$foto = $usuario["us_foto"];
				$cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$login</td>\n";
				$cadena .= "<td>$nombreCorto</td>\n";
				$activo = ($usuario["us_activo"] == 1) ? "Sí" : "No";
				$cadena .= "<td>$activo</td>\n";
				$cadena .= "<td><img src='assets/images/" . $foto . "' class='img-thumbnail' style='width:50px'></td>\n";
				$cadena .= "<td><div class='btn-group'>\n";
				$cadena .= "<a href='javascript:;' class='btn btn-warning item-edit' data='" . $code . "' title='Editar'><span class='fa fa-pencil'></span></a>\n";
				$cadena .= "<a href='javascript:;' class='btn btn-danger item-des-asociar' data='" . $code . "' title='Des-Asociar'><span class='fa fa-remove'></span></a>\n";
				$cadena .= "</div></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='6' align='center'>No se han definido usuarios para este perfil...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function listarUsuariosAsociados()
	{
		$consulta = parent::consulta("SELECT u.id_usuario, "
			. "us_titulo, "
			. "us_fullname, "
			. "us_login, "
			. "pe_nombre "
			. "FROM sw_usuario u, "
			. "sw_perfil p, "
			. "sw_usuario_perfil up "
			. "WHERE u.id_usuario = up.id_usuario "
			. "AND p.id_perfil = up.id_perfil "
			. "AND up.id_perfil = " . $this->id_perfil
			. " ORDER BY pe_nombre, us_apellidos, us_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($usuarios = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $usuarios["id_usuario"];
				$name = $usuarios["us_titulo"] . " " . $usuarios["us_fullname"];
				$login = $usuarios["us_login"];
				$perfil = $usuarios["pe_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$login</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$perfil</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(" . $code . "," . $this->id_perfil . ")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han asociado usuarios para este perfil...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function insertarUsuario($datos)
	{
		/* $consulta = parent::consulta("SELECT * FROM sw_usuario WHERE us_fullname='" . $datos["us_fullname"]);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "Ya existe el Usuario (" . $datos["us_fullname"] . ") en la Base de Datos",
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		} else {
			$consulta = parent::consulta("SELECT * FROM sw_usuario WHERE us_login='" . $datos["us_login"] . "'");
			$num_total_registros = parent::num_rows($consulta);
			if ($num_total_registros > 0) {
				$data = array(
					"titulo"       => "Ocurrió un error inesperado.",
					"mensaje"      => "Ya existe el Login (" . $datos["us_login"] . ") en la Base de Datos",
					"tipo_mensaje" => "error"
				);
				return json_encode($data);
			} else {
				$qry = "INSERT INTO sw_usuario SET ";
				$qry .= "us_titulo = '" . $datos["us_titulo"] . "',";
				$qry .= "us_titulo_descripcion = '" . $datos["us_titulo_descripcion"] . "',";
				$qry .= "us_apellidos = '" . $datos["us_apellidos"] . "',";
				$qry .= "us_nombres = '" . $datos["us_nombres"] . "',";
				$qry .= "us_shortname = '" . $datos["us_shortname"] . "',";
				$qry .= "us_fullname = '" . $datos["us_fullname"] . "',";
				$qry .= "us_login = '" . $datos["us_login"] . "',";
				$clave = encrypter::encrypt($datos["us_password"]);
				$qry .= "us_password = '" . $clave . "',";
				$qry .= "us_genero = '" . $datos["us_genero"] . "',";
				$qry .= "us_foto = '" . $datos["us_foto"] . "'";
				$consulta = parent::consulta($qry);

				$qry = "SELECT MAX(id_usuario) AS max_id FROM sw_usuario";
				$consulta = parent::consulta($qry);
				$registro = parent::fetch_object($consulta);
				$max_id = $registro->max_id;

				for ($i = 0; $i < count($datos["us_perfiles"]); $i++) {
					$qry = "INSERT INTO sw_usuario_perfil VALUES (";
					$qry .= $max_id . ", ";
					$qry .= $datos["us_perfiles"][$i] . ")";
					$consulta = parent::consulta($qry);

					if (!$consulta) {
						$data = array(
							"titulo"       => "Ocurrió un error inesperado.",
							"mensaje"      => "No se pudo insertar el usuario...Error: " . mysqli_error($this->conexion) . "<br />Consulta: " . $qry,
							"tipo_mensaje" => "error"
						);
						return json_encode($data);
					}
				}

				$data = array(
					"titulo"       => "Operación exitosa.",
					"mensaje"      => "Usuario insertado exitosamente...",
					"tipo_mensaje" => "success"
				);
				return json_encode($data);
			}
		} */
	}

	function actualizarUsuario()
	{
		try {
			$qry = "UPDATE sw_usuario SET ";
			$qry .= "us_titulo = '" . $this->us_titulo . "',";
			$qry .= "us_titulo_descripcion = '" . $this->us_titulo_descripcion . "',";
			$qry .= "us_apellidos = '" . $this->us_apellidos . "',";
			$qry .= "us_nombres = '" . $this->us_nombres . "',";
			$qry .= "us_fullname = '" . $this->us_fullname . "',";
			$qry .= "us_shortname = '" . $this->us_shortname . "',";
			$qry .= "us_email = '" . $this->us_email . "',";
			$qry .= "us_login = '" . $this->us_login . "',";
			$clave = encrypter::encrypt($this->us_password);
			$qry .= "us_password = '" . $clave . "',";
			$qry .= "us_activo = " . $this->us_activo . ",";
			$qry .= "us_genero = '" . $this->us_genero . "',";
			$qry .= "us_foto = '" . $this->us_foto . "'";
			$qry .= " WHERE id_usuario = $this->code";
			$consulta = parent::consulta($qry);

			$consulta = parent::consulta("DELETE FROM sw_usuario_perfil WHERE id_usuario = $this->code");

			for ($i = 0; $i < count($this->us_perfiles); $i++) {
				$qry = "INSERT INTO sw_usuario_perfil VALUES (";
				$qry .= $this->code . ", ";
				$qry .= $this->us_perfiles[$i] . ")";
				$consulta = parent::consulta($qry);
			}

			$data = array(
				"titulo"         => "Operación exitosa.",
				"mensaje"        => "Usuario actualizado exitosamente...",
				"tipo_mensaje"   => "success"
			);
		} catch (Exception $e) {
			$data = array(
				"titulo"         => "Ocurrió un error inesperado.",
				"mensaje"        => "No se pudo actualizar el usuario en la Base de Datos. Error: " . $e->getMessage(),
				"tipo_mensaje"   => "error"
			);
		}

		return json_encode($data);
	}

	function eliminarUsuario()
	{
		// SOFT DELETE: Obtenemos la fecha y hora actual para la marca de tiempo
		$now = date('Y-m-d H:i:s');

		try {
			// Al implementar SoftDelete, ya no dependemos de si tiene llaves foráneas o registros 
			// vinculados en 'sw_distributivo' o 'sw_paralelo_tutor' para decidir si se borra o no.
			// Directamente aplicamos la marca de tiempo para enviarlo a la papelera.
			$qry = "UPDATE sw_usuario SET deleted_at = '{$now}' WHERE id_usuario = " . $this->code;

			// Ejecutamos la consulta utilizando tu método heredado
			parent::consulta($qry);

			// NOTA DE SEGURIDAD E INTEGRIDAD:
			// Hemos removido el bloque 'unlink($us_foto)'. Al ser una eliminación lógica, 
			// el usuario puede ser restaurado desde la papelera en el futuro y necesitará su avatar intacto.

			// Mapeamos la estructura JSON para que sea 100% compatible con tu JavaScript y SweetAlert2
			$data = array(
				"success" => true,
				"titulo"  => "Operación exitosa.",
				"message" => "Usuario enviado a la papelera de reciclaje exitosamente...",
				"estado"  => "success"
			);
		} catch (Exception $e) {
			$data = array(
				"success" => false,
				"titulo"  => "Ocurrió un error inesperado.",
				"message" => "No se pudo procesar la eliminación en la Base de Datos. Error: " . $e->getMessage(),
				"estado"  => "error"
			);
		}

		return json_encode($data);
	}

	function restoreUsuario()
	{
		try {
			// SOFT DELETE: Cambiamos 'deleted_at' de vuelta a NULL para activar el registro
			$qry = "UPDATE sw_usuario SET deleted_at = NULL WHERE id_usuario = " . $this->code;

			// Ejecutamos la consulta utilizando tu método heredado
			parent::consulta($qry);

			// Mapeamos la estructura JSON lista para responder a tu petición AJAX y SweetAlert2
			$data = array(
				"success" => true,
				"titulo"  => "Operación exitosa.",
				"message" => "Usuario restaurado de la papelera exitosamente...",
				"estado"  => "success"
			);
		} catch (Exception $e) {
			$data = array(
				"success" => false,
				"titulo"  => "Ocurrió un error inesperado.",
				"message" => "No se pudo restaurar el usuario de la Base de Datos. Error: " . $e->getMessage(),
				"estado"  => "error"
			);
		}

		return json_encode($data);
	}

	function destruirUsuarioPermanente()
	{
		// 1. Consultamos el registro antes de borrarlo para conocer el nombre del archivo de la foto
		$consulta = parent::consulta("SELECT * FROM sw_usuario WHERE id_usuario = " . $this->code);
		$registro = parent::fetch_object($consulta);

		$us_foto = 'public/uploads/' . $registro->us_foto;

		try {
			// Realizamos el DELETE físico definitivo de la base de datos
			$qry = "DELETE FROM sw_usuario WHERE id_usuario = " . $this->code;
			parent::consulta($qry);

			// Como el registro ya no existe en el sistema, ahora sí borramos físicamente el archivo del servidor
			if (!empty($registro->us_foto) && $registro->us_foto !== 'default.png' && file_exists($us_foto)) {
				unlink($us_foto);
			}

			$data = array(
				"success" => true,
				"titulo"  => "Eliminado definitivamente.",
				"message" => "El usuario y sus archivos han sido borrados de forma permanente.",
				"estado"  => "success"
			);
		} catch (Exception $e) {
			$data = array(
				"success" => false,
				"titulo"  => "Error crítico.",
				"message" => "No se pudo borrar permanentemente. Verifique restricciones de integridad: " . $e->getMessage(),
				"estado"  => "error"
			);
		}

		return json_encode($data);
	}

	function buscarUsuario($patron)
	{
		$qry = "SELECT us_login FROM sw_usuario WHERE us_login LIKE '" . $patron . "%' ORDER BY us_login";
		$consulta = parent::consulta($qry);
		$arreglo_php = array();
		if (mysqli_num_rows($consulta) == 0)
			array_push($arreglo_php, "");
		else {
			while ($palabras = mysqli_fetch_array($consulta)) {
				array_push($arreglo_php, $palabras["us_login"]);
			}
		}
		return json_encode($arreglo_php);
	}

	function contarCalificacionesErroneasDocente($id_usuario)
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_rubrica_estudiante r, sw_asignatura a, sw_estudiante e,  sw_paralelo_asignatura pa, sw_usuario u, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE r.id_paralelo = pa.id_paralelo AND r.id_asignatura = pa.id_asignatura AND r.id_asignatura = a.id_asignatura AND pa.id_usuario = u.id_usuario AND r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND r.id_estudiante = e.id_estudiante AND pa.id_usuario = $id_usuario AND re_calificacion > 10");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listarCalificacionesErroneasDocente($id_usuario)
	{
		$consulta = parent::consulta("SELECT as_nombre, es_apellidos, es_nombres, pe_nombre, cu_nombre, pa_nombre, ap_nombre, ru_nombre, r.id_rubrica_estudiante, re_calificacion FROM sw_rubrica_estudiante r, sw_asignatura a, sw_estudiante e,  sw_paralelo_asignatura pa, sw_usuario u, sw_curso cu, sw_paralelo p, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE r.id_paralelo = pa.id_paralelo AND r.id_asignatura = pa.id_asignatura AND r.id_asignatura = a.id_asignatura AND pa.id_paralelo = p.id_paralelo AND p.id_curso = cu.id_curso AND pa.id_usuario = u.id_usuario AND r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND r.id_estudiante = e.id_estudiante AND pa.id_usuario = $id_usuario AND re_calificacion > 10 ORDER BY as_nombre, es_apellidos, es_nombres");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($calificacion = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$id_rubrica_estudiante = $calificacion["id_rubrica_estudiante"];
				$asignatura = $calificacion["as_nombre"];
				$estudiante = $calificacion["es_apellidos"] . " " . $calificacion["es_nombres"];
				$curso = $calificacion["cu_nombre"] . " \"" . $calificacion["pa_nombre"] . "\"";
				$periodo = $calificacion["pe_nombre"];
				$aporte = $calificacion["ap_nombre"];
				$rubrica = $calificacion["ru_nombre"];
				$nota = $calificacion["re_calificacion"];
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"15%\">$asignatura</td>\n";
				$cadena .= "<td width=\"15%\">$estudiante</td>\n";
				$cadena .= "<td width=\"15%\">$curso</td>\n";
				$cadena .= "<td width=\"15%\">$periodo</td>\n";
				$cadena .= "<td width=\"15%\">$aporte</td>\n";
				$cadena .= "<td width=\"15%\">$rubrica</td>\n";
				$cadena .= "<td width=\"5%\" class=\"link_table\"><a href=\"#\" onclick=\"editarCalificacionErronea(" . $id_rubrica_estudiante . ")\">$nota</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han encontrado calificaciones err&oacute;neas...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function asociarUsuarioPerfil()
	{
		//Primero verificar si ya se realizo la asociacion
		$qry = "SELECT * FROM sw_usuario_perfil WHERE id_perfil = " . $this->id_perfil
			. " AND id_usuario = " . $this->id_usuario;
		$consulta = parent::consulta($qry);
		if (parent::num_rows($consulta) > 0) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "Este usuario ya fue asociado anteriormente...",
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		} else {
			$qry = "INSERT INTO sw_usuario_perfil (id_perfil, id_usuario) VALUES (";
			$qry .= $this->id_perfil . ",";
			$qry .= $this->id_usuario . ")";
			$consulta = parent::consulta($qry);
			if (!$consulta) {
				$data = array(
					"titulo"       => "Ocurrió un error inesperado.",
					"mensaje"      => "No se pudo actualizar el usuario...Error: " . mysqli_error($this->conexion),
					"tipo_mensaje" => "error"
				);
				return json_encode($data);
			} else {
				$data = array(
					"titulo"       => "Operación exitosa.",
					"mensaje"      => "Usuario asociado exitosamente...",
					"tipo_mensaje" => "success"
				);
				return json_encode($data);
			}
		}
	}

	function eliminarUsuarioPerfil()
	{
		$qry = "SELECT * FROM sw_usuario_perfil WHERE id_usuario = " . $this->id_usuario;
		$consulta = parent::consulta($qry);
		if (parent::num_rows($consulta) == 1) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "No se puede des-asociar porque este es el único perfil asociado...",
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		} else {
			$qry = "DELETE FROM sw_usuario_perfil WHERE id_perfil = " . $this->id_perfil .
				" AND id_usuario = " . $this->id_usuario;
			$consulta = parent::consulta($qry);
			$mensaje = "Usuario des-asociado exitosamente...";
			if (!$consulta) {
				$data = array(
					"titulo"       => "Ocurrió un error inesperado.",
					"mensaje"      => "No se pudo des-asociar el usuario...Error: " . mysqli_error($this->conexion),
					"tipo_mensaje" => "error"
				);
				return json_encode($data);
			} else {
				$data = array(
					"titulo"       => "Operación exitosa.",
					"mensaje"      => "Usuario des-asociado exitosamente...",
					"tipo_mensaje" => "success"
				);
				return json_encode($data);
			}
		}
	}
}
