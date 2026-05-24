<?php
class Modalidades extends Controlador
{
    private $modalidadModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->modalidadModelo = $this->modelo('Modalidad');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Modalidades',
            'nombreVista' => 'admin/modalidades/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function paginar()
    {
        $paginaActual = $_POST['partida'];
        echo $this->modalidadModelo->paginacion($paginaActual);
    }

    public function cargar()
    {
        $modalidades = $this->modalidadModelo->obtenerModalidades();
        $cadena = "";
        if (!empty($modalidades)) {
            foreach ($modalidades as $v) {
                $code = $v->id_modalidad;
                $name = $v->mo_nombre;
                $cadena .= "<option value=\"$code\">$name</option>";
            }
        }
        echo $cadena;
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Modalidades Crear',
            'nombreVista' => 'admin/modalidades/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        // Validaciones
        if ($this->modalidadModelo->existeModalidad($_POST['mo_nombre'])) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe el nombre de la modalidad en la base de datos.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            echo json_encode($datos);
        } else {
            $datos = [
                'mo_nombre' => trim($_POST['mo_nombre']),
                'mo_activo' => trim($_POST['mo_activo'])
            ];

            echo $this->modalidadModelo->insertarModalidad($datos);
        }
    }

    public function edit($id)
    {
        //Obtener información de la modalidad desde el modelo
        $modalidad = $this->modalidadModelo->obtenerModalidadPorId($id);
        $datos = [
            'titulo' => 'Modalidades Editar',
            'modalidad' => $modalidad,
            'nombreVista' => 'admin/modalidades/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function obtener()
    {
        //Obtener información de la modalidad desde el modelo
        $modalidad = $this->modalidadModelo->obtenerModalidadPorId($_POST['id']);

        echo json_encode($modalidad);
    }

    public function update()
    {
        // Validaciones
        $modalidadActual = $this->modalidadModelo->obtenerModalidadPorId($_POST['id_modalidad']);
        if ($modalidadActual->mo_nombre != $_POST['mo_nombre'] && $this->modalidadModelo->existeModalidad($_POST['mo_nombre'])) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe el nombre de la modalidad en la base de datos.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            echo json_encode($datos);
        } else {
            $datos = [
                'id_modalidad' => trim($_POST['id_modalidad']),
                'mo_nombre' => trim($_POST['mo_nombre']),
                'mo_activo' => trim($_POST['mo_activo'])
            ];

            echo $this->modalidadModelo->actualizarModalidad($datos);
        }
    }

    public function delete()
    {
        $id = $_POST['id'];
        echo $this->modalidadModelo->eliminarModalidad($id);
    }

    public function saveNewPositions()
    {
        foreach($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->modalidadModelo->actualizarOrden($index, $newPosition);
        }
    }
}
