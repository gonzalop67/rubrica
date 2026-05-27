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
        // Limpiar la URI
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME']));

        $uri = trim(str_replace($basePath, '', $uri), '/');

        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset(self::$routes[$method])) {
            echo "404 $method Not Found";
            return;
        }

        foreach (self::$routes[$method] as $routePath => $routeData) {
            // $routeData now contains ['callback' => ..., 'middlewares' => ...]

            $pattern = $routePath;

            if (strpos($routePath, ':') !== false) {
                $pattern = preg_replace('#:[a-zA-Z]+#', '([a-zA-Z0-9]+)', $routePath);
            }

            if (preg_match("#^$pattern$#", $uri, $matches)) {

                $params = array_slice($matches, 1);

                // 1. Run Middlewares
                if (!empty($routeData['middlewares'])) {
                    foreach ($routeData['middlewares'] as $middleware) {
                        // If middleware is a class string, instantiate it; if callable, call it
                        if (is_string($middleware) && class_exists($middleware)) {
                            $m = new $middleware();
                            $m->handle(); // Assuming your middlewares have a handle() method
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
                } else
                    echo $response;

                return;
            }
        }

        echo "404 $uri Not Found";
    }
}
