<?php

namespace App\Controllers;

use App\Controllers\Controller;

use App\Models\Perfil;
use App\Models\Institucion;
use App\Models\Admin\Usuario;

use Core\Encrypter;

class LoginController extends Controller
{
    protected Usuario $userModel;
    protected Perfil $perfilModel;
    protected Institucion $institucionModel;

    public function __construct()
    {
        parent::__construct(); // <--- ESTO ES OBLIGATORIO
        $this->userModel = new Usuario;
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

    public function login()
    {
        $username = $_POST['usuario'];
        $password = $_POST['clave'];
        $id_perfil = $_POST['perfil'];

        // Verify data login
        $clave = Encrypter::encrypt($password);

        $usuario = $this->userModel
            ->where('us_login', $username)
            ->where('us_password', $clave)
            ->first();

        if (!empty($usuario)) {
            // Verificar si el perfil ingresado pertenece al usuario
            $id_usuario = $usuario['id_usuario'];
        }

        echo json_encode([
            'ok' => true,
            'usuario' => $id_usuario
        ]);
    }
}
