<?php

use oktaa\Storage\ClientStorage;
use Swoole\WebSocket\Server;

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
?>