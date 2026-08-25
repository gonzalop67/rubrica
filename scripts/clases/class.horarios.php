<?php

class horarios extends MySQL
{

	var $code = "";
	var $id_paralelo = "";
	var $id_asignatura = "";
	var $id_hora_clase = "";
	var $id_horario_def = "";
	var $id_periodo_lectivo = "";

	var $id_dia_semana = "";

	function listarHorarioDocente($id_usuario, $id_horario_def)
	{
		$dias_semana = "";
		$horas_clase = "";

		$qryString1 = "SELECT * FROM sw_dia_semana WHERE id_horario_def = $id_horario_def";
		$consulta = parent::consulta($qryString1);

		if (parent::num_rows($consulta) > 0) {
			$dias_semana .= "<tr>";
			$dias_semana .= "<th>HORA</th>";
			while ($dia = parent::fetch_object($consulta)) {
				$dias_semana .= "<th>$dia->ds_nombre</th>";
			}
			$dias_semana .= "</tr>";
			$qryString2 = "SELECT DISTINCT(id_hora_clase) 
                             FROM sw_hora_dia hd, 
                                  sw_dia_semana ds 
                            WHERE ds.id_dia_semana = hd.id_dia_semana 
                              AND ds.id_horario_def = $id_horario_def";
			$rs2 = parent::consulta($qryString2);
			while ($hora = parent::fetch_object($rs2)) {
				$horas_clase .= "<tr>";
				$id_hora_clase = $hora->id_hora_clase;
				// Consulto el nombre de la hora clase
				$consulta = parent::consulta("SELECT hc_nombre FROM sw_hora_clase WHERE id_hora_clase = $id_hora_clase");
				$hora_clase = parent::fetch_object($consulta);
				$horas_clase .= "<td class='text-center'><span style='font-size: 12pt'><strong>$hora_clase->hc_nombre</strong></span></td>\n";

				// Acá obtengo los dias de la semana asociados al periodo lectivo
				$dias = parent::consulta("SELECT id_dia_semana 
				                            FROM sw_dia_semana 
			                               WHERE id_horario_def = $id_horario_def
										   ORDER BY ds_orden");

				while ($dia = parent::fetch_object($dias)) {
					$id_dia_semana = $dia->id_dia_semana;
					// Consulto la asignatura del dia y hora correspondientes
					$qryString3 = "SELECT a.id_asignatura,
										  as_nombre, 
										  pa_nombre, 
										  cu_nombre, 
										  es_figura 
								     FROM sw_horario ho, 
										  sw_hora_clase hc, 
										  sw_asignatura a, 
										  sw_paralelo pa, 
										  sw_curso cu, 
										  sw_especialidad es  
								    WHERE ho.id_hora_clase = hc.id_hora_clase 
									  AND ho.id_asignatura = a.id_asignatura 
									  AND pa.id_paralelo = ho.id_paralelo 
									  AND cu.id_curso = pa.id_curso 
									  AND es.id_especialidad = cu.id_especialidad  
									  AND ho.id_dia_semana = $id_dia_semana 
									  AND ho.id_hora_clase = $id_hora_clase 
									  AND id_usuario = $id_usuario";
					$consulta = parent::consulta($qryString3);
					$asignatura = parent::fetch_object($consulta);
					if ($asignatura) {
						$horas_clase .= "<td>\n";
						$horas_clase .= "<p>$asignatura->as_nombre</p>\n";
						$paralelo = $asignatura->cu_nombre . " " . $asignatura->pa_nombre . " - " . $asignatura->es_figura;
						$horas_clase .= "<p><em>$paralelo</em></p>\n";
						$horas_clase .= "</td>\n";
					} else {
						$horas_clase .= "<td>&nbsp;</td>\n";
					}
				}

				$horas_clase .= "</tr>";
			}
		} else {
			$dias_semana .= "<div align='center'>No se han definido los días de la semana para este periodo lectivo...</div>";
		}

		$datos = [
			"dias_semana" => $dias_semana,
			"horas_clase" => $horas_clase
		];

		return json_encode($datos);
	}

	function listarHorarioParalelo($id_paralelo, $id_dia_semana, $id_horario_def)
	{
		$cadena = "<table class=\"fuente9\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		// Primero debo obtener las horas clase del dia de la semana...
		$consulta = parent::consulta("SELECT id_horario,
											 hc_nombre,
											 as_nombre, 
											 DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, 
											 DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
											 us_shortname AS docente, 
											 us_foto
										FROM sw_horario ho,
											 sw_hora_clase hc,
											 sw_asignatura a,
											 sw_usuario u
									   WHERE hc.id_hora_clase = ho.id_hora_clase
										 AND a.id_asignatura = ho.id_asignatura
										 AND u.id_usuario = ho.id_usuario
										 AND id_paralelo = $id_paralelo
										 AND id_dia_semana = $id_dia_semana 
										 AND ho.id_horario_def = $id_horario_def
									   ORDER BY hc_orden
		");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			while ($horario = parent::fetch_assoc($consulta)) {
				$hora = $horario["hc_nombre"] . " (" . $horario["hora_inicio"] . " - " . $horario["hora_fin"] . ")";
				$asignatura = $horario["as_nombre"];
				$docente = $horario["docente"];
				$cadena .= "<tr>\n";
				$code = $horario["id_horario"];
				$us_foto = ($horario["us_foto"] == '') ? 'public/uploads/no-disponible.png' : 'public/uploads/' . $horario["us_foto"];
				$cadena .= "<td><input name='row-check' type='checkbox' class='delete_checkbox' value='$code'></td>\n";
				$cadena .= "<td>$hora</td>\n";
				$cadena .= "<td>$asignatura</td>\n";
				$cadena .= "<td><img class='img-thumbnail' width='40' src='$us_foto'> $docente</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han asociado horas clase para el paralelo y día de la semana elegidos...</td>\n";
			$cadena .= "</tr>\n";
		}

		$cadena .= "</table>";
		return $cadena;
	}

	function cargarHorarioMatriz($id_paralelo, $id_horario_def)
	{
		// Limpiamos los datos de entrada
		$id_paralelo    = $this->filtrar($id_paralelo);
		$id_horario_def = $this->filtrar($id_horario_def);

		// PASO 1: Obtener la lista de horas de la plantilla (Las filas del tablero)
		$sqlHoras = "SELECT id_horario_detalle, nombre, hora_inicio, hora_fin 
					FROM sw_horario_detalles 
					WHERE id_horario_def = '$id_horario_def' 
					ORDER BY hora_inicio ASC";
		$resHoras = parent::consulta($sqlHoras);

		$arrHoras = array();
		while ($row = parent::fetch_assoc($resHoras)) {
			$arrHoras[] = $row;
		}

		// PASO 2: Obtener las asignaciones que ya existen (Ajustado a sw_horario_clases)
		$sqlClases = "SELECT 
                    h.dia_semana AS id_dia_semana, 
                    h.id_horario_detalle AS id_hora_clase, 
                    h.id_asignatura, 
                    a.as_nombre,
                    u.us_foto,
                    u.us_shortname,
                    u.us_titulo,
                    u.us_genero
                FROM sw_horario_clases h 
                INNER JOIN sw_asignatura a ON h.id_asignatura = a.id_asignatura 
                INNER JOIN sw_usuario u    ON h.id_usuario = u.id_usuario
                WHERE h.id_paralelo = '$id_paralelo' 
                AND h.id_horario_def = '$id_horario_def'";

		$resClases = parent::consulta($sqlClases);
		$arrClases = array();
		while ($row = parent::fetch_assoc($resClases)) {
			$arrClases[] = $row;
		}

		// PASO 3: Devolvemos ambos paquetes juntos en un objeto organizado
		$respuesta = array(
			"horas" => $arrHoras,
			"clases" => $arrClases
		);

		return json_encode($respuesta);
	}

	function existeAsignaturaHoraClase($id_paralelo, $id_dia_semana, $id_hora_clase, $id_horario_def)
	{
		// Limpiamos los datos con tu método de filtrado para evitar inyecciones SQL
		// Si estás dentro de la misma clase que extiende de MySQL, usas $this->filtrar()
		$id_paralelo        = $this->filtrar($id_paralelo);
		$id_dia_semana      = $this->filtrar($id_dia_semana);
		$id_hora_clase      = $this->filtrar($id_hora_clase);
		$id_horario_def     = $this->filtrar($id_horario_def);

		// CORREGIDO: Nombres de tabla y columnas actualizados
		$sql = "SELECT id_horario_clase 
            FROM sw_horario_clases 
            WHERE id_paralelo = '$id_paralelo' 
              AND dia_semana = '$id_dia_semana' 
              AND id_horario_detalle = '$id_hora_clase' 
              AND id_horario_def = '$id_horario_def'";

		$consulta = parent::consulta($sql);
		$num_total_registros = parent::num_rows($consulta);

		return $num_total_registros > 0;
	}

	function comprobarCruceDeHorario($id_paralelo, $id_asignatura, $id_dia_semana, $id_hora_clase, $id_horario_def)
	{
		//Primero obtengo el id_usuario para luego verificar si existe otra hora con el mismo docente
		$consulta = parent::consulta("SELECT id_usuario FROM sw_distributivo WHERE id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
		if (parent::num_rows($consulta) > 0) {
			$registro = parent::fetch_assoc($consulta);
			$id_usuario = $registro['id_usuario'];
			//Ahora hay que verificar si existe otra hora dentro del horario para el mismo docente
			$consulta = parent::consulta("SELECT id_horario FROM sw_horario WHERE id_dia_semana = $id_dia_semana AND id_hora_clase = $id_hora_clase AND id_usuario = $id_usuario AND id_horario_def = $id_horario_def");
			$num_total_registros = parent::num_rows($consulta);
			if ($num_total_registros > 0) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 2;
		}
	}

	function asociarAsignaturaHoraClase()
	{
		// SEGURIDAD: Forzamos a que todas las propiedades sean números enteros limpios.
		// Si desde JavaScript viajó un "undefined" o un texto vacío, aquí se transformará en 0, 
		// evitando por completo el error fatal de sintaxis SQL.
		$id_paralelo        = (int)$this->id_paralelo;
		$id_asignatura      = (int)$this->id_asignatura;
		$id_horario_detalle = (int)$this->id_hora_clase; // Mapea tu propiedad al id_horario_detalle real
		$dia_semana         = (int)$this->id_dia_semana; // Mapea tu propiedad al dia_semana real
		$id_horario_def     = (int)$this->id_horario_def;

		// Primero obtenemos el id_usuario (docente) asignado a esta materia en este paralelo
		$sqlDocente = "SELECT id_usuario FROM sw_distributivo 
                   WHERE id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura 
                   LIMIT 1";
		$consulta = parent::consulta($sqlDocente);

		if (parent::num_rows($consulta) > 0) {
			$registro = parent::fetch_assoc($consulta);
			$id_usuario = $registro['id_usuario'];

			// Si el docente existe pero es nulo o cero en la relación distributiva
			if (empty($id_usuario)) {
				$id_usuario = "NULL";
			}

			// CORREGIDO: Inserción apuntando exactamente a tu nueva tabla 'sw_horario_clases' 
			// y a tus nombres de columnas reales actualizados
			$qry = "INSERT INTO sw_horario_clases (
                    id_paralelo, 
                    id_asignatura, 
                    dia_semana, 
                    id_horario_detalle, 
                    id_usuario, 
                    id_horario_def
                ) VALUES (
                    $id_paralelo,
                    $id_asignatura,
                    $dia_semana,
                    $id_horario_detalle,
                    $id_usuario,
                    $id_horario_def
                )";

			$consulta = parent::consulta($qry);

			if ($consulta) {
				$data = [
					'error' => false,
					'mensaje' => "Asignatura asociada exitosamente...",
				];
			} else {
				$data = [
					'error' => true,
					'mensaje' => "No se pudo asociar la Asignatura..."
				];
			}
		} else {
			$data = [
				'error' => true,
				'mensaje' => "No se ha asignado un docente a esta asignatura..."
			];
		}

		return json_encode($data);
	}

	function cargarTitulosHorarios()
	{
		$consulta = parent::consulta("SELECT * FROM sw_horario_def WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY fecha_inicial DESC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($horario = parent::fetch_assoc($consulta)) {
				$code = $horario["id_horario_def"];
				$titulo = $horario["ho_titulo"];
				$cadena .= "<option value='$code'>\n";
				$cadena .= "$titulo\n";
				$cadena .= "</option>\n";
			}
		}
		return $cadena;
	}

