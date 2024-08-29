<?php
require_once __DIR__ . "/../console/Console.php";
require_once __DIR__ . "/../app/init.php";
require_once __DIR__ . "/../app/app/SwooleApp.php";
require_once __DIR__ . "/../app/middleware/SwooleAuth.php";
require_once __DIR__ . "/../app/middleware/ErrorHandler.php";
require_once __DIR__ . "/../app/http/Responses.php";

use oktaa\console\Console;
use oktaa\middlewareSwoole\Auth as Au;
use oktaa\Swoole\Error\ErrorHandler;
use Swoole\Http\Request;
use Swoole\Http\Response;
use oktaa\SwooleApp\App;
use Swoole\Coroutine;

$logMiddleware = function (Request $request, Response $response, $next) {
    try {
        $next();
        $text = "[" . date("d/m/Y H:i") . "] " . $request->server['remote_addr'] . ":  " . $request->server['request_method'] . " " . $request->server['request_uri'];
        Console::log($text);
        Coroutine::writeFile(__DIR__ . "/../storage/logs/logs", $text . PHP_EOL, FILE_APPEND);
    } catch (\Throwable $th) {
        $text = "[" . date("d/m/Y H:i") . "] " . $request->server['remote_addr'] . ":  " . $request->server['request_method'] . " " . $request->server['request_uri'];
        Coroutine::writeFile(__DIR__ . "/../storage/logs/error_logs", $text . "  " . $th->getMessage() . PHP_EOL, FILE_APPEND);
        $file =  Coroutine::readFile($th->getFile());
        $filelines = explode("\n", $file);
        $errorline = $filelines[$th->getLine() - 1];
        Console::error($th->getMessage());
        // var_dump($th->getCode());
        render($response, 'error/error', ["code" => $th->getCode(), "message" => $th->getMessage(), "line" => $th->getLine(), "trace" => $th->getTrace(), "file" => $th->getFile(), "errorline" => $errorline]);
    }
};

$hosts = explode("//", config('app.url'))[1];
$hosts = explode(":", $hosts);
$port = $hosts[1];
$host = $hosts[0];

$app = new App($host, $port);

$app->use($logMiddleware);
$app->use(new ErrorHandler());

$app->get('/', function (Request $request, Response $response) {
    render($response, 'index', ['username' => "jefyokta"]);
});

$app->get("/login", function (Request $request, Response $response) {
    render($response, 'login');
});
$app->post('/login', function (Request $req, Response $res) {
    Au::Login($req, $res);
});
$app->get("/register", function ($req, $res) {

    render($res, "register");
});
$app->get("/token", function (Request $req, Response $res) {
    Au::TokenVerify($req, $res, function ($dec) use ($res) {
        $dec->userid;
        $res->setHeader("content-type","application/json");
        $res->end(json_encode($dec));
    });
});

$app->start();
