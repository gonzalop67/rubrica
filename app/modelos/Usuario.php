<?php
class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function buscarUsuarios($pattern)
    {
        $this->db->query("SELECT id_usuario, 
                                 us_login, 
                                 us_shortname,  
                                 us_activo, 
                                 us_foto 
                            FROM sw_usuario 
                           WHERE us_apellidos LIKE '%$pattern%' OR us_nombres LIKE '%$pattern%' 
                           ORDER BY us_apellidos, us_nombres ASC");
        return $this->db->registros();
    }

    public function buscarUsuario($patron)
    {
        // Obtener usuarios que coincidan con el patrón de búsqueda
        $this->db->query("SELECT id_usuario, 
                                 us_login, 
                                 us_fullname,  
                                 us_activo, 
                                 us_foto 
                            FROM sw_usuario 
                           WHERE us_apellidos LIKE '%$patron%' OR us_nombres LIKE '%$patron%' 
                           ORDER BY us_apellidos, us_nombres ASC");

        $usuarios = $this->db->registros();

        // Obtener los perfiles asociados con los usuarios
        $this->db->query("SELECT u.id_usuario, p.pe_nombre FROM sw_usuario u, sw_perfil p, sw_usuario_perfil up WHERE u.id_usuario = up.id_usuario AND p.id_perfil = up.id_perfil ORDER BY p.pe_nombre ASC");
        $this->db->registros();

        $perfilesUsuarios = $this->db->registros();

        $error = (count($usuarios) == 0) ? 1 : 0;

        $array = array(
            0 => json_encode($usuarios),
            1 => json_encode($perfilesUsuarios),
            2 => $error
        );

        return json_encode($array);
    }

    public function obtenerUsuarioPorId($id)
    {
        $this->db->query("SELECT * FROM sw_usuario WHERE id_usuario = $id");
        return $this->db->registro();
    }

    public function obtenerUsuarios()
    {
        $this->db->query("SELECT * FROM sw_usuario ORDER BY us_apellidos, us_nombres ASC");
        return $this->db->registros();
    }

    public function obtenerDocentes()
    {
        $this->db->query("SELECT * 
                            FROM sw_usuario u,
                                 sw_perfil p,
                                 sw_usuario_perfil up 
                           WHERE u.id_usuario = up.id_usuario 
                             AND p.id_perfil = up.id_perfil
                             AND pe_nombre = 'DOCENTE'
                             AND us_activo = 1
                        ORDER BY us_apellidos, us_nombres");
        return $this->db->registros();
    }

    public function obtenerTutores()
    {
        $this->db->query("SELECT * 
                            FROM sw_usuario u,
                                 sw_perfil p,
                                 sw_usuario_perfil up 
                           WHERE u.id_usuario = up.id_usuario 
                             AND p.id_perfil = up.id_perfil
                             AND pe_nombre = 'TUTOR'
                             AND us_activo = 1
                        ORDER BY us_apellidos, us_nombres");
        return $this->db->registros();
    }

    public function obtenerInspectores()
    {
        $this->db->query("SELECT * 
                            FROM sw_usuario u,
                                 sw_perfil p,
                                 sw_usuario_perfil up 
                           WHERE u.id_usuario = up.id_usuario 
                             AND p.id_perfil = up.id_perfil
                             AND (pe_nombre = 'INSPECCION'
                                 OR pe_nombre = 'INSPECCIÓN')
                             AND us_activo = 1
                        ORDER BY us_apellidos, us_nombres");
        return $this->db->registros();
    }

    public function contarAutoridades()
    {
        $this->db->query("SELECT COUNT(*) AS nro_autoridades 
                            FROM sw_usuario u,
                                 sw_perfil p, 
                                 sw_usuario_perfil up 
                           WHERE u.id_usuario = up.id_usuario 
                             AND p.id_perfil = up.id_perfil 
                             AND pe_nombre = 'AUTORIDAD' 
                             AND us_activo = 1");
        return $this->db->registro()->nro_autoridades;
    }

    public function contarDocentes()
    {
        $this->db->query("SELECT COUNT(*) AS nro_docentes 
                            FROM sw_usuario u,
                                 sw_perfil p, 
                                 sw_usuario_perfil up 
                           WHERE u.id_usuario = up.id_usuario 
                             AND p.id_perfil = up.id_perfil 
                             AND pe_nombre = 'DOCENTE' 
                             AND us_activo = 1");
        return $this->db->registro()->nro_docentes;
    }

    public function contarRepresentantes($id_periodo_lectivo)
    {
        $this->db->query("SELECT COUNT(*) AS nro_representantes
                            FROM sw_representante r,
                                 sw_estudiante_periodo_lectivo ep
                           WHERE r.id_estudiante = ep.id_estudiante
                             AND id_periodo_lectivo = $id_periodo_lectivo");
        return $this->db->registro()->nro_representantes;
    }

    public function obtenerDocentesPorParalelo($id_curso, $id_paralelo)
    {
        $this->db->query("
            SELECT us_titulo, 
                   us_apellidos, 
                   us_nombres, 
                   as_nombre 
              FROM sw_distributivo di,
                   sw_asignatura_curso ac, 
                   sw_usuario u, 
                   sw_asignatura a 
             WHERE u.id_usuario = di.id_usuario 
               AND a.id_asignatura = di.id_asignatura
               AND ac.id_asignatura = di.id_asignatura
               AND ac.id_curso = $id_curso 
               AND id_paralelo = $id_paralelo
             ORDER BY ac_orden
        ");
        $registros = $this->db->registros();
        $num_rows = $this->db->rowCount();
        $cadena = "";
        if ($num_rows > 0) {
            $contador = 1;
            foreach ($registros as $row) {
                $asignatura = $row->as_nombre;
                $profesor = $row->us_titulo . " " . $row->us_apellidos . " " . $row->us_nombres;
                $cadena .= "<tr>\n";
                $cadena .= "<td>$contador</td>\n";
                $cadena .= "<td>$asignatura</td>\n";
                $cadena .= "<td>$profesor</td>\n";
                $cadena .= "</tr>\n";
                $contador++;
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='3' align='center'>No se han asociado Docentes a este Paralelo...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function obtenerUsuario($login, $clave, $id_perfil)
    {
        $query = "SELECT u.id_usuario, institucion_id, pe_nombre "
            . " FROM sw_usuario u, "
            . "      sw_perfil p, "
            . "      sw_usuario_perfil up "
            . "WHERE u.id_usuario = up.id_usuario "
            . "  AND p.id_perfil = up.id_perfil "
            . "  AND us_login = '$login' "
            . "  AND us_password = '$clave' "
            . "  AND p.id_perfil = $id_perfil"
            . "  AND us_activo = 1";

        $this->db->query($query);
        return $this->db->registro();
    }

    public function obtenerNombreUsuario($id_usuario)
    {
        //Obtengo los nombres del usuario
        $queryString = "SELECT SUBSTRING_INDEX(us_apellidos, ' ', 1) AS primer_apellido, 
                               SUBSTRING_INDEX(us_nombres, ' ', 1) AS primer_nombre
                          FROM sw_usuario 
                         WHERE id_usuario = $id_usuario";
        $this->db->query($queryString);
        $usuario = $this->db->registro();

        return $usuario->primer_nombre . " " . $usuario->primer_apellido;
    }

    public function obtenerFotoUsuario($id_usuario)
    {
        //Obtener el nombre del archivo de imagen del usuario
        $queryString = "SELECT us_foto FROM sw_usuario WHERE id_usuario = $id_usuario";
        $this->db->query($queryString);
        $usuario = $this->db->registro();

        return $usuario->us_foto;
    }

    public function existeUsuarioPorNombreCompleto($us_fullname)
    {
        $this->db->query("SELECT id_usuario FROM sw_usuario WHERE us_fullname = '$us_fullname'");
        $usuario = $this->db->registro();

        return !empty($usuario);
    }

    public function existeUsuarioPorNombreUsuario($us_login)
    {
        $this->db->query("SELECT id_usuario FROM sw_usuario WHERE us_login = '$us_login'");
        $usuario = $this->db->registro();

        return !empty($usuario);
    }

    public function insertarUsuario($datos)
    {
        $this->db->query('INSERT INTO sw_usuario (us_titulo, us_apellidos, us_nombres, us_shortname, us_fullname, us_login, us_password, us_foto, us_genero, us_activo) VALUES (:us_titulo, :us_apellidos, :us_nombres, :us_shortname, :us_fullname, :us_login, :us_password, :us_foto, :us_genero, :us_activo)');

        //Vincular valores
        $this->db->bind(':us_titulo', $datos['us_titulo']);
        $this->db->bind(':us_apellidos', $datos['us_apellidos']);
        $this->db->bind(':us_nombres', $datos['us_nombres']);
        $this->db->bind(':us_shortname', $datos['us_shortname']);
        $this->db->bind(':us_fullname', $datos['us_fullname']);
        $this->db->bind(':us_login', $datos['us_login']);
        $this->db->bind(':us_password', $datos['us_password']);
        $this->db->bind(':us_foto', $datos['us_foto']);
        $this->db->bind(':us_genero', $datos['us_genero']);
        $this->db->bind(':us_activo', $datos['us_activo']);

        $this->db->execute();

        $this->db->query("SELECT MAX(id_usuario) AS lastInsertId FROM sw_usuario");
        $lastInsertId = $this->db->registro()->lastInsertId;

        for ($i = 0; $i < count($datos['id_perfil']); $i++) {
            //Insertar en la tabla sw_usuario_perfil
            $this->db->query("INSERT INTO sw_usuario_perfil (id_usuario, id_perfil) VALUES (:id_usuario, :id_perfil)");

            //Vincular valores
            $this->db->bind(':id_usuario', $lastInsertId);
            $this->db->bind(':id_perfil', $datos['id_perfil'][$i]);

            $this->db->execute();
        }
    }

    public function actualizarUsuario($datos)
    {
        $this->db->query('UPDATE sw_usuario SET us_titulo = :us_titulo, us_apellidos = :us_apellidos, us_nombres = :us_nombres, us_shortname = :us_shortname, us_fullname = :us_fullname, us_login = :us_login, us_password = :us_password, us_foto = :us_foto, us_genero = :us_genero, us_activo = :us_activo WHERE id_usuario = :id_usuario');

        //Vincular valores
        $id_usuario = $datos['id_usuario'];
        $this->db->bind(':id_usuario', $id_usuario);
        $this->db->bind(':us_titulo', $datos['us_titulo']);
        $this->db->bind(':us_apellidos', $datos['us_apellidos']);
        $this->db->bind(':us_nombres', $datos['us_nombres']);
        $this->db->bind(':us_shortname', $datos['us_shortname']);
        $this->db->bind(':us_fullname', $datos['us_fullname']);
        $this->db->bind(':us_login', $datos['us_login']);
        $this->db->bind(':us_password', $datos['us_password']);
        $this->db->bind(':us_foto', $datos['us_foto']);
        $this->db->bind(':us_genero', $datos['us_genero']);
        $this->db->bind(':us_activo', $datos['us_activo']);

        $this->db->execute();

        $this->db->query("DELETE FROM sw_usuario_perfil WHERE id_usuario = $id_usuario");
        $this->db->execute();

        for ($i = 0; $i < count($datos['id_perfil']); $i++) {
            //Insertar en la tabla sw_usuario_perfil
            $this->db->query("INSERT INTO sw_usuario_perfil (id_usuario, id_perfil) VALUES (:id_usuario, :id_perfil)");

            //Vincular valores
            $this->db->bind(':id_usuario', $id_usuario);
            $this->db->bind(':id_perfil', $datos['id_perfil'][$i]);

            $this->db->execute();
        }
    }

    public function eliminarUsuario($id)
    {
        $this->db->query('DELETE FROM `sw_usuario` WHERE `id_usuario` = :id_usuario');

        //Vincular valores
        $this->db->bind(':id_usuario', $id);

        return $this->db->execute();
    }
}
