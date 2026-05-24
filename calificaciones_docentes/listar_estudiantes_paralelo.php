<?php
include_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_curso = $_POST["id_curso"];
$id_paralelo = $_POST["id_paralelo"];

session_start();

$id_asignatura = $_POST["id_asignatura"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$id_usuario = $_SESSION["id_usuario"];

// Obtener el id_periodo_lectivo
$sql = "SELECT id_periodo_lectivo FROM sw_paralelo WHERE id_paralelo = $id_paralelo";
$consulta = $db->consulta($sql);
$id_periodo_lectivo = $db->fetch_object($consulta)->id_periodo_lectivo;

// Obtener la nota mínima y máxima
$sql = "SELECT pe_nota_minima, pe_nota_maxima FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$consulta = $db->consulta($sql);
$record = $db->fetch_object($consulta);
$nota_minima = $record->pe_nota_minima;
$nota_maxima = $record->pe_nota_maxima;

// Obtener el tipo de asignatura: 1: Cuantitativa, 2: Cualitativa
$sql = "SELECT id_tipo_asignatura FROM sw_asignatura WHERE id_asignatura = $id_asignatura";
$consulta = $db->consulta($sql);
$id_tipo_asignatura = $db->fetch_object($consulta)->id_tipo_asignatura;

function listarEstudiantesParalelo($num_total_registros, $estudiantes)
{
    global $db, $id_paralelo, $id_asignatura, $id_aporte_evaluacion, $id_tipo_asignatura, $id_periodo_lectivo, $nota_minima, $nota_maxima;

    $cadena = "";
    $num_cols = 0;

    $sql = "SELECT qi.nombre FROM sw_periodo_lectivo pl, sw_quien_inserta_comp qi WHERE qi.id = quien_inserta_comp_id AND pl.id_periodo_lectivo = $id_periodo_lectivo";
    $result = $db->consulta($sql);
    $quien_inserta_comportamiento = $db->fetch_object($result)->nombre;

    $ok = false;
    $titulo = "";
    $mensaje = "";
    $tipo_mensaje = "";

    if ($num_total_registros > 0) {
        $ok = true;
        $contador = 0;
        while ($estudiante = $db->fetch_object($estudiantes)) {
            $contador++;
            $id_estudiante = $estudiante->id_estudiante;
            $apellidos = $estudiante->es_apellidos;
            $nombres = $estudiante->es_nombres;
            $cadena .= "<tr class='fila-datos'>\n";
            $cadena .= "<td>$contador</td>\n";
            $cadena .= "<td>" . $apellidos . " " . $nombres . "</td>\n";

            //Aca vamos a obtener el estado del aporte de evaluacion
            $sql = "SELECT ac.ap_estado
					  FROM sw_aporte_evaluacion a,
						   sw_aporte_paralelo_cierre ac,
						   sw_periodo_lectivo pl,
						   sw_paralelo p
					 WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
					   AND pl.id_periodo_lectivo = p.id_periodo_lectivo
					   AND ac.id_paralelo = p.id_paralelo
					   AND p.id_paralelo = $id_paralelo 
                       AND a.id_aporte_evaluacion = $id_aporte_evaluacion";

            $resultado = $db->consulta($sql);
            $estado_aporte = $db->fetch_object($resultado)->ap_estado;

            //Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
            if ($id_tipo_asignatura == 1) { //CUANTITATIVA
                // Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
                $sql = "SELECT id_rubrica_evaluacion, 
							   id_tipo_aporte, 
							   ap_ponderacion 
						  FROM sw_rubrica_evaluacion r, 
							   sw_aporte_evaluacion a,
							   sw_asignatura asignatura
						 WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion
						   AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
						   AND asignatura.id_asignatura = $id_asignatura
						   AND r.id_aporte_evaluacion = $id_aporte_evaluacion";

                $resultado = $db->consulta($sql);
                $num_registros = $db->num_rows($resultado);

                if ($num_registros > 0) {
                    $suma_rubricas = 0;
                    $contador_rubricas = 0;
                    $j = 0;
                    while ($rubricas = $db->fetch_object($resultado)) {
                        $contador_rubricas++;
                        $id_rubrica_evaluacion = $rubricas->id_rubrica_evaluacion;
                        $tipo_aporte = $rubricas->id_tipo_aporte;

                        // Obtener los valores de las calificaciones
                        $sql = "SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion;

                        $consulta = $db->consulta($sql);

                        $num_registros = $db->num_rows($consulta);
                        $rubrica_estudiante = $db->fetch_assoc($consulta);

                        if ($num_registros > 0) {
                            $calificacion = $rubrica_estudiante["re_calificacion"];
                        } else {
                            $calificacion = 0;
                        }

                        $suma_rubricas += $calificacion;
                        $calificacion_truncada = $calificacion == 0 ? "" : $calificacion;

                        $disabled = '';
                        if ($estado_aporte == 'C') {
                            $disabled = 'disabled';
                        }

                        $cadena .= "<td class='p-0'>
                        <input type='text' id='" . chr(65 + $j) . $contador . "' 
                        class='excel-input formativa' 
                        value='$calificacion_truncada' 
                        onblur='editarCalificacion(this, $calificacion, $id_estudiante, $id_paralelo, $id_asignatura, $id_rubrica_evaluacion)'  
                        $disabled>
                        </td>\n";
                        $j++;
                    }

                    $promedio = $suma_rubricas / $contador_rubricas;
                    $promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);

                    $cadena .= "<td class='resultado'><input type='text' class='excel-input' value='$promedio' disabled></td>\n";

                    if (strtolower($quien_inserta_comportamiento) == 'docente') {
                        // Aqui va el codigo para obtener el comportamiento
                        $sql = "SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_aporte_evaluacion = " . $id_aporte_evaluacion;

                        $consulta = $db->consulta($sql);
                        $num_registros = $db->num_rows($consulta);

                        $comportamiento = $db->fetch_assoc($consulta);
                        if ($num_registros > 0) {
                            $calificacion = $comportamiento["co_cualitativa"];
                        } else {
                            $calificacion = '';
                        }

                        $cadena .= "<td class='p-0'>
                        <input type='text' id='" . chr(65 + $j) . $contador . "' 
                        value='$calificacion' 
                        class='excel-input' 
                        onblur='editarComportamiento(this, $id_estudiante, $id_paralelo, $id_asignatura, $id_aporte_evaluacion)' 
                        $disabled>
                        </td>\n";
                    }
                } else {
                    // Aqui va el codigo para obtener la calificacion cualitativa
                    $qry = $db->consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
                    $num_registros = $db->num_rows($qry);
                    $cualitativa = $db->fetch_object($qry);
                    if ($num_registros > 0) {
                        $calificacion = $cualitativa->rc_calificacion;
                    } else {
                        $calificacion = " ";
                    }
                    $cadena .= "<td class='p-0'>
                        <input type='text' id='" . chr(65 + $j) . $contador . "' 
                        value='$calificacion' 
                        class='excel-input' 
                        $disabled>
                        </td>\n";
                }
            }

            $cadena .= "</tr>\n";
        }
        $cadena .= "</table>";
        $num_cols = $j + 1;
    } else {
        $titulo = "Error";
        $mensaje = "No se han matriculado estudiantes en este paralelo...";
        $tipo_mensaje = "info";
    }

    $data = array(
        "ok" => $ok,
        "body" => $cadena,
        "num_filas" => $num_total_registros,
        "num_columnas" => $num_cols,
        "titulo" => $titulo,
        "mensaje" => $mensaje,
        "tipo_mensaje" => $tipo_mensaje,
        "nota_minima" => $nota_minima,
        "nota_maxima" => $nota_maxima
    );

    return json_encode($data);
}


