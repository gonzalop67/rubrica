<?php
class Examen_categorias extends Controlador
{
    private $categoriaModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->categoriaModelo = $this->modelo('Examen_categoria');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Categorias',
            'nombreVista' => 'docente/cuestionarios/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Categorías Crear',
            'nombreVista' => 'docente/cuestionarios/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $id_usuario = $_POST["id_usuario"];
        $category = $_POST["category"];
        $exam_time_in_minutes = $_POST["exam_time_in_minutes"];

        // Validaciones
        if ($this->categoriaModelo->existeCategoria($category, $id_usuario)) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe el nombre de la categoria en la base de datos.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            echo json_encode($datos);
        } else {
            $datos = [
                'id_usuario' => $id_usuario,
                'category' => trim($category),
                'exam_time_in_minutes' => trim($exam_time_in_minutes)
            ];

            echo $this->categoriaModelo->insertarCategoria($datos);
        }
    }

    public function obtener()
    {
        //Obtener información de la categoria desde el modelo
        $categoria = $this->categoriaModelo->obtenerCategoriaPorId($_POST['id']);

        echo json_encode($categoria);
    }

    public function update()
    {
        // Validaciones
        $categoriaActual = $this->categoriaModelo->obtenerCategoriaPorId($_POST['id']);
        if ($categoriaActual->category != $_POST['category'] && $this->categoriaModelo->existeCategoria($_POST['category'], $_POST['id_usuario'])) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe el nombre de la categoria en la base de datos.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            echo json_encode($datos);
        } else {
            $datos = [
                'id' => trim($_POST['id']),
                'category' => trim($_POST['category']),
                'exam_time_in_minutes' => trim($_POST['exam_time_in_minutes'])
            ];

            echo $this->categoriaModelo->actualizarCategoria($datos);
        }
    }

    /* public function cargar_asignaturas()
    {
        $id_usuario = $_POST["id_usuario"];
        $id_periodo_lectivo = $_POST["id_periodo_lectivo"];
        $asignaturas = $this->asignaturaModelo->obtenerAsignaturasDocente($id_usuario, $id_periodo_lectivo);
        $cadena = "";
        if (!empty($asignaturas)) {
            foreach ($asignaturas as $v) {
                $code = $v->id_asignatura;
                $name = $v->as_nombre;
                $cadena .= "<option value=\"$code\">$name</option>";
            }
        }
        echo $cadena;
    } */

    /* public function cargar_paralelos()
    {
        $id_usuario = $_POST["id_usuario"];
        $id_periodo_lectivo = $_POST["id_periodo_lectivo"];
        $paralelos = $this->paraleloModelo->obtenerParalelosDocente($id_usuario, $id_periodo_lectivo);
        $cadena = "";
        if (!empty($paralelos)) {
            foreach ($paralelos as $v) {
                $code = $v->id_paralelo;
                $name = $v->cu_abreviatura.$v->pa_nombre." ".$v->es_abreviatura;
                $cadena .= "<option value=\"$code\">$name</option>";
            }
        }
        echo $cadena;
    } */

    public function add_edit_questions($id_categoria)
    {
        $datos = [
            'titulo' => 'Añadir Editar Preguntas',
            'id_categoria' => $id_categoria,
            'nombreVista' => 'docente/preguntas/add_edit_questions.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function cargar()
    {
        $id_usuario = $_POST['id_usuario'];

        if (isset($_SESSION["end_time"])) {
            unset($_SESSION["end_time"]);
        }

        echo $this->categoriaModelo->obtenerCategoriasPorUsuario($id_usuario);
    }

    public function paginar()
    {
        $paginaActual = $_POST['partida'];
        $id_usuario = $_POST['id_usuario'];

        echo $this->categoriaModelo->paginacion($paginaActual, $id_usuario);
    }

    public function paginar_select()
    {
        $paginaActual = $_POST['partida'];
        $id_usuario = $_POST['id_usuario'];

        echo $this->categoriaModelo->paginacion_select($paginaActual, $id_usuario);
    }

    public function delete()
    {
        //
    }
}
