<?php

namespace oktaa\http\Request;

use Swoole\Http\Request as HttpRequest;

class Request
{
    public  $body = [];
    public  $params = '';
    public $header = [];
    public $query = [];
    public $cookies = [];
    public $useragent = '';
    public $ip = '';
    public  $files = [];
    public $data = [];

    public function __construct()
    {
        $this->data = $_POST;
        $data = file_get_contents("php://input");
        $data = json_decode($data, true);
        $this->body = $data ?? $_POST;
        // $this->body = (object) $this->body;
        $this->cookies = $_COOKIE ?? [];
        $this->ip = $_SERVER["REMOTE_ADDR"] ?? '';
        $this->useragent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $this->files = $_FILES ?? [];
        // $this->header = getallheaders() ? getallheaders() : $this->header;
        $this->params = $params ?? $this->params;


        $this->query = $_GET;
    }
    public function getBody(): array
    {
        $data = file_get_contents("php://input");
        return $data = json_decode($data, true) ?? [];
    }
    public function query(string $key): ?string
    {
        $query = $this->query[$key] ?? null;
        if (!is_null($query)) {
            return htmlspecialchars($query);
        }
        return null;
    }
    public function getData(): array
    {
        return $this->data ?? [];
    }
}
class RequestSwoole extends HttpRequest
{
    public $body = [];
    public $params = '';
    public $header = [];
    public $query = [];
    public $cookies = [];
    public $useragent = '';
    public $ip = '';
    public $files = [];
    public $data = [];

    public function __construct(string $params = '')
    {
        // parent::__construct(); 

        $this->data = $this->post ?? [];
        $this->body = $this->data;
        $this->cookies = $this->cookie ?? [];
        $this->ip = $this->server['REMOTE_ADDR'] ?? '';
        $this->useragent = $this->server['HTTP_USER_AGENT'] ?? 'unknown';
        $this->files = $this->files ?? [];
        $this->params = $params ?? $this->params;
        $this->query = $this->get ?? [];
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function query(string $key): ?string
    {
        $query = $this->query[$key] ?? null;
        return $query !== null ? htmlspecialchars($query) : null;
    }

 
}