<?php
class Subniveles_educacion extends Controlador
{
    private $subNivelEducacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->subNivelEducacionModelo = $this->modelo('Subnivel_educacion');
    }

    public function index()
    {
        $subniveles_educacion = $this->subNivelEducacionModelo->obtenerTodos();
        $datos = [
            'titulo' => 'CRUD Sub Nivel de Educación',
            'subniveles_educacion' => $subniveles_educacion,
            'nombreVista' => 'admin/subnivel_educacion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Crear Sub Niveles de Educación',
            'nombreVista' => 'admin/subnivel_educacion/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function store()
    {
        $institucion_id = $_SESSION['institucion_id'];
        $nombre = preg_replace('/\s+/', ' ', strtoupper(trim($_POST['te_nombre'])));
        $es_bachillerato = trim($_POST['te_bachillerato']);

        $ok = false;
        $titulo = "";
        $mensaje = "";
        $tipo_mensaje = "";

        $datos = [
            'institucion_id' => $institucion_id,
            'nombre' => $nombre,
            'es_bachillerato' => $es_bachillerato
        ];

        if ($this->subNivelEducacionModelo->existeNombre($nombre)) {
            $_SESSION['mensaje'] = "Ya existe el nombre del subnivel de educación ingresado.";
            $_SESSION['tipo'] = "danger";
            $_SESSION['icono'] = "ban";
            $_SESSION['nombre'] = $nombre;
            $_SESSION['es_bachillerato'] = $es_bachillerato;
            redireccionar('/subniveles_educacion/create');
        } else {
            try {
                $this->subNivelEducacionModelo->insertar($datos);
                $_SESSION['mensaje'] = "Subnivel de Educación insertado exitosamente en la base de datos.";
                $_SESSION['tipo'] = "success";
                $_SESSION['icono'] = "check";
                redireccionar('/subniveles_educacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje'] = "El Subnivel de Educación no fue insertado exitosamente. Error: " . $ex->getMessage();
                $_SESSION['tipo'] = "danger";
                $_SESSION['icono'] = "ban";
                redireccionar('/subniveles_educacion');
            }
        }

        echo json_encode(array(
            'ok' => $ok,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo_mensaje' => $tipo_mensaje
        ));
    }

    public function edit($id)
    {
        $subnivel = $this->subNivelEducacionModelo->obtener($id);

        $datos = [
            'titulo' => 'Editar Sub Nivel de Educación',
            'subnivel' => $subnivel,
            'nombreVista' => 'admin/subnivel_educacion/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id = $_POST['id_sub_nivel_educacion'];
        $institucion_id = $_SESSION['institucion_id'];
        $nombre = preg_replace('/\s+/', ' ', strtoupper(trim($_POST['te_nombre'])));
        $es_bachillerato = trim($_POST['te_bachillerato']);

        $subnivel_actual = $this->subNivelEducacionModelo->obtener($id);

        $ok = false;
        $titulo = "";
        $mensaje = "";
        $tipo_mensaje = "";

        $datos = [
            'id' => $id,
            'institucion_id' => $institucion_id,
            'nombre' => $nombre,
            'es_bachillerato' => $es_bachillerato
        ];

        if ($subnivel_actual->nombre != $nombre && $this->subNivelEducacionModelo->existeNombre($nombre)) {
            $_SESSION['mensaje'] = "Ya existe el nombre del subnivel de educación ingresado.";
            $_SESSION['tipo'] = "danger";
            $_SESSION['icono'] = "ban";
            $_SESSION['nombre'] = $nombre;
            $_SESSION['es_bachillerato'] = $es_bachillerato;
            redireccionar('/subniveles_educacion/create');
        } else {
            try {
                $this->subNivelEducacionModelo->actualizar($datos);
                $_SESSION['mensaje'] = "Subnivel de Educación actualizado exitosamente en la base de datos.";
                $_SESSION['tipo'] = "success";
                $_SESSION['icono'] = "check";
                redireccionar('/subniveles_educacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje'] = "El Subnivel de Educación no fue actualizado exitosamente. Error: " . $ex->getMessage();
                $_SESSION['tipo'] = "danger";
                $_SESSION['icono'] = "ban";
                redireccionar('/subniveles_educacion');
            }
        }

        echo json_encode(array(
            'ok' => $ok,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo_mensaje' => $tipo_mensaje
        ));
    }

    public function delete($id)
    {
        try {
            $this->subNivelEducacionModelo->eliminar($id);
            $_SESSION['mensaje'] = "Subnivel de Educación eliminado exitosamente de la base de datos.";
            $_SESSION['tipo'] = "success";
            $_SESSION['icono'] = "check";
            redireccionar('/subniveles_educacion');
        } catch (PDOException $ex) {
            $_SESSION['mensaje'] = "El Subnivel de Educación no fue eliminado exitosamente. Error: " . $ex->getMessage();
            $_SESSION['tipo'] = "danger";
            $_SESSION['icono'] = "ban";
            redireccionar('/subniveles_educacion');
        }
    }
}