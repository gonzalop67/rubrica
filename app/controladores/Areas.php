<?php
class Areas extends Controlador
{
    private $areaModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->areaModelo = $this->modelo('Area');
    }

    public function index()
    {
        $areas = $this->areaModelo->obtenerAreas();
        $datos = [
            'titulo' => 'Areas',
            'areas' => $areas,
            'nombreVista' => 'admin/areas/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Areas Crear',
            'nombreVista' => 'admin/areas/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        if ($this->areaModelo->existeNombreArea($_POST['ar_nombre'])) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del área en la base de datos.";
            redireccionar('/areas/create');
        } else {
            $datos = [
                'ar_nombre' => $_POST['ar_nombre']
            ];
            try {
                $this->areaModelo->insertarArea($datos);
                $_SESSION['mensaje_exito'] = "Area insertada exitosamente en la base de datos.";
                redireccionar('/areas');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Area no fue insertada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/areas');
            }
        }
    }

    public function edit($id)
    {
        $area = $this->areaModelo->obtenerArea($id);
        $datos = [
            'titulo' => 'Areas Editar',
            'area' => $area,
            'nombreVista' => 'admin/areas/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_area = $_POST['id_area'];
        $ar_nombre = $_POST['ar_nombre'];

        $areaActual = $this->areaModelo->obtenerArea($id_area);

        if ($areaActual->ar_nombre != $ar_nombre && $this->areaModelo->existeNombreArea($ar_nombre)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del área en la base de datos.";
            redireccionar('/areas/edit/' . $id_area);
        } else {
            $datos = [
                'id_area' => $id_area,
                'ar_nombre' => $ar_nombre
            ];
            try {
                $this->areaModelo->actualizarArea($datos);
                $_SESSION['mensaje_exito'] = "Area actualizada exitosamente en la base de datos.";
                redireccionar('/areas');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Area no fue actualizada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/areas');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->areaModelo->eliminarArea($id);
            $_SESSION['mensaje_exito'] = "Area eliminada exitosamente de la base de datos.";
            redireccionar('/areas');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Area no fue eliminada exitosamente. Error: " . $ex->getMessage();
            redireccionar('/areas');
        }
    }
}
