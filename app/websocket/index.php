<?php
require_once __DIR__ . "/../init.php";

use oktaa\model\MessageModel\MessageModel;
use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use oktaa\model\Usermodel\UserModel;


$server = new Server(config('ws.host'), config('ws.port'));

$server->on('start', function () {
    echo "WebSocket Server started at ws://" . config('ws.url') . "\n";
});

$server->on('open', function (Server $server, Request $request) {
    go(function($request){
        echo "Connection opened: {$request->fd}\n";
    
    
        $userid = 1;
        $userid = 1;
        $users = UserModel::selectDistinct("users.*")
            ->join('messages', 'messages.from = users.id OR messages.to = users.id')
            ->where("messages.from", "=", $userid)
            ->orWhere("messages.to", "=", $userid)
            ->andWhere("users.id", "!=", $userid)
            ->get();
        foreach ($users as $user):
            echo $user;
        endforeach;
    });

});

$server->on('message', function (Server $server, Frame $frame) {
    $messageData = json_decode($frame->data, true);

    var_dump($messageData);

    if ($messageData) {
        try {
            if (!isset($messageData['from'], $messageData['to'], $messageData['message'])) {
                throw new InvalidArgumentException("Missing required fields");
            }

            MessageModel::insert([
                'from' => $messageData['from'],
                'to' => $messageData['to'],
                'message' => $messageData['message']
            ])->run();
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            var_dump($e->getMessage());
        } catch (\Throwable $th) {
            error_log("Unexpected error: " . $th->getMessage());
            var_dump($th->getMessage());
        }
    } else {
        var_dump("Invalid message data received");
    }
});


$server->on('close', function ($server, $fd) {
    echo "Connection closed: {$fd}\n";
});

$server->start();
