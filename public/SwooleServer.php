<?php
require_once __DIR__ . "/../console/Console.php";
require_once __DIR__ . "/../app/init.php";
require_once __DIR__ . "/../app/app/SwooleApp.php";
require_once __DIR__ . "/../app/middleware/SwooleAuth.php";
require_once __DIR__ . "/../app/middleware/ErrorHandler.php";
require_once __DIR__ . "/../app/http/Responses.php";

use oktaa\console\Console;
use oktaa\Database\Interfaces\OrderByType;
use oktaa\middlewareSwoole\Auth as Au;
use oktaa\model\MessageModel;
use oktaa\model\UserModel;
use oktaa\Swoole\Error\ErrorHandler;
use Swoole\Http\Request;
use Swoole\Http\Response;
use oktaa\SwooleApp\App;
use Swoole\Coroutine;

$logMiddleware = function (Request $request, Response $response, $next) {
    $query = $request->get;
    $queryurl = '';
    if (!empty($query)) {
        foreach ($query as $q => $val) {
            $queryurl .= urlencode($q) . '=' . urlencode($val) . '&';
        }
        $queryurl = rtrim($queryurl, '&');
        $queryurl = '?' . $queryurl;
    }
    try {
        $next();
        $text = "[" . date("d/m/Y H:i") . "] " . $request->server['remote_addr'] . ":  " . $request->server['request_method'] . " " . $request->server['request_uri'] . $queryurl;
        Console::log($text);
        Coroutine::writeFile(__DIR__ . "/../storage/logs/logs", $text . PHP_EOL, FILE_APPEND);
    } catch (\Throwable $th) {
        // echo json_encode($th);
        $text = "[" . date("d/m/Y H:i") . "] " . $request->server['remote_addr'] . ":  " . $request->server['request_method'] . " " . $request->server['request_uri'] . $queryurl;
        Coroutine::writeFile(__DIR__ . "/../storage/logs/error_logs", $text . "  " . $th->getMessage() . PHP_EOL, FILE_APPEND);
        $file =  Coroutine::readFile($th->getFile()) ?: null;
        $filelines = explode("\n", $file) ?: null;
        $errorline = is_array($filelines) ? $filelines[$th->getLine() - 1] : "unknown";
        Console::error($th->getMessage());
        render($response, 'error/error', [
            "code" => $th->getCode(),
            "message" => $th->getMessage(),
            "line" => $th->getLine(),
            "trace" => $th->getTrace(),
            "file" => $th->getFile() ?: null,
            "errorline" => $errorline ?? null
        ]);
    }
};
$hosts = explode("//", config('app.url'))[1];
$hosts = explode(":", $hosts);
$port = $hosts[1];
$host = $hosts[0];

$app = new App($host, $port);


$app->use($logMiddleware);


$app->get("/login", function (Request $request, Response $response) {
    render($response, 'login');
}, [$auth->guestVerify]);

$app->get('/', function (Request $request, Response $response, $user) {
    $users =  UserModel::getMyMessage($user->id);
    render($response, 'index', ['username' => $user->username, 'users' => $users, "userid" => $user->id]);
}, [$auth->tokenVerify]);


$app->post('/login', function (Request $req, Response $res) {
    Au::Login($req, $res);
}, [$auth->guestVerify]);


$app->get("/register", function ($req, $res) {
    render($res, "register");
}, [$auth->guestVerify]);


$app->get("/token", function (Request $req, Response $res) {
    Au::TokenVerify($req, $res, function ($dec) use ($res) {
        $res->setHeader("Content-Type", "Application/json");
        $res->end(json_encode($dec));
    });
});
$app->get("/messages", function (Request $req, Response $res, $user) {
    $theirid = isset($req->get['with']) ? $req->get['with'] : null;
    if (empty($theirid)) {
        $res->setStatusCode(404, "not Found");
        $res->end();
        return $res;
    }
    $users =  UserModel::getMyMessage($user->id);

    $they = UserModel::select("*")->where("id", "=", $theirid)->first();
    if (!$they) {
        $res->setStatusCode(404, "not Found");
        $res->end();
    }
    $messages =  MessageModel::raw("SELECT * FROM messages
    WHERE (messages.from = ? OR messages.from = ?)
    AND (messages.to = ? OR messages.to = ?)
    ORDER BY created_at ASC", [$user->id, $theirid, $user->id, $theirid])->get();
    // MessageModel::select("*")
    //     ->where("messages.from", "=", $user->id)
    //     ->orWhere("messages.from", "=", $theirid)
    //     ->orWhere("messages.to", "=", $user->id)
    //     ->orWhere("messages.to", "=", $theirid)->OrderBy("created_at", OrderByType::ASC)->get();
    render($res, "index", [
        "userid" => $user->id,
        "messages" => $messages,
        "theirusername" => $they->username,
        "theirid" => $they->id,
        "username" => $user->username,
        "users" => $users
    ]);
}, [$auth->tokenVerify]);
$app->get("/search", function (Request $request, Response $res, $user) {
    $username = $request->get['username'] ?: false;
    if (!$username) {
        $res->setStatusCode(404);
        $res->end();
    }
    $users =  UserModel::getMyMessage($user->id);

    $search = UserModel::search($username);

    render($res, 'index', ['username' => $user->username, 'users' => $users, "userid" => $user->id, "search" => $search]);
}, [$auth->tokenVerify]);

$app->get("/api/js", function (Request $req, Response $res) {
    $js = $req->get['n'] ?: false;
    if (!$js) {
        $res->setStatusCode(404);
        $res->end();
    }
    if (strpos($js, '/') !== false) {
        $res->setStatusCode(404);
        $res->end();
    }
    $res->sendfile(ResourcePath("js/$js"));
});




$app->get("/api/css", function (Request $req, Response $res) {
    $css = $req->get['n'] ?: false;
    if (!$css) {
        $res->setStatusCode(404);
        $res->end();
    }
    if (strpos($css, '/') !== false) {
        $res->setStatusCode(404);
        $res->end();
    }
    $res->sendfile(ResourcePath("css/$css"));
});
$app->get("/api/img", function (Request $req, Response $res) {
    $img = $req->get['n'] ?: false;
    if (!$img) {
        $res->setStatusCode(404);
        $res->end();
    }
    if (strpos($img, '/') !== false) {
        $res->setStatusCode(404);
        $res->end();
    }
    $res->sendfile(ResourcePath("img/$img"));
});

$app->post("/register", function (Request $req, Response $res) {

    $body = json_decode($req->rawContent(), true);

    if (!isset($body['username'], $body['password'])) {
        $res->status(400);
        $res->end();
    } else {

        $user = UserModel::find($body['username']);
        if ($user) {
            $res->status(400);
            SendJson($res, ApiResponse([], "username has been used"));
        }
        $user =  UserModel::insert(["username" => $body['username'], "password" => $body['password']])->run(true);
        if ($user > 0) {
            $res->status(200);
            SendJson($res, ApiResponse(["oke"]));
        } else {
            SendJson($res, ApiResponse(["not ok"]));
        }
    }
}, [$auth->guestVerify]);

$app->delete("/logout", function ($req, $res) {
    SendJson($res, ApiResponse([], "null"));
}, [$auth->logout]);

$app->start();
