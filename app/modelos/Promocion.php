<?php
class Promocion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarEstudiantes($id_paralelo, $id_periodo_lectivo)
    {
        $this->db->query("SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado, es_cedula FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = " . $id_periodo_lectivo . " AND id_paralelo = " . $id_paralelo . " AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
        $resultado = $this->db->registros();
        $num_total_registros = $this->db->rowCount();

        $cadena = "<table class=\" table fuente8\">\n";
        $cadena .= "<thead>\n";
        $cadena .= "<tr>\n";
        $cadena .= "<th>Nro.</th>\n";
        $cadena .= "<th>Id.</th>\n";
        $cadena .= "<th>Apellidos</th>\n";
        $cadena .= "<th>Nombres</th>\n";
        $cadena .= "<th>Promedio Anual</th>\n";
        $cadena .= "<th>Observación</th>\n";
        $cadena .= "<th class=\"text-center\">Certificado</th>\n";
        // $cadena .= "<th class=\"text-center\"></th>\n";
        $cadena .= "</tr>\n";
        $cadena .= "</thead>\n";
        $cadena .= "<tbody>\n";

        if ($num_total_registros > 0) {
            $contador = 0;
            foreach ($resultado as $estudiante) {
                $contador++;
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $codigo = $estudiante->id_estudiante;
                $apellidos = $estudiante->es_apellidos;
                $nombres = $estudiante->es_nombres;
                $cadena .= "<td width=\"5%\">$contador</td>\n";
                $cadena .= "<td width=\"5%\" align=\"left\">$codigo</td>\n";
                $cadena .= "<td width=\"24%\" align=\"left\">$apellidos</td>\n";
                $cadena .= "<td width=\"24%\" align=\"left\">$nombres</td>\n";
                //Aqui calculo el promedio general final del estudiante
                $this->db->query("SELECT calcular_promedio_general(" . $id_periodo_lectivo . "," . $codigo . "," . $id_paralelo . ") AS promedio_general");
                $record = $this->db->registro();
				$promedio = $record->promedio_general;
                //Aqui determino si aprueba o no el periodo lectivo
                $this->db->query("SELECT es_promocionado($codigo, $id_periodo_lectivo, $id_paralelo) AS promocionado");
                $record = $this->db->registro();
				$promocionado = $record->promocionado;
				$aprueba = $promocionado ? 'APRUEBA' : 'NO APRUEBA';
				$cadena .= "<td width=\"16%\" align=\"left\">$promedio</td>\n";
				$cadena .= "<td width=\"8%\" align=\"left\">$aprueba</td>\n";
				$cadena .= "<td width=\"9%\"><a href=\"#\"class=\"btn btn-success\" onclick=\"seleccionarEstudiante(".$codigo.")\">A PDF</a></td>\n";
				/* $cadena .= "<td width=\"9%\"><a href=\"#\"class=\"btn btn-info\" onclick=\"seleccionarAExcel(".$codigo.")\">A EXCEL</a></td>\n"; */
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td>No se han matriculado estudiantes para este paralelo...</td>\n";
            $cadena .= "</tr>\n";
        }

        $cadena .= "</tbody>\n";
        $cadena .= "</table>";

        return $cadena;
    }
}
