<?php
class Auth extends Controlador
{
    private $perfilModelo;
    private $jornadaModelo;
    private $usuarioModelo;
    private $estudianteModelo;
    private $institucionModelo;
    private $periodoLectivoModelo;
    
    public function __construct()
    {
        $this->perfilModelo = $this->modelo('Perfil');
        $this->jornadaModelo = $this->modelo('Jornada');
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->estudianteModelo = $this->modelo('Estudiante');
        $this->institucionModelo = $this->modelo('Institucion');
        $this->periodoLectivoModelo = $this->modelo('PeriodoLectivo');
    }

    public function index()
    {
        $perfiles = $this->perfilModelo->obtenerPerfiles();
        $nombreInstitucion = $this->institucionModelo->obtenerNombreInstitucion();
        $periodosLectivos = $this->periodoLectivoModelo->obtenerPeriodosLectivos();
        $datos = [
            'titulo' => 'Login',
            'perfiles' => $perfiles,
            'periodosLectivos' => $periodosLectivos,
            'nombreInstitucion' => $nombreInstitucion
        ];
        $this->vista('auth/login', $datos);
    }

    public function login()
    {
        $username = $_POST["uname"];
        $password = $_POST["passwd"];
        $id_perfil = $_POST["cboPerfil"];
        $id_periodo_lectivo = $_POST["cboPeriodo"];

        $clave = encrypter::encrypt($password);
        $usuario = $this->usuarioModelo->obtenerUsuario($username, $clave, $id_perfil);

        session_start();
        $_SESSION['usuario_logueado'] = false;

        if (!empty($usuario)) {
            $_SESSION['usuario_logueado'] = true;
            $_SESSION['institucion_id'] = $usuario->institucion_id;
            $_SESSION['id_periodo_lectivo'] = $id_periodo_lectivo;
            $_SESSION['id_usuario'] = $usuario->id_usuario;
            $_SESSION['pe_nombre'] = $usuario->pe_nombre;
            $_SESSION['id_perfil'] = $id_perfil;
            $_SESSION['cambio_paralelo'] = 0;
            
            echo json_encode(array(
                'error' => false,
                'id_usuario' => $usuario->id_usuario,
                'pe_nombre' => $usuario->pe_nombre
            ));
        } else {
            echo json_encode(array(
                'error' => true,
                'id_usuario' => 0,
                'pe_nombre' => ''
            ));
        }
    }

    public function dashboard()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        } else {
            $numero_autoridades = $this->usuarioModelo->contarAutoridades();
            $numero_docentes = $this->usuarioModelo->contarDocentes();
            $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
            $numero_estudiantes = $this->estudianteModelo->contarEstudiantes($id_periodo_lectivo);
            $num_representantes = $this->usuarioModelo->contarRepresentantes($id_periodo_lectivo);
            $jornadas = $this->jornadaModelo->obtenerJornadasPorPeriodoLectivo($id_periodo_lectivo);
            $datos = [
                'titulo' => 'Dashboard',
                'numero_autoridades' => $numero_autoridades,
                'numero_docentes' => $numero_docentes,
                'numero_estudiantes' => $numero_estudiantes,
                'num_representantes' => $num_representantes,
                'jornadas' => $jornadas,
                'nombreVista' => 'admin/dashboard.php'
            ];
            $this->vista('admin/index', $datos);
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        redireccionar('/auth');
    }
}
