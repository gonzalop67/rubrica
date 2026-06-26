<?php

namespace Core;

class Route
{
    private static array $routes = [];

    public static function get(string $uri, callable|array $callback, ?array $middlewares = [])
    {
        $uri = trim($uri, '/');
        self::$routes['GET'][$uri] = [
            'callback'    => $callback,
            'middlewares' => $middlewares
        ];
    }

    public static function post(string $uri, callable|array $callback, array $middlewares = [])
    {
        $uri = trim($uri, '/');
        self::$routes['POST'][$uri] = [
            'callback'    => $callback,
            'middlewares' => $middlewares
        ];
    }

    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME']));
        $uri = trim(str_replace($basePath, '', $uri), '/');

        $method = $_SERVER['REQUEST_METHOD'];

        // Si no existen rutas registradas para este método (ej. POST), detener inmediatamente
        if (!isset(self::$routes[$method])) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'mensaje' => "Método $method no permitido."]);
            return;
        }

        // El foreach solo debe iterar sobre las rutas del método solicitado
        foreach (self::$routes[$method] as $routePath => $routeData) {

            // 1. Convertir la ruta en una expresión regular segura
            // Cambiamos los parámetros tipo :id por un grupo de captura (.+) o ([a-zA-Z0-9_-]+)
            // Escapamos las barras diagonales normales de la URL usando preg_quote

            $quotedRoute = preg_quote($routePath, '#');
            // Buscamos los parámetros que escapó preg_quote (aparecerán como \:id)
            $pattern = preg_replace('#\\\\:[a-zA-Z0-9]+#', '([a-zA-Z0-9_-]+)', $quotedRoute);

            // 2. Evaluar si la URI actual coincide con el patrón
            if (preg_match("#^$pattern$#", $uri, $matches)) {

                $params = array_slice($matches, 1);

                // 1. Run Middlewares
                if (!empty($routeData['middlewares'])) {
                    foreach ($routeData['middlewares'] as $middleware) {
                        if (is_string($middleware) && class_exists($middleware)) {
                            $m = new $middleware();
                            $m->handle();
                        } else if (is_callable($middleware)) {
                            $middleware();
                        }
                    }
                }

                // 2. Execute Callback
                $callback = $routeData['callback'];
                $response = null;

                if (is_callable($callback)) {
                    $response = $callback(...$params);
                } elseif (is_array($callback)) {
                    $controller = new $callback[0];
                    $response = $controller->{$callback[1]}(...$params);
                }

                // 3. Handle Response
                if (is_array($response) || is_object($response)) {
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    echo $response;
                }

                return;
            }
        }

        echo "404 $uri Not Found";
    }
}
