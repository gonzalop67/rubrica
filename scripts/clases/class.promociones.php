<?php

class promociones extends MySQL
{

    var $code = "";
    var $id_periodo_lectivo = "";
    var $id_paralelo_actual = "";
    var $id_paralelo_anterior = "";

    public function asociarParalelosPromocion()
    {
        $consulta = parent::consulta("SELECT * FROM sw_promocion_automatica WHERE id_paralelo_actual = " . $this->id_paralelo_actual . " AND id_paralelo_anterior = " . $this->id_paralelo_anterior);
        $num_total_registros = parent::num_rows($consulta);
        if ($num_total_registros > 0) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "El Paralelo Actual ya está asociado con el Paralelo Anterior...",
                "tipo_mensaje" => "error"
            );
            return json_encode($data);
        } else {
            try {
                $qry = "INSERT INTO sw_promocion_automatica (id_periodo_lectivo, id_paralelo_actual, id_paralelo_anterior) VALUES (";
                $qry .= $this->id_periodo_lectivo . ", ";
                $qry .= $this->id_paralelo_actual . ", ";
                $qry .= $this->id_paralelo_anterior . ")";

                $consulta = parent::consulta($qry);

                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Paralelo Actual y Paralelo Anterior se ha insertado exitosamente...",
                    "tipo_mensaje" => "success"
                );
                return json_encode($data);
            } catch (\Exception $e) {
                $data = array(
                    "titulo"       => "Ocurrió un error inesperado.",
                    "mensaje"      => "La Asociación Paralelo Actual y Paralelo Anterior no se pudo insertar exitosamente...Error: " . $e->getMessage(),
                    "tipo_mensaje" => "error"
                );
                return json_encode($data);
            }
        }
    }

    public function eliminarAsociacion()
    {
        try {
            $qry = "DELETE FROM sw_promocion_automatica WHERE id_promocion_automatica = $this->code";
            $consulta = parent::consulta($qry);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Paralelo Actual y Paralelo Anterior se ha eliminado exitosamente...",
                "tipo_mensaje" => "success"
            );
            return json_encode($data);
        } catch (\Exception $e) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "La Asociación Paralelo Actual y Paralelo Anterior no se pudo eliminar exitosamente...Error: " . $e->getMessage(),
                "tipo_mensaje" => "error"
            );
            return json_encode($data);
        }
    }

    public function cargarParalelosPromocion()
    {
        $consulta = parent::consulta("
            SELECT pa.*
              FROM sw_promocion_automatica pa,
                   sw_paralelo pac
             WHERE pac.id_paralelo = pa.id_paralelo_actual
               AND pa.id_periodo_lectivo = $this->id_periodo_lectivo 
             ORDER BY pac.pa_orden ASC");
        $num_total_registros = parent::num_rows($consulta);
        $tabla = "";
        if ($num_total_registros > 0) {
            while ($paralelo_promocion = parent::fetch_assoc($consulta)) {
                $tabla .= "<tr>";
                $id_paralelo_actual = $paralelo_promocion["id_paralelo_actual"];
                $query = parent::consulta("
                    SELECT pa_nombre,
                           cu_nombre,
                           es_abreviatura,
                           jo_nombre
                      FROM sw_paralelo pa,
                           sw_curso cu,
                           sw_especialidad es,
                           sw_jornada jo
                     WHERE cu.id_curso = pa.id_curso
                       AND es.id_especialidad = cu.id_especialidad
                       AND jo.id_jornada = pa.id_jornada
                       AND pa.id_paralelo = $id_paralelo_actual
                ");
                $registro_actual = parent::fetch_assoc($query);
                $paralelo_actual = $registro_actual["cu_nombre"] . " " . $registro_actual["pa_nombre"] . " [" . $registro_actual["es_abreviatura"] . "] - " . $registro_actual["jo_nombre"];
                $id_paralelo_anterior = $paralelo_promocion["id_paralelo_anterior"];
                $query = parent::consulta("
                    SELECT pa_nombre,
                           cu_nombre,
                           es_abreviatura,
                           jo_nombre
                      FROM sw_paralelo pa,
                           sw_curso cu,
                           sw_especialidad es,
                           sw_jornada jo
                     WHERE cu.id_curso = pa.id_curso
                       AND es.id_especialidad = cu.id_especialidad
                       AND jo.id_jornada = pa.id_jornada
                       AND pa.id_paralelo = $id_paralelo_anterior
                ");
                $registro_anterior = parent::fetch_assoc($query);
                $paralelo_anterior = $registro_anterior["cu_nombre"] . " " . $registro_anterior["pa_nombre"] . " [" . $registro_anterior["es_abreviatura"] . "] - " . $registro_anterior["jo_nombre"];
                $id = $paralelo_promocion["id_promocion_automatica"];
                $tabla .= "<td>" . $id . "</td>";
                $tabla .= "<td>" . $paralelo_actual . "</td>";
                $tabla .= "<td>" . $paralelo_anterior . "</td>";
                $tabla .= "<td><div class='btn-group'>";
                $tabla .= "<a href='javascript:;' class='btn btn-danger item-delete' data='" . $id . "' title='Eliminar'><span class='fa fa-remove'></span></a></div></td>";
                $tabla .= "</tr>";
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<tr><td colspan='3' align='center'>No se han asociado paralelos a promocionar todavia...</td></tr>\n";
            $tabla .= "</tr>\n";
        }
        return $tabla;
    }

    public function cargarParalelosAPromocionar()
    {
        $consulta = parent::consulta("
            SELECT pa.*
              FROM sw_promocion_automatica pa,
                   sw_paralelo pac
             WHERE pac.id_paralelo = pa.id_paralelo_actual
               AND pa.id_periodo_lectivo = $this->id_periodo_lectivo 
             ORDER BY pac.pa_orden ASC");
        $num_total_registros = parent::num_rows($consulta);
        $tabla = "";
        if ($num_total_registros > 0) {
            while ($paralelo_promocion = parent::fetch_assoc($consulta)) {
                $tabla .= "<tr>";
                $id_paralelo_actual = $paralelo_promocion["id_paralelo_actual"];
                $query = parent::consulta("
                    SELECT pa_nombre,
                           cu_nombre,
                           es_abreviatura,
                           jo_nombre
                      FROM sw_paralelo pa,
                           sw_curso cu,
                           sw_especialidad es,
                           sw_jornada jo
                     WHERE cu.id_curso = pa.id_curso
                       AND es.id_especialidad = cu.id_especialidad
                       AND jo.id_jornada = pa.id_jornada
                       AND pa.id_paralelo = $id_paralelo_actual
                ");
                $registro_actual = parent::fetch_assoc($query);
                $paralelo_actual = $registro_actual["cu_nombre"] . " " . $registro_actual["pa_nombre"] . " [" . $registro_actual["es_abreviatura"] . "] - " . $registro_actual["jo_nombre"];
                $id_paralelo_anterior = $paralelo_promocion["id_paralelo_anterior"];
                $query = parent::consulta("
                    SELECT pa_nombre,
                           cu_nombre,
                           es_abreviatura,
                           jo_nombre
                      FROM sw_paralelo pa,
                           sw_curso cu,
                           sw_especialidad es,
                           sw_jornada jo
                     WHERE cu.id_curso = pa.id_curso
                       AND es.id_especialidad = cu.id_especialidad
                       AND jo.id_jornada = pa.id_jornada
                       AND pa.id_paralelo = $id_paralelo_anterior
                ");
                $registro_anterior = parent::fetch_assoc($query);
                $paralelo_anterior = $registro_anterior["cu_nombre"] . " " . $registro_anterior["pa_nombre"] . " [" . $registro_anterior["es_abreviatura"] . "] - " . $registro_anterior["jo_nombre"];
                $id = $paralelo_promocion["id_promocion_automatica"];
                $tabla .= "<td>" . $id . "</td>";
                $tabla .= "<td>" . $paralelo_actual . "</td>";
                $tabla .= "<td>" . $paralelo_anterior . "</td>";
                $estado = $paralelo_promocion["pro_estado"] == 0 ? "primary" : "success";
                $tabla .= "<td><div class='btn-group'>";
                $tabla .= "<a href='javascript:;' class='btn btn-$estado item-procesar' data='" . $id . "' title='Promocionar'><span class='fa fa-check'></span></a></div></td>";
                $tabla .= "</tr>";
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<tr><td colspan='3' align='center'>No se han asociado paralelos a promocionar todavia...</td></tr>\n";
            $tabla .= "</tr>\n";
        }
        return $tabla;
    }

    public function PromocionarParalelo()
    {
        $query = parent::consulta("
            SELECT id_paralelo_actual,
                   id_paralelo_anterior
              FROM sw_promocion_automatica
             WHERE id_promocion_automatica = $this->code
        ");
        $registro_actual = parent::fetch_assoc($query);
        $id_paralelo_actual = $registro_actual["id_paralelo_actual"];
        $id_paralelo_anterior = $registro_actual["id_paralelo_anterior"];
        // Obtengo el id_modalidad del Periodo Lectivo actual
        $query = parent::consulta("SELECT id_modalidad 
                                     FROM sw_periodo_lectivo 
                                    WHERE id_periodo_lectivo = $this->id_periodo_lectivo");
        $registro = parent::fetch_object($query);
        $id_modalidad = $registro->id_modalidad;
        // Obtengo el id_periodo_lectivo del Periodo Lectivo anterior
        $query = parent::consulta("SELECT MAX(id_periodo_lectivo) AS max_id 
									 FROM sw_periodo_lectivo 
									WHERE id_modalidad = $id_modalidad");
		$registro = parent::fetch_object($query);
		$max_id_periodo_lectivo = $registro->max_id;
		$query = parent::consulta("SELECT MAX(id_periodo_lectivo) AS id_periodo_lectivo
									 FROM sw_periodo_lectivo
									WHERE id_modalidad = $id_modalidad
									  AND id_periodo_lectivo < $max_id_periodo_lectivo");
		$num_total_registros = parent::num_rows($query);
		if ($num_total_registros > 0) {
			$periodo_lectivo = parent::fetch_object($query);
			$id_periodo_lectivo_anterior = $periodo_lectivo->id_periodo_lectivo;
        }
        try {
            $qry = "call promocionar_paralelo (";
            $qry .= $id_paralelo_actual . ",";
            $qry .= $id_paralelo_anterior . ",";
            $qry .= $this->id_periodo_lectivo . ",";
            $qry .= $id_periodo_lectivo_anterior . ")";
            $consulta = parent::consulta($qry);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Promoción Automática del Paralelo se realizó exitosamente...",
                "tipo_mensaje" => "success"
            );

            return json_encode($data);
        } catch (\Exception $e) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "La Promoción Automática del Paralelo no se pudo realizar exitosamente...Error: " . $e->getMessage(),
                "tipo_mensaje" => "error"
            );

            return json_encode($data);
        }
    }
}
