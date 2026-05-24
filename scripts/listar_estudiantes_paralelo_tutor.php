<?php
include("clases/class.mysql.php");
$db = new mysql();

$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

$cadena = "";

$consulta = $db->consulta("SELECT ru_nombre FROM sw_rubrica_evaluacion WHERE id_tipo_asignatura = 1 AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
	//Cabecera de la tabla
	$cadena .= "<table class=\"table table-striped table-hover fuente9\">\n";
	$cadena .= "<thead>\n";
	$cadena .= "<tr>\n";
	$cadena .= "<th>Nro.</th>\n";
	$cadena .= "<th>Id</th>\n";
	$cadena .= "<th>N&oacute;mina</th>\n";
	while ($titulo_rubrica = $db->fetch_assoc($consulta)) {
		$cadena .= "<th>" . $titulo_rubrica["ru_nombre"] . "</th>\n";
	}
	$cadena .= "<th>PROMEDIO</th>\n";
	$cadena .= "</tr>\n";
	$cadena .= "</thead>\n";
	//Cuerpo de la tabla
	$cadena .= "<tbody>\n";
	$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if ($num_total_registros > 0) {
		$contador = 0;
		while ($estudiante = $db->fetch_assoc($consulta)) {
			$contador++;
			$id_estudiante = $estudiante["id_estudiante"];
			$apellidos = $estudiante["es_apellidos"];
			$nombres = $estudiante["es_nombres"];
			$cadena .= "<tr>\n";
			$cadena .= "<td>$contador</td>\n";
			$cadena .= "<td>$id_estudiante</td>\n";
			$cadena .= "<td>" . $apellidos . " " . $nombres . "</td>\n";
			// Aqui se consultan las rubricas de las asignaturas cuantitativas definidas para el aporte de evaluacion elegido
			$query = "SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion";
			$rubrica_evaluacion = $db->consulta($query);
			$num_total_registros = $db->num_rows($rubrica_evaluacion);
			if ($num_total_registros > 0) {
				//Aqui van las calificaciones cuantitativas
				$suma_rubricas = 0;
				$contador_rubricas = 0;
				while ($rubrica = $db->fetch_assoc($rubrica_evaluacion)) {
					$contador_rubricas++;
					$id_rubrica_evaluacion = $rubrica["id_rubrica_evaluacion"];
					$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
					$num_total_registros = $db->num_rows($qry);
					if ($num_total_registros > 0) {
						$rubrica_estudiante = $db->fetch_assoc($qry);
						$calificacion = $rubrica_estudiante["re_calificacion"];
					} else {
						$calificacion = 0;
					}
					$suma_rubricas += $calificacion;
					$calificacion_insumo = $calificacion == 0 ? "" : substr($calificacion, 0, strpos($calificacion, '.') + 3);
					$cadena .= "<td>" . $calificacion_insumo . "</td>\n";
				}
			}
			$promedio = $suma_rubricas / $contador_rubricas;
			$nota_promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
			$cadena .= "<td>" . $nota_promedio . "</td>\n";
			$cadena .= "</tr>\n";
		}
	} else {
		$cadena .= "<tr>\n";
		$cadena .= "<td class=\"text-center\">No se han matriculado estudiantes en este paralelo...</td>\n";
		$cadena .= "</tr>\n";
	}
	$cadena .= "</tbody>\n";
	$cadena .= "</table>\n";
} else {
	$cadena .= "<p class='mensaje'>\n";
	$cadena .= "No se han definido los insumos de evaluaci&oacute;n correspondientes...\n";
	$cadena .= "</p>\n";
}

echo $cadena;
