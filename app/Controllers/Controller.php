<?php

namespace App\Controllers;

use Core\MiniBlade;

class Controller
{
    protected MiniBlade $blade;

    public function __construct()
    {
        // Limpio, elegante y delegando la responsabilidad a MiniBlade
        $this->blade = new MiniBlade();
    }

    // Métodos comunes para controladores
    protected function view(string $route, array $data = [])
    {
        // 1. Aseguramos que la sesión esté iniciada para poder leer el menú
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Inyectamos de forma invisible el menú guardado en la sesión
        $data['menuItems'] = $_SESSION['menuItems'] ?? [];

        // 3. Tu lógica nativa para extraer variables en el entorno actual
        extract($data);

        // 4. Se envía el arreglo actualizado con el menú incluido hacia Blade/MiniBlade
        return $this->blade->render($route, $data);
    }

    protected function json(mixed $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
