<?php

namespace oktaa\SwooleApp;

require __DIR__."/../websocket/Routing.php";

require __DIR__."/../Storage/Clients.php";
require __DIR__."/getServer.php";
use Auth;
use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use oktaa\console\Console;
use oktaa\model\MessageModel;
use oktaa\model\UserModel;
use oktaa\Storage\ClientStorage;
use oktaa\websocket\Routing;
use Swoole\Coroutine;


class App
{
    protected $server;
    protected $routes = [];
    protected $middleware = [];

    public function __construct(string $host, int $port)
    {
        $this->server = new Server($host, $port);
        $this->server->on("start", function (Server $server) use ($host, $port) {
            Console::info("Server Running On http://$host:$port");
        });

        $this->server->on("request", function (Request $request, Response $response) {
            $this->handleRequest($request, $response);
        });

        $this->server->on('open', function (Server $server, Request $request) {
            Console::info("WebSocket connection opened: {$request->fd}");
        });

        $route = $this->webSocketRouting()["route"];
        $clientStorage = $this->webSocketRouting()["clientStorage"];
        $this->server->on('message', function (Server $server, Frame $frame) use ($clientStorage, &$route) {
            echo "Message received from FD: {$frame->fd}\n";
            $messageData = json_decode($frame->data, true);
        
            if ($messageData === null && json_last_error() !== JSON_ERROR_NONE) {
                $server->push($frame->fd, json_encode(['error' => 'Invalid JSON format', 'data' => []]));
                return;
            }
        
            if (isset($messageData['token'])) {
                $userid = Auth::decodeToken($messageData['token']);
                if (!$userid) {
                    $server->push($frame->fd, json_encode([
                        'type' => 'error',
                        'error' => 'Invalid token',
                        'data' => []
                    ]));
                    return;
                }
        
                $data = $messageData['data'] ?? [];
                $data['from'] = $userid['id'];
                $clients = $clientStorage->loadClients();
                addClient($frame->fd, $data['from'], $clients, $clientStorage);
                $route->run($server, $frame, $data);
            } else {
                $server->push($frame->fd, json_encode(WebsocketResponse([], 'error', 'invalid credential')));
            }
        });

        $this->server->on('close', function (Server $server, int $fd) {
            Console::info("WebSocket connection closed: {$fd}");
        });
    }
    public function route($method, string $path, callable $handler, array $middleware = [])
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }


    public function get(string $path, callable $handler, array $middleware = [])
    {
        $this->route('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable $handler, array $middleware = [])
    {
        $this->route('POST', $path, $handler, $middleware);
    }

    public function put(string $path, callable $handler, array $middleware = [])
    {
        $this->route('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, callable $handler, array $middleware = [])
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
        $path = filter_var($path, FILTER_SANITIZE_URL);
        // $path = rtrim($path,"/");


        $middlewareStack = array_merge($this->middleware, [
            function ($request, $response, $next) use ($method, $path) {
                $this->processRequest($request, $response, $method, $path, $next);
            }
        ]);
        // var_dump($middlewareStack);


        $this->runMiddlewareStack($middlewareStack, $request, $response);
    }

    protected function runMiddlewareStack(array $stack, Request $request, Response $response, $params = null)
    {
        $next = function ($params = null) use (&$stack, $request, $response, &$next) {
            if (!empty($stack)) {
                $middleware = array_shift($stack);
                if (is_callable($middleware)) {
                    $middleware($request, $response, $next, $params);
                } else {

                    Console::error("Middleware is not callable.");
                    $response->status(500);
                    $response->end("Internal Server Error");
                }
            }
        };

        $next($params);
    }

    protected function processRequest(Request $request, Response $response, string $method, string $path, $next)
    {
        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
            $handler = $route['handler'];
            $routeMiddleware = $route['middleware'] ?? [];

            $middlewareStack = array_merge($routeMiddleware, [
                function ($request, $response, $next, $params = null) use ($handler) {
                    $handler($request, $response, $params);
                }
            ]);

            $this->runMiddlewareStack($middlewareStack, $request, $response);
        } else {
            $response->status(404);
            $page = Coroutine::readFile(__DIR__ . "/../../resources/views/error/404.php");
            $response->end($page);
        }
    }

    public function start()
    {
        $this->server->start();
    }

    private function webSocketRouting()
    {
        $clientStorage = new ClientStorage();
        $route = new Routing();
        $route->path('message', function (Server $server, Frame $frame, array $data) use ($clientStorage, &$route) {
            if (isset($data['to'], $data['message'])) {
                $from = $data['from'];
                $to = $data['to'];
                $message = $data['message'];

                Coroutine::create(function () use ($from, $to, $message, $server, $frame, $clientStorage, &$route) {
                    $pdo = MessageModel::getPdo();
                    try {
                        $pdo->beginTransaction();
                        $id = uniqid();
                        MessageModel::insert([
                            "id" => $id,
                            'from' => $from,
                            'to' => $to,
                            'message' => $message
                        ])->run();
                        $pdo->commit();

                        $message = MessageModel::find($id);
                        $message->created_at = MessageTime($message->created_at);
                        $clients = $clientStorage->loadClients();
                        sendMessage($server, $to, WebsocketResponse($message, 'message'), $clients, $clientStorage);
                        sendMessage($server, $from, WebsocketResponse($message, 'message'), $clients, $clientStorage);

                        sendMessage($server, $to, WebsocketResponse(["from" => "user$from", "to" => "user$to"], 'new'), $clients, $clientStorage);
                        sendMessage($server, $from, WebsocketResponse(["from" => "user$from", "to" => "user$to"], 'new'), $clients, $clientStorage);
                    } catch (\Exception $e) {
                        $pdo->rollBack();
                        $server->push($frame->fd, json_encode(WebsocketResponse([], 'error', 'internal server error')));
                    }
                });
            } else {
                $server->push($frame->fd, json_encode(WebsocketResponse([], 'error', 'invalid message data')));
            }
        });

        $route->path('mymessage', function (Server $server, Frame $frame, array $data) {
            $id = $data['id'];
            UserModel::getMyMessage($id);
        });

        $route->path('auth', function (Server $server, Frame $frame, array $data) use ($clientStorage) {
            Console::info("client added");
        });

        return compact("route","clientStorage");
    }
}
