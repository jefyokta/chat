<?php

use Swoole\Http\Response;

function render(Response $res, string $view, array $data = [])
{
    ob_start();

    extract($data);
    if (!file_exists(__DIR__ . "/../../resources/views/$view.php")) {
        $res->status(404);
        //    $res->end();
        //    exit;
    }

    include __DIR__ . "/../../resources/views/layouts/header.php";
    include __DIR__ . "/../../resources/views/$view.php";
    include __DIR__ . "/../../resources/views/layouts/footer.php";

    $content = ob_get_clean();

    $res->end($content);
}
