<?php
class Menus extends Controlador
{
    private $menuModelo;
    private $perfilModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->perfilModelo = $this->modelo('Perfil');
        $this->menuModelo = $this->modelo('Menu');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Menus',
            'nombreVista' => 'admin/menus/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $perfiles = $this->perfilModelo->obtenerPerfiles();
        $datos = [
            'titulo' => 'Menús Crear',
            'perfiles' => $perfiles,
            'nombreVista' => 'admin/menus/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function guardarOrden()
    {
        $menus = $_POST['menu'];
        echo $this->menuModelo->guardarOrden($menus);
    }

    public function insert()
    {
        $datos = [
            'mnu_texto' => trim($_POST['mnu_texto']),
            'mnu_link' => trim($_POST['mnu_link']),
            'mnu_publicado' => trim($_POST['mnu_publicado']),
            'id_perfil' => trim($_POST['id_perfil']),
        ];

        $this->menuModelo->insertarMenu($datos);
        redireccionar('/menus');
    }

    public function edit($id)
    {
        //Obtener el registro para editar
        $menu = $this->menuModelo->obtenerMenuPorId($id);
        $perfiles = $this->perfilModelo->obtenerPerfiles();
        $datos = [
            'titulo' => 'Menú Editar',
            'menu' => $menu,
            'perfiles' => $perfiles,
            'nombreVista' => 'admin/menus/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $datos = [
            'id_menu' => trim($_POST['id_menu']),
            'mnu_texto' => trim($_POST['mnu_texto']),
            'mnu_link' => trim($_POST['mnu_link']),
            'mnu_publicado' => trim($_POST['mnu_publicado']),
            'mnu_icono' => trim($_POST['mnu_icono']),
            'id_perfil' => trim($_POST['id_perfil']),
        ];

        $this->menuModelo->actualizarMenu($datos);
        redireccionar('/menus');
    }

    public function delete($id)
    {
        $this->menuModelo->eliminarMenu($id);
        redireccionar('/menus');
    }
}
