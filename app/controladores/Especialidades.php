<?php
class Especialidades extends Controlador
{
    private $especialidadModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->especialidadModelo = $this->modelo('Especialidad');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $especialidades = $this->especialidadModelo->obtenerEspecialidades($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Especialidades',
            'especialidades' => $especialidades,
            'nombreVista' => 'admin/especialidades/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Especialidades Crear',
            'nombreVista' => 'admin/especialidades/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function store()
    {
        $es_nombre = $_POST['es_nombre'];
        $es_figura = $_POST['es_figura'];
        $es_abreviatura = $_POST['es_abreviatura'];

        if ($this->especialidadModelo->existeFiguraEspecialidad($es_figura)) {
            $_SESSION['mensaje_error'] = "Ya existe la figura de la especialidad en la base de datos.";
            redireccionar('/especialidades/create');
        } else if ($this->especialidadModelo->existeNombreEspecialidad($es_nombre)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre de la especialidad en la base de datos.";
            redireccionar('/especialidades/create');
        } else if ($this->especialidadModelo->existeAbreviaturaEspecialidad($es_abreviatura)) {
            $_SESSION['mensaje_error'] = "Ya existe la abreviatura de la especialidad en la base de datos.";
            redireccionar('/especialidades/create');
        } else {
            $datos = [
                'es_nombre' => $es_nombre,
                'es_figura' => $es_figura,
                'es_abreviatura' => $es_abreviatura
            ];
            try {
                $this->especialidadModelo->insertarEspecialidad($datos);
                $_SESSION['mensaje_exito'] = "Especialidad insertada exitosamente en la base de datos.";
                redireccionar('/especialidades');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Especialidad no fue insertada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/especialidades');
            }
        }
    }

    public function edit($id)
    {
        $especialidad = $this->especialidadModelo->obtenerEspecialidad($id);

        $datos = [
            'titulo' => 'Especialidad Editar',
            'especialidad' => $especialidad,
            'nombreVista' => 'admin/especialidades/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_especialidad = $_POST['id_especialidad'];
        $es_nombre = $_POST['es_nombre'];
        $es_figura = $_POST['es_figura'];
        $es_abreviatura = $_POST['es_abreviatura'];

        $especialidadActual = $this->especialidadModelo->obtenerEspecialidad($id_especialidad);

        if ($especialidadActual->es_nombre != $es_nombre && $this->especialidadModelo->existeNombreEspecialidad($es_nombre)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre de la especialidad en la base de datos.";
            redireccionar('/especialidades/edit/' . $id_especialidad);
        } else if ($especialidadActual->es_figura != $es_figura && $this->especialidadModelo->existeFiguraEspecialidad($es_figura)) {
            $_SESSION['mensaje_error'] = "Ya existe la figura de la especialidad en la base de datos.";
            redireccionar('/especialidades/edit/' . $id_especialidad);
        } else if ($especialidadActual->es_abreviatura != $es_abreviatura && $this->especialidadModelo->existeAbreviaturaEspecialidad($es_abreviatura)) {
            $_SESSION['mensaje_error'] = "Ya existe la abreviatura de la especialidad en la base de datos.";
            redireccionar('/especialidades/edit/' . $id_especialidad);
        } else {
            $datos = [
                'id_especialidad' => $id_especialidad,
                'es_nombre' => $es_nombre,
                'es_figura' => $es_figura,
                'es_abreviatura' => $es_abreviatura
            ];
            try {
                $this->especialidadModelo->actualizarEspecialidad($datos);
                $_SESSION['mensaje_exito'] = "Especialidad actualizada exitosamente en la base de datos.";
                redireccionar('/especialidades');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Especialidad no fue actualizada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/especialidades');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->especialidadModelo->eliminarEspecialidad($id);
            $_SESSION['mensaje_exito'] = "Especialidad eliminada exitosamente de la base de datos.";
            redireccionar('/especialidades');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "No se puede eliminar la Especialidad porque tiene Cursos asociados. Error: " . $ex->getMessage();
            redireccionar('/especialidades');
        }
    }
}