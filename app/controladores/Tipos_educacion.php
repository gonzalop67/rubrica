<?php
class Tipos_educacion extends Controlador
{
    private $tipoEducacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->tipoEducacionModelo = $this->modelo('Tipo_educacion');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $tipos_educacion = $this->tipoEducacionModelo->obtenerNivelesEducacion($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Niveles de Educación',
            'tipos_educacion' => $tipos_educacion,
            'nombreVista' => 'admin/tipos_educacion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Niveles de Educación Crear',
            'nombreVista' => 'admin/tipos_educacion/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $te_nombre = $_POST['te_nombre'];
        $te_bachillerato = $_POST['te_bachillerato'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($this->tipoEducacionModelo->existeNombreNivelDeEducacion($te_nombre)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del nivel de educación en la base de datos.";
            redireccionar('/tipos_educacion/create');
        } else {
            $datos = [
                'te_nombre' => $te_nombre,
                'te_bachillerato' => $te_bachillerato,
                'id_periodo_lectivo' => $id_periodo_lectivo
            ];
            try {
                $this->tipoEducacionModelo->insertarNivelEducacion($datos);
                $_SESSION['mensaje_exito'] = "Nivel de educación insertado exitosamente en la base de datos.";
                redireccionar('/tipos_educacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El nivel de educación no fue insertado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/tipos_educacion');
            }
        }
    }

    public function edit($id)
    {
        $tipo_educacion = $this->tipoEducacionModelo->obtenerNivelEducacion($id);

        $datos = [
            'titulo' => 'Niveles de Educación Editar',
            'tipo_educacion' => $tipo_educacion,
            'nombreVista' => 'admin/tipos_educacion/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_nivel_educacion = $_POST['id_nivel_educacion'];
        $nombre = $_POST['nombre'];
        $es_bachillerato = $_POST['es_bachillerato'];

        $nivelEducacionActual = $this->tipoEducacionModelo->obtenerNivelEducacion($id_nivel_educacion);

        if ($nivelEducacionActual->nombre != $nombre && $this->tipoEducacionModelo->existeNombreNivelDeEducacion($nombre)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del nivel de educación en la base de datos.";
            redireccionar('/tipos_educacion/edit/' . $id_nivel_educacion);
        } else {
            $datos = [
                'id_nivel_educacion' => $id_nivel_educacion,
                'nombre' => $nombre,
                'es_bachillerato' => $es_bachillerato
            ];
            try {
                $this->tipoEducacionModelo->actualizarNivelEducacion($datos);
                $_SESSION['mensaje_exito'] = "Nivel de educación actualizado exitosamente en la base de datos.";
                redireccionar('/tipos_educacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El nivel de educación no fue actualizado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/tipos_educacion');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->tipoEducacionModelo->eliminarNivelEducacion($id);
            $_SESSION['mensaje_exito'] = "Nivel de educación eliminado exitosamente de la base de datos.";
            redireccionar('/tipos_educacion');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "No se puede eliminar el Nivel de educación porque tiene registros asociados en especialidades. Error: " . $ex->getMessage();
            redireccionar('/tipos_educacion');
        }
    }
}
