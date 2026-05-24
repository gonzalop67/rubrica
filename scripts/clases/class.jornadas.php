<?php

class jornadas extends MySQL
{
    function cargar_jornadas()
    {
        $consulta = parent::consulta("SELECT * FROM sw_jornada ORDER BY jo_orden ASC");
        $num_total_registros = parent::num_rows($consulta);
        $cadena = "";
        if ($num_total_registros > 0) {
            $contador = 0;
            while ($jornada = parent::fetch_assoc($consulta)) {
                $contador++;
                $id = $jornada["id_jornada"];
                $name = $jornada["jo_nombre"];
                $orden = $jornada["jo_orden"];
                $cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
                $cadena .= "<td>$contador</td>\n";
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>$name</td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class='btn-group'>\n";
                $cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarJornadaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
                $cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarJornada(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
                $cadena .= "</div>";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='100%' align='center'>No se han definido Jornadas...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }
}