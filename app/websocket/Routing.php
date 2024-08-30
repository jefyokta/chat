<?php
namespace oktaa\websocket;
$clients = []; 
use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;
class Routing
{
    protected array $routes = [];

    public function path(string $action, callable $handler)
    {
        $this->routes[$action] = $handler;
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
