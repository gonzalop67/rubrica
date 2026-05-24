<?php
class Asignaturas extends Controlador
{
    private $areaModelo;
    private $asignaturaModelo;
    private $tipoAsignaturaModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->areaModelo = $this->modelo('Area');
        $this->asignaturaModelo = $this->modelo('Asignatura');
        $this->tipoAsignaturaModelo = $this->modelo('Tipo_asignatura');
    }

    public function index()
    {
        $asignaturas = $this->asignaturaModelo->obtenerAsignaturas();
        $datos = [
            'titulo' => 'Asignaturas',
            'asignaturas' => $asignaturas,
            'nombreVista' => 'admin/asignaturas/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function search()
    {
        $patron = trim($_POST['patron']);
        echo $this->asignaturaModelo->buscarAsignatura($patron);
    }

    public function create()
    {
        $areas = $this->areaModelo->obtenerAreas();
        $tiposAsignatura = $this->tipoAsignaturaModelo->obtenerTiposAsignatura();
        $datos = [
            'titulo' => 'Asignaturas Crear',
            'areas' => $areas,
            'tiposAsignatura' => $tiposAsignatura,
            'nombreVista' => 'admin/asignaturas/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        if ($this->asignaturaModelo->existeNombreAsignatura($_POST['as_nombre'])) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre de la asignatura en la base de datos.";
            redireccionar('/asignaturas/create');
        } else {
            $datos = [
                'as_nombre' => $_POST['as_nombre'],
                'as_shortname' => $_POST['as_shortname'],
                'as_abreviatura' => $_POST['as_abreviatura'],
                'id_area' => $_POST['id_area'],
                'id_tipo_asignatura' => $_POST['id_tipo_asignatura']
            ];
            try {
                $this->asignaturaModelo->insertarAsignatura($datos);
                $_SESSION['mensaje_exito'] = "Asignatura insertada exitosamente en la base de datos.";
                redireccionar('/asignaturas');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Asignatura no fue insertada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/asignaturas');
            }
        }
    }

    public function edit($id)
    {
        $areas = $this->areaModelo->obtenerAreas();
        $tiposAsignatura = $this->tipoAsignaturaModelo->obtenerTiposAsignatura();

        $asignatura = $this->asignaturaModelo->obtenerAsignatura($id);

        $datos = [
            'titulo' => 'Asignaturas Crear',
            'asignatura' => $asignatura,
            'areas' => $areas,
            'tiposAsignatura' => $tiposAsignatura,
            'nombreVista' => 'admin/asignaturas/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_asignatura = $_POST['id_asignatura'];
        $as_nombre = $_POST['as_nombre'];

        $asignaturaActual = $this->asignaturaModelo->obtenerAsignatura($id_asignatura);

        if ($asignaturaActual->as_nombre != $as_nombre && $this->asignaturaModelo->existeNombreAsignatura($as_nombre)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre de la asignatura en la base de datos.";
            redireccionar('/asignaturas/edit/' . $id_asignatura);
        } else {
            $datos = [
                'id_asignatura'      => $id_asignatura,
                'as_nombre'          => $as_nombre,
                'as_shortname'       => $_POST['as_shortname'],
                'as_abreviatura'     => $_POST['as_abreviatura'],
                'id_area'            => $_POST['id_area'],
                'id_tipo_asignatura' => $_POST['id_tipo_asignatura']
            ];
            try {
                $this->asignaturaModelo->actualizarAsignatura($datos);
                $_SESSION['mensaje_exito'] = "Asignatura actualizada exitosamente en la base de datos.";
                redireccionar('/asignaturas');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Asignatura no fue actualizada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/asignaturas');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->asignaturaModelo->eliminarAsignatura($id);
            $_SESSION['mensaje_exito'] = "Asignatura eliminada exitosamente de la base de datos.";
            redireccionar('/asignaturas');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "La Asignatura no fue eliminada exitosamente. Error: " . $ex->getMessage();
            redireccionar('/asignaturas');
        }
    }
}