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
    /**
     * @param string $path The path to match.
     * @param callable|callable[]|App $callback A single callback, array of callbacks, or an instance of the App class.
     */
    public function get($path, callable | array | App $callback)
    {
        $this->routes['GET'][$path] = $callback;
        return $this;
    }
    /**
     * @param string $path The path to match.
     * @param callable|callable[]|App $callback A single callback, array of callbacks, or an instance of the App class.
     */

    public function post($path, callable | array | App $callback)
    {
        $this->routes['POST'][$path] = $callback;
        return $this;
    }
    /**
     * @param string $path The path to match.
     * @param callable|callable[]|App $callback A single callback, array of callbacks, or an instance of the App class.
     */
    public function put($path, callable | array | App $callback)
    {
        $this->routes['PUT'][$path] = $callback;
        return $this;
    }
    /**
     * @param string $path The path to match.
     * @param callable|callable[]|App $callback A single callback, array of callbacks, or an instance of the App class.
     */
    public function delete($path, callable | array | App $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
        return $this;
    }

    /**
     * @param string $prefix The path to match.
     * @param App $subApp The sub-application as an instance of App .
     *
     * @throws InvalidArgumentException If the subApp is not an instance of App or a valid class name.
     * @return $this
     */

    public function path($prefix, $subApp)
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
    private function matchRoute($requestUri, $method)
    {
        foreach ($this->routes[$method] as $path => $callback) {
            $pattern = preg_replace('/\{[^\}]+\}/', '([^/]+)', $path);
            if (preg_match('#^' . $pattern . '$#', $requestUri, $matches)) {
                array_shift($matches);
                return [$callback, $matches];
            }
        }
        return [null, []];
    }

    public function run($requestMethod = null, $requestUri = null)
    {
        $requestMethod = $requestMethod ?? $_SERVER['REQUEST_METHOD'];
        $requestUri = $requestUri ?? (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/');

        foreach ($this->subApps as $prefix => $subApp) {
            if (strpos($requestUri, $prefix) === 0) {
                $subRequestUri = substr($requestUri, strlen($prefix));
                $subApp->run($requestMethod, $subRequestUri);
                return;
            }
        }

        list($callback, $params) = $this->matchRoute($requestUri, $requestMethod);
        if ($callback) {
            if (is_callable($callback)) {
                call_user_func_array($callback, array_merge([$this->request(), $this->response()], $params));
            } elseif (is_array($callback)) {
                foreach ($callback as $func) {
                    if (is_callable($func)) {
                        call_user_func_array($func, array_merge([$this->request(), $this->response()], $params));
                    }
                }
            } else {
                $this->response()->status(500);
                exit;
            }
        } else {
            $this->response()->status(404);
            exit;
        }
    }
}
