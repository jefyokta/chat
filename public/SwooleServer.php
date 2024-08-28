<?php
require_once __DIR__ . "/../app/init.php";

use Swoole\Http\Server;


/**
 * Mixing Swoole HTTP server with Okta HTTP server
 */
$server = new Server('127.0.0.1', 8000);

$server->on('Request', function ($req, $res) {

});

$server->start();
