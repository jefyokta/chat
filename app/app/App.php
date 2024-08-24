<?php

namespace oktaa\App;
use InvalidArgumentException;
use oktaa\http\Request\Request;
use oktaa\http\Response\Response;
class App
{
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    private $subApps = [];

    public function get($path, callable | array | App $callback)
    {
        $this->routes['GET'][$path] = $callback;
        return $this;
    }

    public function post($path, callable | array | App $callback)
    {
        $this->routes['POST'][$path] = $callback;
        return $this;
    }

    public function put($path, callable | array | App $callback)
    {
        $this->routes['PUT'][$path] = $callback;
        return $this;
    }

    public function delete($path, callable | array | App $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
        return $this;
    }

    public function use($prefix, $subApp)
    {
        if (is_string($subApp) && class_exists($subApp)) {
            $subApp = new $subApp();
        }
        if (!$subApp instanceof App) {
            throw new InvalidArgumentException("The sub-application must be an instance of App or a valid class name.");
        }
        $this->subApps[$prefix] = $subApp;
        return $this;
    }

    private function request(): Request
    {
        return new Request();
    }

    private function response(): Response
    {
        return new Response();
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

        foreach ($this->subApps as $prefix => $subApp) {
            if (strpos($requestUri, $prefix) === 0) {
                $subRequestUri = substr($requestUri, strlen($prefix));
                $subApp->run($requestMethod, $subRequestUri);
                return;
            }
        }

        if (isset($this->routes[$requestMethod][$requestUri])) {
            $callback = $this->routes[$requestMethod][$requestUri];
            if (is_callable($callback)) {
                call_user_func_array($callback, [$this->request(), $this->response()]);
            } elseif (is_array($callback)) {
                foreach ($callback as $func) {
                    if (is_callable($func)) {
                        call_user_func_array($func, [$this->request(), $this->response()]);
                    }
                }
            } else {
                http_response_code(500);
                echo "Internal Server Error: Unknown callback type";
                exit;
            }
        } else {
            http_response_code(404);
            echo "404 Not Found";
            exit;
        }
    }
}
