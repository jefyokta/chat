<?php

namespace oktaa\SwooleApp;

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use oktaa\console\Console;

class App
{
    protected $server;
    protected $routes = [];
    protected $middleware = [];

    public function __construct($host, $port)
    {
        $this->server = new Server($host, $port);
        $this->server->on("start", function (Server $server) use ($host, $port) {
           Console::info("Server Running On http://$host:$port");
        });

        $this->server->on("request", function (Request $request, Response $response) {
            $this->handleRequest($request, $response);
        });
    }

    public function route($method, $path, $handler, $middleware = [])
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function get($path, $handler, $middleware = [])
    {
        $this->route('GET', $path, $handler, $middleware);
    }

    public function post($path, $handler, $middleware = [])
    {
        $this->route('POST', $path, $handler, $middleware);
    }
    
    public function put($path, $handler, $middleware = [])
    {
        $this->route('PUT', $path, $handler, $middleware);
    }
    
    public function delete($path, $handler, $middleware = [])
    {
        $this->route('DELETE', $path, $handler, $middleware);
    }
    
    public function use($middleware)
    {
        $this->middleware[] = $middleware;
    }

    protected function handleRequest(Request $request, Response $response)
    {
        $method = $request->server['request_method'];
        $path = $request->server['request_uri'];

        $middlewareStack = array_merge($this->middleware, [
            function ($request, $response) use ($method, $path) {
                $this->processRequest($request, $response, $method, $path);
            }
        ]);

        $this->runMiddlewareStack($middlewareStack, $request, $response);
    }

    protected function runMiddlewareStack(array $stack, Request $request, Response $response)
    {
        $next = function () use (&$stack, $request, $response, &$next) {
            if (!empty($stack)) {
                $middleware = array_shift($stack);
                if (is_callable($middleware)) {
                    $middleware($request, $response, $next); 
                } else {
                    Console::error("Middleware is not callable.");
                    $response->status(500);
                    $response->end("Internal Server Error");
                }
            }
        };
    
        $next(); 
    }
    

    protected function processRequest(Request $request, Response $response, $method, $path)
    {
        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
            $handler = $route['handler'];
            $routeMiddleware = $route['middleware'] ?? [];

            $middlewareStack = array_merge($routeMiddleware, [
                function ($request, $response) use ($handler) {
                    $handler($request, $response);
                }
            ]);

            $this->runMiddlewareStack($middlewareStack, $request, $response);
        } else {
            $response->status(404);
            $response->end("Not Found");
        }
    }

    public function start()
    {
        $this->server->start();
    }
}

