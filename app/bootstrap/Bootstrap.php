<?php

class Bootstrap
{
    public string $path = '/';
    public function __construct()
    {
        if (isset($_SERVER['PATH_INFO'])) {
            $this->path = $_SERVER['PATH_INFO'];
        }
    }
    public function getPath(){
        return $this->path;
    }
}
