<?php
class Categorias extends Controlador
{
    private $categoriaModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->categoriaModelo = $this->modelo('Categoria');
    }

    public function index()
    {
        $categorias = $this->categoriaModelo->obtenerTodos();
        $datos = [
            'titulo' => 'Categorías de Especialidad',
            'categorias' => $categorias,
            'nombreVista' => 'admin/categorias/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Crear Categorías de Especialidad',
            'nombreVista' => 'admin/categorias/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function store()
    {
        if ($this->categoriaModelo->existeNombre($_POST['nombre'])) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre de la categoría de la especialidad en la base de datos.";
            redireccionar('/categorias/create');
        } else {
            $datos = [
                'nombre' => $_POST['nombre']
            ];
            try {
                $this->categoriaModelo->insertar($datos);
                $_SESSION['mensaje_exito'] = "Categoría de Especialidad insertada exitosamente en la base de datos.";
                redireccionar('/categorias');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Categoría de Especialidad no fue insertada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/categorias');
            }
        }
    }

    public function edit($id)
    {
        $categoria = $this->categoriaModelo->obtener($id);
        
        $datos = [
            'titulo' => 'Editar Categoría de Especialidad',
            'categoria' => $categoria,
            'nombreVista' => 'admin/categorias/edit.php'
        ];

        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_categoria = $_POST['id_categoria'];
        $nombre = $_POST['nombre'];

        $categoriaActual = $this->categoriaModelo->obtener($id_categoria);

        if ($categoriaActual->nombre != $nombre && $this->categoriaModelo->existeNombre($nombre)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre de la categoría de especialidad en la base de datos.";
            $_SESSION['nombre'] = $nombre;
            redireccionar('/categorias/edit/' . $id_categoria);
        } else {
            $datos = [
                'id_categoria' => $id_categoria,
                'nombre' => $nombre
            ];
            try {
                $this->categoriaModelo->actualizar($datos);
                $_SESSION['mensaje_exito'] = "Categoría de Especialidad actualizada exitosamente en la base de datos.";
                redireccionar('/categorias');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Categoría de Especialidad no fue actualizada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/categorias');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->categoriaModelo->eliminar($id);
            $_SESSION['mensaje_exito'] = "Categoría de Especialidad eliminada exitosamente de la base de datos.";
            redireccionar('/categorias');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "La Categoría de Especialidad no fue eliminada exitosamente. Error: " . $ex->getMessage();
            redireccionar('/categorias');
        }
    }
}
