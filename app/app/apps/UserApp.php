<?php

namespace oktaa\App;

use oktaa\App\App;

class UserApp extends App
{
    public function __construct()
    {
        $this->get("/", function ($req, $res) {
            $res->Json(["hello"]);
    });
    }
}