	function obtenerTituloHorario()
	{
		$consulta = parent::consulta("SELECT * FROM sw_horario_def WHERE id_horario_def = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function insertarTituloHorario($datos)
	{
		$id_periodo_lectivo = $datos['id_periodo_lectivo'];
		$ho_titulo          = $datos['ho_titulo'];
		$fecha_inicial      = $datos['fecha_inicial'];
		$fecha_final        = $datos['fecha_final'];
		$hora_entrada       = $datos['hora_entrada']; // Ej: "07:30"
		$nro_horas          = (int)$datos['nro_horas']; // Ej: 6
		$duracion           = (int)$datos['duracion'];  // Ej: 45 (minutos)

		// 1. Inserción del Título del Horario
		$sql_insert_horario = "INSERT INTO sw_horario_def SET 
                            id_periodo_lectivo = '$id_periodo_lectivo', 
                            ho_titulo = '$ho_titulo', 
                            fecha_inicial = '$fecha_inicial', 
                            fecha_final = '$fecha_final', 
                            hora_entrada = '$hora_entrada', 
                            nro_horas = '$nro_horas', 
                            duracion = '$duracion', 
                            status = 1";

		$consulta = parent::consulta($sql_insert_horario);

		if ($consulta) {
			// Obtener el ID del horario recién creado
			$consulta_id = parent::consulta("SELECT LAST_INSERT_ID() AS last_insert_id");
			$last_insert_id = parent::fetch_object($consulta_id)->last_insert_id;

			// ==========================================
			// PROCESO 1: Insertar los Días de la Semana
			// ==========================================
			$query_dias = parent::consulta("SELECT * FROM sw_config_dias_semana ORDER BY orden ASC");
			$valores_dias = [];
			while ($dia_semana = parent::fetch_assoc($query_dias)) {
				$valores_dias[] = "('$dia_semana[id_config_dias_semana]', '$last_insert_id', '$dia_semana[nombre]', '$dia_semana[orden]')";
			}
			if (!empty($valores_dias)) {
				$sql_dias = "INSERT INTO sw_dia_semana (id_config_dias_semana, id_horario_def, ds_nombre, ds_orden) VALUES " . implode(',', $valores_dias);
				parent::consulta($sql_dias);
			}

			// ==========================================
			// PROCESO 2: Generar automáticamente las horas de clase con Recreo
			// ==========================================
			$valores_horas = [];

			// Inicializamos el objeto de tiempo con la hora de entrada general (ej: "07:30")
			$tiempo_actual = new DateTime($hora_entrada);

			for ($i = 1; $i <= $nro_horas; $i++) {
				// 1. Guardamos la hora exacta en que inicia este bloque pedagógico
				$hora_inicio = $tiempo_actual->format('H:i:s');

				// 2. Sumamos la duración de la clase para obtener el término de la hora
				$tiempo_actual->modify("+$duracion minutes");
				$hora_fin = $tiempo_actual->format('H:i:s');

				// 3. Nombre descriptivo para la base de datos (ej. "1° Hora")
				$nombre_hora = $i . "a.";

				// 4. Guardamos el registro en el array para el Bulk Insert
				$valores_horas[] = "('$last_insert_id', '$nombre_hora', '$hora_inicio', '$hora_fin', '$i')";

				// ================================================================
				// LOGICA DEL RECREO: Si acabamos de terminar la 3° Hora, 
				// sumamos 15 minutos al tiempo antes de que empiece la 4° Hora.
				// ================================================================
				if ($i == 3) {
					$tiempo_actual->modify("+15 minutes");
				}
			}

			// Insertamos todas las horas pedagógicas en un solo viaje a la base de datos
			if (!empty($valores_horas)) {
				$sql_horas = "INSERT INTO sw_hora_clase (id_horario_def, hc_nombre, hc_hora_inicio, hc_hora_fin, hc_orden) VALUES " . implode(',', $valores_horas);
				parent::consulta($sql_horas);
			}

			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "El horario y sus $nro_horas horas pedagógicas se han generado correctamente.",
				"tipo_mensaje" => "success"
			);
		} else {
			$error_db = isset($this->conexion) ? mysqli_error($this->conexion) : "Error en consulta SQL";
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "No se pudo insertar el horario. Error: " . $error_db,
				"tipo_mensaje" => "error"
			);
		}

		return json_encode($data);
	}

