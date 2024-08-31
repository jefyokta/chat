<?php

namespace oktaa\Swoole\Error;
use Swoole\Coroutine;
use Swoole\Http\Response;
class ErrorHandler
{


    public function __invoke($request, $response, $next)
    {
        try {
            $next();
        } catch (\Exception $e) {
            render($response, 'error/error', ["error" => $e->getCode(), "message" => $e->getMessage(), "line" => $e->getLine()]);
        }
    }
    protected function renderErrorView($statusCode, Response $response)
    {
        switch ($statusCode) {
            case 404:
                $page = Coroutine::readFile(__DIR__ . "/../../resources/views/error/404.php");
                break;
            case 500:
                $page = Coroutine::readFile(__DIR__ . "/../../resources/views/error/500.php");
                break;
            default:
                $page = Coroutine::readFile(__DIR__ . "/../../resources/views/error/generic_error.php");
                break;
        }

        if (!$page) {
            $page = "<h1>Error $statusCode</h1><p>Oops Something went wrong!</p>";
        }

        $response->end($page);
    }
}
