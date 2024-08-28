<?php

namespace oktaa\Websocket;

use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

class Routing
{
    protected array $routes = [];
    public array $clients = [];

    public function path(string $action, callable $handler)
    {
        $this->routes[$action] = $handler;
    }

    public function addClient(int $fd, int $userId)
    {
        $this->clients[$userId] = $fd;
    }

    public function removeClient(int $userId)
    {
        unset($this->clients[$userId]);
    }

    public function sendMessage(Server $server, int $userId, array $messageData)
    {
        if (isset($this->clients[$userId])) {
            $fd = $this->clients[$userId];
            $server->push($fd, json_encode($messageData));
        }
    }

    public function run(Server $server, Frame $frame, array $messageData)
    {
        $action = $messageData['action'] ?? null;
        if ($action && isset($this->routes[$action]) && is_callable($this->routes[$action])) {
            try {
                call_user_func($this->routes[$action], $server, $frame, $messageData);
            } catch (\Exception $e) {
                $server->push($frame->fd, json_encode(['error' => 'Server error: ' . $e->getMessage()]));
            }
        } else {
            $server->push($frame->fd, json_encode(['error' => 'Invalid action']));
        }
    }
    
}