	function actualizarTituloHorario($datos)
	{
		$consulta = parent::consulta("UPDATE sw_horario_def SET ho_titulo = '$datos[ho_titulo]', fecha_inicial = '$datos[fecha_inicial]', fecha_final = '$datos[fecha_final]', status = '$datos[status]' WHERE id_horario_def = '$datos[id_horario_def]'");
		if ($consulta) {
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "El título del horario se ha actualizado exitosamente...",
				"tipo_mensaje" => "success"
			);
		} else {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "El título del horario no se pudo actualizar exitosamente...Error: " . mysqli_error($this->conexion),
				"tipo_mensaje" => "error"
			);
		}
		return json_encode($data);
	}

	function eliminarTituloHorario($id)
	{
		$consulta = parent::consulta("DELETE FROM sw_horario_def WHERE id_horario_def = $id");
		if ($consulta) {
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "El título del horario se ha eliminado exitosamente...",
				"tipo_mensaje" => "success"
			);
		} else {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "El título del horario no se pudo elimianr exitosamente...Error: " . mysqli_error($this->conexion),
				"tipo_mensaje" => "error"
			);
		}
		return json_encode($data);
	}

	function eliminarAsignaturaHoraClase()
	{
		$qry = "DELETE FROM sw_horario WHERE id_horario =" . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura des-asociada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar la Asignatura...Error: " . mysqli_error($this->conexion);
		return $mensaje;
	}
}