// Obtener el tipo de aporte de evaluación
$sql = "SELECT ta_descripcion FROM sw_tipo_aporte ta, sw_aporte_evaluacion ap WHERE ta.id_tipo_aporte = ap.id_tipo_aporte AND id_aporte_evaluacion = $id_aporte_evaluacion";
$consulta = $db->consulta($sql);
$tipo_aporte = $db->fetch_object($consulta)->ta_descripcion;

$sql = "SELECT e.id_estudiante, 
			   c.id_curso, 
			   d.id_paralelo, 
			   d.id_asignatura, 
			   e.es_apellidos, 
			   e.es_nombres, 
			   es_retirado, 
			   as_nombre, 
			   cu_nombre, 
			   pa_nombre,
			   id_tipo_asignatura, 
			   dg_abreviatura 
		  FROM sw_distributivo d, 
			   sw_estudiante_periodo_lectivo ep, 
			   sw_estudiante e,
			   sw_def_genero dg,  
			   sw_asignatura a, 
			   sw_curso c, 
			   sw_paralelo p 
		 WHERE d.id_paralelo = ep.id_paralelo 
		   AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
		   AND e.id_estudiante = ep.id_estudiante 
		   AND dg.id_def_genero = e.id_def_genero 
		   AND d.id_asignatura = a.id_asignatura 
		   AND d.id_paralelo = p.id_paralelo 
		   AND p.id_curso = c.id_curso 
		   AND d.id_paralelo = $id_paralelo
		   AND d.id_asignatura = $id_asignatura
		   AND es_retirado <> 'S' 
		   AND activo = 1 
	  ORDER BY es_apellidos, es_nombres ASC";

try {
    $estudiantes = $db->consulta($sql);
    $num_total_registros = $db->num_rows($estudiantes);

    if ($tipo_aporte == 'PARCIAL')    // Parciales
        echo listarEstudiantesParalelo($num_total_registros, $estudiantes);
    else if ($tipo_aporte == 'EXAMEN_SUB_PERIODO')   // Examen Sub Periodo
        //echo listarCalificacionesParalelo($num_total_registros, $estudiantes);
        echo "EXAMEN_SUB_PERIODO";
    else if ($tipo_aporte == 'FASE_PRI') // Proyecto Integrador o Interdisciplinario
        //echo listarCalificacionesFaseProyecto($num_total_registros, $estudiantes);
        echo "FASE_PRI";
    else if ($tipo_aporte == 'SUPLETORIO') // Examen Supletorio
        //echo listarCalificacionesSupletorio($num_total_registros, $estudiantes);
        echo "SUPLETORIO";
} catch (Exception $e) {
    echo $e->getMessage();
}
