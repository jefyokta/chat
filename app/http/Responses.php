<?php

use Swoole\Http\Response;

use Swoole\Coroutine;

function singleRender(Response $res, string $view, array $data)
{
    $baseDir = __DIR__ . "/../../resources/views/";
    $viewBasedir = $baseDir;

    if (!file_exists($baseDir . "$view.php")) {
        $res->status(404);
        $res->end("View not found");
        throw new InvalidArgumentException("Views Not Found");
        return;
    }

    extract($data);
    ob_start();
    require $viewBasedir . "$view.php";
    $output = ob_get_clean();
    if ($res->isWritable()) {
        $res->end($output);
    }
}
function render(Response $res, string $view, array $data = [])
{
    $baseDir = __DIR__ . "/../../resources/views/";
    $viewBasedir = $baseDir;

    if (!file_exists($baseDir . "$view.php")) {
        $res->status(404);
        $res->end("View not found");
        throw new InvalidArgumentException("Views Not Found");
        return;
    }


    extract($data);
    ob_start();

    require $baseDir . "layouts/header.php";
    require $viewBasedir . "$view.php";
    require $baseDir . "layouts/footer.php";

    $output = ob_get_clean();
    // Coroutine::writeFile("okta.html", $output);

    if ($res->isWritable()) {
        $res->end($output);
    }
}


function SendJson(Response $response, array $data)
{

    $response->setHeader("content-type", "application/json");
    if ($response->isWritable()) {

        $response->end(json_encode($data));
    } else {
        return;
    }
}
function preprocessTemplate($template)
{
    $res = preg_replace_callback(
        '/<x\s+(.*?)\s+x>/',
        function ($matches) {
            $varName = $matches[1];
            return "<?php echo htmlspecialchars($varName, ENT_QUOTES, 'UTF-8'); ?>";
        },
        $template
    );
    return $res;
}
