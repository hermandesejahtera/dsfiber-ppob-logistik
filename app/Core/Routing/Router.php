<?php

namespace DSFiber\Core\Routing;

/**
 * Router Class
 */
class Router
{
    private array $routes = [];
    private string $method;
    private string $path;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Register GET route
     */
    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * Register POST route
     */
    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Register PUT route
     */
    public function put(string $path, callable $callback): void
    {
        $this->routes['PUT'][$path] = $callback;
    }

    /**
     * Register DELETE route
     */
    public function delete(string $path, callable $callback): void
    {
        $this->routes['DELETE'][$path] = $callback;
    }

    /**
     * Dispatch request
     */
    public function dispatch(): void
    {
        if (isset($this->routes[$this->method][$this->path])) {
            $callback = $this->routes[$this->method][$this->path];
            $response = call_user_func($callback);
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Route not found
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found', 'code' => 404]);
        exit;
    }
}
