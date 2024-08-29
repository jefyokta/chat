<?php
require_once __DIR__ . "/../init.php";
require_once __DIR__ . "/Routing.php";

use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use oktaa\model\MessageModel;
use oktaa\model\UserModel;
use oktaa\Websocket\Routing;
use Swoole\Coroutine;
use Swoole\Timer;

$route = new Routing();
$route->path('message', function (Server $server, Frame $frame, array $data) {
    if (isset($data['to'], $data['message'])) {
        $from = $data['from'];
        $to = $data['to'];
        $message = $data['message'];

        try {
            MessageModel::insert([
                'from' => $from,
                'to' => $to,
                'message' => $message
            ])->run();
        } catch (\Exception $e) {
            $server->push($frame->fd, json_encode(WebsocketResponse([], 'error', 'internal server error')));
        }
    } else {
        $server->push($frame->fd, json_encode(WebsocketResponse([], 'error', 'invalid message data')));
    }
});
$route->path('mymessage', function (Server $server, Frame $frame, array $data) {
    $id = $data['id'];
    UserModel::getMyMessage($id);
});

$server = new Server(config('ws.host'), config('ws.port'));

$server->on('start', function () {
    echo "WebSocket Server started at ws://" . config('ws.url') . "\n";
});

$server->on('open', function (Server $server, Request $request) {
    echo "Connection open: {$request->fd}\n";
});

$server->on('message', function (Server $server, Frame $frame) use ($route) {
    $messageData = json_decode($frame->data, true);
    var_dump($frame->data);
    var_dump($server->getClientInfo($frame->fd));


    if ($messageData === null && json_last_error() !== JSON_ERROR_NONE) {
        $server->push($frame->fd, json_encode(['error' => 'Invalid JSON format', 'data' => []]));
        return;
    }

    if (isset($messageData['token'])) {
        $userid = Auth::decodeToken($messageData['token']);
        if (is_array($userid)) {
            $data = $messageData['data'] ?? [];
            $data['from'] = $userid['id'];

            $route->addClient($frame->fd, $userid['id']);
            $route->run($server, $frame, $data);
        } else {
            echo 'Invalid credential';
            $server->push($frame->fd, json_encode([
                'type' => 'error',
                'error' => 'Invalid token',
                'data' => []
            ]));
        }
    } else {
        echo 'No credential';
        $server->push($frame->fd, json_encode(WebsocketResponse([], 'error', 'invalid credential')));
    }
});

$server->on('close', function (Server $server, int $fd) use ($route) {
    foreach ($route->clients as $userId => $clientFd) {
        if ($clientFd === $fd) {
            $route->removeClient($userId);
            break;
        }
    }
});

$server->start();
