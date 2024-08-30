<?php

use Swoole\Http\Response;

use Swoole\Coroutine;

function render(Response $res, string $view, array $data = [])
{
    $baseDir = __DIR__ . "/../../resources/views/";

    if (!file_exists($baseDir . "$view.php")) {
        $res->status(404);
        $res->end("View not found");

        throw new InvalidArgumentException("Views Not Found");
        return;
    }

    $header = Coroutine::readFile($baseDir . "layouts/header.php") ?: '';
    $content = Coroutine::readFile($baseDir . "$view.php") ?: '';
    $footer = Coroutine::readFile($baseDir . "layouts/footer.php") ?: '';

    extract($data);

    ob_start();
    eval('?>' . $header);
    eval('?>' . $content);
    eval('?>' . $footer);
    $output = ob_get_clean();
    // Coroutine::writeFile('index.html',$output);

    if ($res->isWritable()) {
        $res->end($output);
    }
}


function SendJson(Response $response, array $data)
{

    $response->setHeader("content-type", "application/json");
    if ($response->isWritable()) {
        
        $response->end(json_encode($data));
    }
    else{
        return;
    }
}
