<?php
namespace oktaa\Swoole\Error;
class ErrorHandler
{


    public function __invoke($request, $response, $next)
    {
        try {
            $next();
        } catch (\Exception $e) {
            render($response, 'error/error', ["error" => $e->getCode(), "message" => $e->getMessage(),"line" => $e->getLine()]);
        }
    }
}
