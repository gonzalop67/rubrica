<?php
class Perfil
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerPerfiles()
    {
        $this->db->query("SELECT * FROM sw_perfil ORDER BY pe_nombre ASC");
        return $this->db->registros();
    }

    public function obtenerPerfilesUsuario($id_usuario)
    {
        $this->db->query("SELECT up.*, pe_nombre FROM sw_usuario_perfil up, sw_perfil p WHERE p.id_perfil = up.id_perfil AND id_usuario = $id_usuario ORDER BY pe_nombre");
        return $this->db->registros();
    }

    public function obtenerPerfilesUsuarios()
    {
        $this->db->query("SELECT u.id_usuario, p.pe_nombre FROM sw_usuario u, sw_perfil p, sw_usuario_perfil up WHERE u.id_usuario = up.id_usuario AND p.id_perfil = up.id_perfil ORDER BY p.pe_nombre ASC");
        return $this->db->registros();
    }

    public function obtenerNombrePerfil($id_perfil)
    {
        $this->db->query("SELECT pe_nombre FROM sw_perfil WHERE id_perfil = $id_perfil");
        return $this->db->registro()->pe_nombre;
    }

    public function existePerfil($pe_nombre)
    {
        $query = "SELECT id_perfil "
                . " FROM `sw_perfil` "
                . "WHERE pe_nombre = '" . $pe_nombre ."'";

        $this->db->query($query);
        $perfil = $this->db->registro();

        return !empty($perfil);
    }

    public function insertarPerfil($datos)
    {
        $this->db->query('INSERT INTO sw_perfil (pe_nombre) VALUES (:pe_nombre)');

        //Vincular valores
        $this->db->bind(':pe_nombre', $datos['pe_nombre']);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Agregado con éxito!",
                'mensaje' => "Inserción realizada exitosamente.",
                'estado' => 'success'
            ];
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
        }

        //Enviar el objeto en formato json
        return json_encode($datos);
    }

    public function obtenerPerfilPorId($id)
    {
        $this->db->query("SELECT * FROM sw_perfil WHERE id_perfil = $id");
        return $this->db->registro();
    }

    public function actualizarPerfil($datos)
    {
        $this->db->query('UPDATE sw_perfil SET pe_nombre = :pe_nombre WHERE id_perfil = :id_perfil');

        //Vincular valores
        $this->db->bind(':pe_nombre', $datos['pe_nombre']);
        $this->db->bind(':id_perfil', $datos['id_perfil']);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Actualizado con éxito!",
                'mensaje' => "Actualización realizada exitosamente.",
                'estado' => 'success'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        }
    }

    public function eliminarPerfil($id)
    {
		$this->db->query('DELETE FROM `sw_perfil` WHERE `id_perfil` = :id_perfil');
        
        //Vincular valores
        $this->db->bind(':id_perfil', $id);

		return $this->db->execute();
	}

    public function paginacion($paginaActual)
    {
        $this->db->query("SELECT * FROM `sw_perfil`");
        $consulta = $this->db->registros();
        $nroRegistros = $this->db->rowCount();

        $nroLotes = 5;
        $nroPaginas = ceil($nroRegistros / $nroLotes);

        $lista = '';
        $tabla = '';

        if ($nroRegistros > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ');">‹</a></li>';
            }

            for ($i = 1; $i <= $nroPaginas; $i++) {
                if ($i == $paginaActual) {
                    $lista = $lista . '<li class="active"><a href="javascript:pagination(' . $i . ');">' . $i . '</a></li>';
                } else {
                    $lista = $lista . '<li><a href="javascript:pagination(' . $i . ');">' . $i . '</a></li>';
                }
            }

            if ($paginaActual == $nroPaginas) {
                $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ');">›</a></li>';
            }

            if ($paginaActual <= 1) {
                $limit = 0;
            } else {
                $limit = $nroLotes * ($paginaActual - 1);
            }

            $this->db->query("SELECT * FROM `sw_perfil` ORDER BY pe_nombre LIMIT $limit, $nroLotes");
            $consulta = $this->db->registros();
            $num_total_registros = $this->db->rowCount();

            if ($num_total_registros > 0) {
                $contador = $limit;
                foreach ($consulta as $v) {
                    $contador++;
                    $tabla .= "<tr>\n";
                    $id = $v->id_perfil;
                    $nombre = $v->pe_nombre;
                    $tabla .= "<td>" . $id . "</td>";
                    $tabla .= "<td>" . $nombre . "</td>";
                    $tabla .= "<td>\n";
                    $tabla .= "<div class=\"btn-group\">\n";
                    $tabla .= "<a href=\"javascript:;\" class=\"btn btn-warning item-edit\" onclick=\"obtenerDatos(" . $v->id_perfil . ")\" data-toggle=\"modal\" data-target=\"#editarPerfilModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                    $tabla .= "<a href=\"javascript:;\" class=\"btn btn-danger\" onclick=\"eliminarPerfil(".$v->id_perfil.")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
                    $tabla .= "</div>\n";
                    $tabla .= "</td>\n";
                    $tabla .= "</tr>\n";
                }
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<tr><td colspan='3' align='center'>No se han ingresado perfiles todavia...</td></tr>\n";
            $tabla .= "</tr>\n";
        }

        $array = array(
            0 => $tabla,
            1 => $lista
        );

        return json_encode($array);
    }
}
