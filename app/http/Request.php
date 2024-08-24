<?php

namespace oktaa\http\Request;


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

    public function __construct(string $params = '')
    {
        $this->data = $_POST;
        $data = file_get_contents("php://input");
        $data = json_decode($data, true);
        $this->body = $data ?? $_POST;
        $this->cookies = $_COOKIE ?? [];
        $this->ip = $_SERVER["REMOTE_ADDR"] ?? '';
        $this->useragent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $this->files = $_FILES ?? [];
        $this->header = getallheaders() ? getallheaders() : $this->header;
        $this->params = $params ?? $this->params;


        $this->query = $_GET;
    }
    public function getBody(): array
    {
        $data = file_get_contents("php://input");
        return $data = json_decode($data, true) ?? [];
    }
    public function query(string $key): string
    {
        return htmlspecialchars($this->query[$key]);
    }
    public function getData(): array
    {
        return $this->data ?? [];
    }
}
