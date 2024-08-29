<?php

namespace oktaa\http\Response;

use Swoole\Http\Response as HttpResponse;

class Response
{
    public function __construct()
    {
        header('X-Powered-By: Oktaax');
    }

    public function Json(array $response, int $status = 200): Response
    {
        header("Content-Type: Application/json");
        echo json_encode($response, JSON_PRETTY_PRINT);
        return $this;
    }
    public function File(string $filepath): void
    {
        // $this->imgcheck($imgpath);
        header('Content-Type: ' . mime_content_type($filepath));
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    public function Download(string $file_url): void
    {
        $this->imgcheck($file_url);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_url) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_url));
        readfile($file_url);
        exit;
    }
    private function imgcheck(string $path)
    {
        if (!file_exists($path)) $this->Json(['not found'], 404);
    }
    public function status(int $status = 200)
    {
        header('X-Powered-By: Oktaax');
        http_response_code($status);
        exit;
    }
    public function render(string $views, array $data = []): Response
    {
        $data = $data;
        extract($data);
        require_once __DIR__ . "/../../resources/views/layouts/header.php";
        require_once __DIR__ . "/../../resources/views/$views.php";
        require_once __DIR__ . "/../../resources/views/layouts/footer.php";
        return $this;
    }

    public function redirect(string $location): void
    {
        header('X-Powered-By: Oktaax');
        header("Location: $location");
        exit;
    }
    public function redirectSameHost(?string $location): void
    {
        header('X-Powered-By: Oktaax');
        header("Location: http://" . config('app.host') . "$location");
        exit;
    }
}

class ResponseSwoole extends HttpResponse
{
    public function render(string $views, array $data = [])
    {
        ob_start();

        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        require_once __DIR__ . "/../../resources/views/layouts/header.php";
        require_once __DIR__ . "/../../resources/views/$views.php";
        require_once __DIR__ . "/../../resources/views/layouts/footer.php";

        $content = ob_get_clean();

        $this->header('Content-Type', 'text/html');
        $this->write($content);
        $this->end();
    }
    
}
