<?php
class Perfiles extends Controlador
{
    private $perfilModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->perfilModelo = $this->modelo('Perfil');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Perfiles',
            'nombreVista' => 'admin/perfiles/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        // Validaciones
        if ($this->perfilModelo->existePerfil($_POST['pe_nombre'])) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe el nombre del perfil en la base de datos.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            echo json_encode($datos);
        } else {
            $datos = [
                'pe_nombre' => trim($_POST['pe_nombre'])
            ];

            echo $this->perfilModelo->insertarPerfil($datos);
        }
    }

    public function obtener()
    {
        //Obtener información del perfil desde el modelo
        $perfil = $this->perfilModelo->obtenerPerfilPorId($_POST['id']);

        echo json_encode($perfil);
    }

    public function update()
    {
        // Validaciones
        $perfilActual = $this->perfilModelo->obtenerPerfilPorId($_POST['id_perfil']);
        if ($perfilActual->pe_nombre != $_POST['pe_nombre'] && $this->perfilModelo->existePerfil($_POST['pe_nombre'])) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe el nombre del perfil en la base de datos.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            echo json_encode($datos);
        } else {
            $datos = [
                'id_perfil' => trim($_POST['id_perfil']),
                'pe_nombre' => trim($_POST['pe_nombre'])
            ];

            echo $this->perfilModelo->actualizarPerfil($datos);
        }
    }

    public function delete()
    {
        $id_perfil = $_POST['id'];
        echo $this->perfilModelo->eliminarPerfil($id_perfil);
    }

    public function paginar()
    {
        $paginaActual = $_POST['partida'];
        echo $this->perfilModelo->paginacion($paginaActual);
    }
}
