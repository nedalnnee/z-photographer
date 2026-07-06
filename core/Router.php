<?php
declare(strict_types=1);

namespace Core;

final class Router
{
    /** @var array<string, array<int, array{pattern:string, handler:callable}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    /**
     * @param callable|array{0:class-string,1:string} $handler
     */
    public function get(string $pattern, $handler): void
    {
        $this->routes['GET'][] = ['pattern' => $pattern, 'handler' => $handler];
    }

    /**
     * @param callable|array{0:class-string,1:string} $handler
     */
    public function post(string $pattern, $handler): void
    {
        $this->routes['POST'][] = ['pattern' => $pattern, 'handler' => $handler];
    }


    public function dispatch(string $route): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->normalize($route);

        foreach ($this->routes[$method] ?? [] as $r) {
            $regex = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $r['pattern']);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $path, $matches)) {
                $params = [];
                foreach ($matches as $k => $v) {
                    if (!is_int($k)) $params[$k] = $v;
                }
                $h = $r['handler'];
                // Support [ControllerClass::class, 'method'] by instantiating the controller.
                if (is_array($h) && is_string($h[0])) {
                    $controllerClass = $h[0];
                    $method = $h[1] ?? null;
                    $controller = new $controllerClass();
                    return (string) call_user_func([$controller, $method], $params);
                }
                return (string) call_user_func($h, $params);

            }
        }

        http_response_code(404);
        return $this->simplePage('404 - Not Found');
    }

    private function normalize(string $route): string
    {
        $route = trim($route);
        if ($route === '') return '/';
        if (!str_starts_with($route, '/')) $route = '/' . $route;
        // remove query string if provided
        $route = explode('?', $route, 2)[0];
        return rtrim($route, '/') ?: '/';
    }

    private function simplePage(string $title): string
    {
        return '<!doctype html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title></head><body><h1>' . htmlspecialchars($title) . '</h1></body></html>';
    }
}

