<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

use App\Models\Admin\Menu;
use App\Models\Admin\Perfil;
use App\Models\Admin\Permiso;

class MenuController extends Controller
{
    protected Menu $menuModel;
    protected Perfil $perfilModel;
    protected Permiso $permisoModel;

    public function __construct()
    {
        parent::__construct();
        $this->menuModel = new Menu;
        $this->perfilModel = new Perfil;
        $this->permisoModel = new Permiso;
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index()
    {
        $title = 'Listado de Menús';

        // 1. Carga los perfiles activos ordenados alfabéticamente
        $roles = $this->perfilModel->orderBy('pe_nombre', 'ASC')->get();

        // 2. Carga los menús principales (donde mnu_padre es exactamente 0)
        // El Soft Delete (deleted_at IS NULL) se inyecta automáticamente gracias a tu clase base
        // 2.1. Aplicamos la condición de filtrado para menús raíz
        $this->menuModel->where = "sw_menu.mnu_padre = 0";

        // 2.2. Construimos y ejecutamos la consulta encadenando todos tus métodos nativos
        $menus_principales = $this->menuModel
            ->select('sw_menu.id_menu', 'sw_menu.mnu_texto', 'sw_perfil.pe_nombre')
            ->join('sw_menu_perfil', 'sw_menu.id_menu', '=', 'sw_menu_perfil.id_menu')
            ->join('sw_perfil', 'sw_perfil.id_perfil', '=', 'sw_menu_perfil.id_perfil')
            ->orderBy('sw_perfil.pe_nombre', 'ASC')
            ->orderBy('sw_menu.mnu_orden', 'ASC')
            ->get();

        // 3. Cargar los permisos disponibles para el modal de edición
        $permisos_disponibles = $this->permisoModel->get() ?? [];

        return $this->view('admin.menus.index', compact('title', 'roles', 'menus_principales', 'permisos_disponibles'));
    }

    public function get_menu_ajax()
    {
        $id_rol = isset($_POST['perfil_id']) ? (int)$_POST['perfil_id'] : 0;

        if ($id_rol === 0) {
            echo '<div id="nestable-placeholder"><div class="text-muted text-center py-4">Rol no válido.</div></div>';
            exit;
        }

        $sql = "SELECT m.id_menu, m.mnu_texto, m.mnu_link, m.mnu_padre, m.mnu_orden, m.mnu_icono 
                FROM `sw_menu` m 
                INNER JOIN `sw_menu_perfil` mp ON m.id_menu = mp.id_menu 
                INNER JOIN `sw_permiso` p ON m.permiso_slug = p.slug 
                INNER JOIN `sw_perfil_permiso` pp ON p.id_permiso = pp.id_permiso 
                WHERE mp.id_perfil = ? 
                AND pp.id_perfil = ? 
                AND m.mnu_publicado = 1 
                
                UNION 
                
                SELECT m.id_menu, m.mnu_texto, m.mnu_link, m.mnu_padre, m.mnu_orden, m.mnu_icono 
                FROM `sw_menu` m 
                INNER JOIN `sw_menu_perfil` mp ON m.id_menu = mp.id_menu 
                WHERE mp.id_perfil = ? 
                AND (m.permiso_slug IS NULL OR m.permiso_slug = '') 
                AND m.mnu_publicado = 1 
                
                ORDER BY mnu_padre ASC, mnu_orden ASC";

        $this->menuModel->query($sql, [$id_rol, $id_rol, $id_rol], 'iii');

        $rows = [];
        $queryResult = $this->menuModel->getQueryResult();

        if ($queryResult instanceof \mysqli_result) {
            $rows = $queryResult->fetch_all(MYSQLI_ASSOC);
        }

        if (empty($rows)) {
            echo '<div class="dd-empty text-muted text-center py-4">Este rol no tiene menús asignados.</div>';
            exit;
        }

        // 2. Construcción del Árbol Jerárquico 
        $menuTree = [];
        $submenus = [];

        foreach ($rows as $row) {
            if ($row['mnu_padre'] === null || (int)$row['mnu_padre'] === 0) {
                $row['submenu'] = [];
                $menuTree[$row['id_menu']] = $row;
            } else {
                $submenus[] = $row;
            }
        }

        foreach ($submenus as $sub) {
            $padreId = $sub['mnu_padre'];
            if (isset($menuTree[$padreId])) {
                $sub['submenu'] = []; // CORRECCIÓN: Inicializar clave para evitar errores en niveles inferiores
                $menuTree[$padreId]['submenu'][] = $sub;
            } else {
                $asignado = false;
                foreach ($menuTree as &$padreRaiz) {
                    if ($this->insertarEnHijo($padreRaiz, $sub)) { // CORRECCIÓN: Llamada con $this->
                        $asignado = true;
                        break;
                    }
                }
                if (!$asignado) {
                    $sub['submenu'] = [];
                    $menuTree[$sub['id_menu']] = $sub;
                }
            }
        }

        // 3. Renderizar y retornar el HTML directo 
        // CORRECCIÓN: Llamada al método usando $this->
        echo $this->renderNestableTree(array_values($menuTree));
    }

    // ========================================== 
    // MÉTODOS DE SOPORTE (Deben ir al mismo nivel que get_menu_ajax dentro de la clase)
    // ========================================== 
    private function insertarEnHijo(&$nodoPadre, $subnode)
    {
        if ($nodoPadre['id_menu'] == $subnode['mnu_padre']) {
            if (!isset($nodoPadre['submenu'])) {
                $nodoPadre['submenu'] = [];
            }
            $subnode['submenu'] = [];
            $nodoPadre['submenu'][] = $subnode;
            return true;
        }

        if (isset($nodoPadre['submenu']) && is_array($nodoPadre['submenu'])) {
            foreach ($nodoPadre['submenu'] as &$hijo) {
                if ($this->insertarEnHijo($hijo, $subnode)) { // CORRECCIÓN: Llamada con $this->
                    return true;
                }
            }
        }
        return false;
    }

    private function renderNestableTree(array $menus)
    {
        if (empty($menus)) return '';

        $html = '<ol class="dd-list">';
        foreach ($menus as $menu) {
            $hasChildren = !empty($menu['submenu']);

            $html .= '<li class="dd-item dd3-item" data-id="' . $menu["id_menu"] . '">';

            // SOLUCIÓN: Se eliminó el <i> interno. El CSS pintará el símbolo '≡' de forma limpia.
            $html .= ' <div class="dd-handle dd3-handle"></div>';

            // Se unificó el padding-left a 40px (como dicta tu clase .dd3-content) para que no se encime con el tirador
            $html .= ' <div class="dd3-content menu_link">';

            $iconoHtml = !empty($menu['mnu_icono']) ? '<i class="' . htmlspecialchars($menu['mnu_icono']) . '" style="margin-right: 8px; width: 16px; text-align: center;"></i> ' : '';

            $html .= ' <a href="#" onclick="obtenerDatos(' . $menu["id_menu"] . '); return false;">' . $iconoHtml . htmlspecialchars($menu["mnu_texto"]) . '</a>';

            $html .= ' <a href="menus/delete/' . $menu["id_menu"] . '" class="eliminar-menu pull-right" title="Eliminar este menú" style="margin-top: 2px;">';
            $html .= ' <i class="fa fa-trash text-danger"></i>';
            $html .= ' </a>';
            $html .= ' </div>';

            if ($hasChildren) {
                $html .= $this->renderNestableTree($menu['submenu']);
            }
            $html .= '</li>';
        }
        $html .= '</ol>';

        return $html;
    }


    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store()
    {
        // $this->model->create($_POST);
        // return redirect('/menu');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show($id)
    {
        // $data = $this->model->find($id);
        // return $this->view('admin.menu.show', compact('data'));
    }

    /**
     * Muestra el formulario para editar un recurso específico.
     */
    public function edit($id)
    {
        $title = 'Editar MenuController';
        // $data = $this->model->find($id);
        // return $this->view('admin.menu.edit', compact('data', 'title'));
    }

    /**
     * Actualiza un recurso específico en la base de datos.
     */
    public function update($id)
    {
        // $this->model->update($id, $_POST);
        // return redirect('/menu');
    }

    /**
     * Elimina un recurso específico de la base de datos.
     */
    public function destroy($id)
    {
        // $this->model->delete($id);
        // return redirect('/menu');
    }
}
