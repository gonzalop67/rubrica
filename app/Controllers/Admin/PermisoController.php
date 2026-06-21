<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

use App\Models\Admin\Permiso;

class PermisoController extends Controller
{
    protected Permiso $permissionModel;

    public function __construct()
    {
        parent::__construct(); // <--- ESTO ES OBLIGATORIO
        $this->permissionModel = new Permiso;
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index()
    {
        $title = 'Permisos | ' . APP_NAME;
        $search = trim($_GET['search'] ?? '');

        if ($search !== '') {
            // 1. Creamos la estructura SQL agrupada con paréntesis para proteger la lógica
            $this->permissionModel->where = "(nombre LIKE ? OR slug LIKE ? OR descripcion LIKE ?)";

            // 2. Preparamos los comodines de forma segura
            $term = "%{$search}%";

            // 3. Pasamos los valores al arreglo que procesará el prepare del ORM
            $this->permissionModel->values = [$term, $term, $term];
        }

        // El ORM inyectará de forma automática el ORDER BY y resolverá la paginación
        $permisos = $this->permissionModel
            ->orderBy('nombre')
            ->paginate(5);

        // return $permisos;

        return $this->view('admin.permisos.index', compact('permisos', 'title'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $title = 'Crear PermisoController';
        // return $this->view('admin.permiso.create', compact('title'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store()
    {
        // $this->model->create($_POST);
        // return redirect('/permiso');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show($id)
    {
        // $data = $this->model->find($id);
        // return $this->view('admin.permiso.show', compact('data'));
    }

    /**
     * Muestra el formulario para editar un recurso específico.
     */
    public function edit($id)
    {
        $title = 'Editar PermisoController';
        // $data = $this->model->find($id);
        // return $this->view('admin.permiso.edit', compact('data', 'title'));
    }

    /**
     * Actualiza un recurso específico en la base de datos.
     */
    public function update($id)
    {
        // $this->model->update($id, $_POST);
        // return redirect('/permiso');
    }

    /**
     * Elimina un recurso específico de la base de datos.
     */
    public function destroy($id)
    {
        // $this->model->delete($id);
        // return redirect('/permiso');
    }
}
