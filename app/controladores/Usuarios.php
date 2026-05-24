<?php
class Usuarios extends Controlador
{
    private $perfilModelo;
    private $usuarioModelo;
    private $usuarioPerfilModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->perfilModelo = $this->modelo('Perfil');
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->usuarioPerfilModelo = $this->modelo('Usuario_perfil');
    }

    public function index()
    {
        $usuarios = $this->usuarioModelo->obtenerUsuarios();
        $perfilesUsuarios = $this->perfilModelo->obtenerPerfilesUsuarios();
        $datos = [
            'titulo' => 'Usuarios',
            'usuarios' => $usuarios,
            'perfilesUsuarios' => $perfilesUsuarios,
            'nombreVista' => 'admin/usuarios/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function search()
    {
        $pattern = $_POST["search_pattern"];
        $usuarios = $this->usuarioModelo->buscarUsuarios($pattern);
        $perfilesUsuarios = $this->perfilModelo->obtenerPerfilesUsuarios();
        $datos = [
            'titulo' => 'Usuarios',
            'usuarios' => $usuarios,
            'search_pattern' => $pattern,
            'perfilesUsuarios' => $perfilesUsuarios,
            'nombreVista' => 'admin/usuarios/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function buscar()
    {
        $patron = trim($_POST['patron']);
        echo $this->usuarioModelo->buscarUsuario($patron);
    }

    public function create()
    {
        $perfiles = $this->perfilModelo->obtenerPerfiles();
        $datos = [
            'titulo' => 'Usuarios Crear',
            'perfiles' => $perfiles,
            'nombreVista' => 'admin/usuarios/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function upload_image()
    {
        if (isset($_FILES["us_foto"])) {
            $extension = explode('.', $_FILES['us_foto']['name']);
            $new_name = rand() . '.' . $extension[1];
            $destination = dirname(dirname(dirname(__FILE__))) . '/public/uploads/' . $new_name;
            move_uploaded_file($_FILES['us_foto']['tmp_name'], $destination);
            return $new_name;
        }
    }

    public function insert()
    {
        $us_titulo = trim($_POST['us_titulo']);
        $us_apellidos = preg_replace('/\s+/', ' ', trim($_POST['us_apellidos']));
        $us_nombres = preg_replace('/\s+/', ' ', trim($_POST['us_nombres']));
        $us_login = preg_replace('/\s+/', ' ', trim($_POST['us_login']));
        $apellidos = explode(" ", $us_apellidos);
        $nombres = explode(" ", $us_nombres);
        $us_shortname = $us_titulo . " " . $nombres[0] . " " . $apellidos[0];
        $us_fullname = $us_apellidos . " " . $us_nombres;

        if ($this->usuarioModelo->existeUsuarioPorNombreUsuario($us_login) && $this->usuarioModelo->existeUsuarioPorNombreCompleto($us_fullname)) {
            $_SESSION['mensaje_error'] = "Ya existe el Usuario [$us_fullname] en la Base de Datos.";
            redireccionar('/usuarios');
        } else if ($this->usuarioModelo->existeUsuarioPorNombreUsuario($us_login)) {
            $_SESSION['mensaje_error'] = "Ya existe el Nombre de Usuario [$us_login] en la Base de Datos.";
            redireccionar('/usuarios');
        } else {

            $image = $this->upload_image();

            $datos = [
                'us_titulo' => $us_titulo,
                'us_apellidos' => $us_apellidos,
                'us_nombres' => $us_nombres,
                'us_login' => $_POST['us_login'],
                'us_password' => encrypter::encrypt($_POST['us_password']),
                'us_shortname' => $us_shortname,
                'us_fullname' => $us_fullname,
                'us_genero' => $_POST['us_genero'],
                'us_foto' => $image,
                'us_activo' => $_POST['us_activo'],
                'id_perfil' => $_POST['id_perfil']
            ];

            try {
                $this->usuarioModelo->insertarUsuario($datos);

                $_SESSION['mensaje_exito'] = "Usuario insertado exitosamente en la base de datos.";
                redireccionar('/usuarios');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Usuario no fue insertado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/usuarios');
            }
        }
    }

    public function edit($id)
    {
        $usuario = $this->usuarioModelo->obtenerUsuarioPorId($id);
        $perfiles = $this->perfilModelo->obtenerPerfiles();
        $usuariosPerfil = $this->usuarioPerfilModelo->obtenerPerfilesUsuario($id);

        $datos = [
            'titulo' => 'Usuarios Editar',
            'usuario' => $usuario,
            'perfiles' => $perfiles,
            'usuarios_perfil' => $usuariosPerfil,
            'nombreVista' => 'admin/usuarios/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_usuario = $_POST['id_usuario'];
        $us_titulo = $_POST['us_titulo'];
        //preg_replace('/\s+/', ' ', $cadena_limpia)  //Reemplazar uno o más espacios entre palabras por un solo espacio
        $us_apellidos = preg_replace('/\s+/', ' ', trim($_POST['us_apellidos']));
        $us_nombres = preg_replace('/\s+/', ' ', trim($_POST['us_nombres']));
        $us_login = preg_replace('/\s+/', ' ', trim($_POST['us_login']));
        $apellidos = explode(" ", $us_apellidos);
        $nombres = explode(" ", $us_nombres);
        $us_shortname = $us_titulo . " " . $nombres[0] . " " . $apellidos[0];
        $us_fullname = $us_apellidos . " " . $us_nombres;

        $usuarioActual = $this->usuarioModelo->obtenerUsuarioPorId($id_usuario);

        if ($usuarioActual->us_fullname != $us_fullname && $this->usuarioModelo->existeUsuarioPorNombreCompleto($us_fullname)) {
            $_SESSION['mensaje_error'] = "Ya existe el Usuario [$us_fullname] en la Base de Datos.";
            redireccionar('/usuarios');
        } else if ($usuarioActual->us_login != $us_login && $this->usuarioModelo->existeUsuarioPorNombreUsuario($us_login)) {
            $_SESSION['mensaje_error'] = "Ya existe el Nombre de Usuario [$us_login] en la Base de Datos.";
            redireccionar('/usuarios');
        } else {

            if ($_FILES['us_foto']['name'] != "") {
                $image = $this->upload_image();
            } else {
                $image = $usuarioActual->us_foto;
            }

            $datos = [
                'id_usuario' => $id_usuario,
                'us_titulo' => $us_titulo,
                'us_apellidos' => $us_apellidos,
                'us_nombres' => $us_nombres,
                'us_login' => $_POST['us_login'],
                'us_password' => encrypter::encrypt($_POST['us_password']),
                'us_shortname' => $us_shortname,
                'us_fullname' => $us_fullname,
                'us_genero' => $_POST['us_genero'],
                'us_foto' => $image,
                'us_activo' => $_POST['us_activo'],
                'id_perfil' => $_POST['id_perfil']
            ];

            try {
                $this->usuarioModelo->actualizarUsuario($datos);
                $_SESSION['mensaje_exito'] = "Usuario actualizado exitosamente en la base de datos.";
                redireccionar('/usuarios');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Usuario no fue actualizado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/usuarios');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->usuarioModelo->eliminarUsuario($id);
            $_SESSION['mensaje_exito'] = "Usuario eliminado exitosamente de la base de datos.";
            redireccionar('/usuarios');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Usuario no se pudo eliminar porque tiene registros asociados en el distributivo.";
            redireccionar('/usuarios');
        }
    }
}
