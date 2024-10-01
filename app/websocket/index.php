<?php
require_once __DIR__ . "/../init.php";
require_once __DIR__ . "/Routing.php";
require_once __DIR__ . "/../Storage/Clients.php";

use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use oktaa\model\MessageModel;
use oktaa\model\UserModel;
use oktaa\Websocket\Routing;
use oktaa\Storage\ClientStorage;
use Swoole\Coroutine;

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
    Cli::info("client added");
});

/**
 * ========================
 * Server Websocket
 * ========================
 */

$server = require __DIR__."/../app/getServer.php";

$server->on('open', function (Server $server, Request $request) use ($clientStorage) {
    echo "Connection open: FD: {$request->fd}\n";
});

$server->on('message', function (Server $server, Frame $frame) use ($clientStorage, &$route) {
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

$server->on('close', function (Server $server, int $fd) use ($clientStorage) {
    echo "Connection closed: FD: {$fd}\n";
    $clients = $clientStorage->loadClients();
    removeClientByFd($fd, $clients, $clientStorage);
});





/**
 * 
 * 
 * 
 * ================
 * Functions
 * ================
 * 
 * 
 * 
 * 
 */
function addClient(int $fd, int $userId, array &$clients, ClientStorage $clientStorage)
{
    if (!isset($clients[$userId])) {
        $clients[$userId] = [];
    }

    if (!in_array($fd, $clients[$userId])) {
        $clients[$userId][] = $fd;
        Cli::info("FD $fd added for user ID: $userId");
    }

    Cli::info("Current clients: " . print_r($clients, true));
    $clientStorage->saveClients($clients);
}


function removeClient(int $fd, int $userId, array &$clients, ClientStorage $storage)
{

    if (isset($clients[$userId])) {
        if (($key = array_search($fd, $clients[$userId])) !== false) {
            unset($clients[$userId][$key]);

            if (empty($clients[$userId])) {
                unset($clients[$userId]);
            }

            $storage->saveClients($clients);

            Cli::info("FD $fd removed for user ID: $userId");
        }
    }
}
function removeClientByfd(int $fd, array &$clients, ClientStorage $storage)
{
    foreach ($clients as $userId => &$clientList) {
        if (($key = array_search($fd, $clientList)) !== false) {
            unset($clientList[$key]);


            if (empty($clientList)) {
                unset($clients[$userId]);
            }

            $storage->saveClients($clients);
            Cli::info("FD $fd removed for user ID: $userId");
            break;
        }
    }
}
function sendMessage(Server $server, int $userId, array $messageData, array &$clients, ClientStorage $storage)
{
    if (isset($clients[$userId])) {
        foreach ($clients[$userId] as $fd) {
            try {
                $result = $server->push($fd, json_encode($messageData));
                if ($result === false) {
                    removeClient($fd, $userId, $clients, $storage);
                } else {
                    Cli::info("Message sent to FD $fd for user ID: $userId");
                }
            } catch (\Throwable $e) {
                Cli::info("Failed to send message to FD $fd for user ID: $userId");
                removeClient($fd, $userId, $clients, $storage);
            }
        }
    } else {
        Cli::info("User ID $userId not online");
    }
}

$server->start();
