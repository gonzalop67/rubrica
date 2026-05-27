<?php

namespace App\Controllers;

use App\Controllers\Controller;

use App\Models\Perfil;
use App\Models\Institucion;

class LoginController extends Controller
{
    protected Perfil $perfilModel;
    protected Institucion $institucionModel;

    public function __construct()
    {
        parent::__construct(); // <--- ESTO ES OBLIGATORIO
        $this->perfilModel = new Perfil;
        $this->institucionModel = new Institucion;
    }

    public function showLoginForm()
    {
        $institucion = $this->institucionModel
            ->select('in_nombre')
            ->orderBy('id_institucion')
            ->first();
        $nom_institucion = $institucion['in_nombre'];
        $perfiles = $this->perfilModel->orderBy('pe_nombre')->get();
        return $this->view('auth.login', compact('nom_institucion', 'perfiles'));
    }
}
