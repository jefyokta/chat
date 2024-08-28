<?php

namespace oktaa\App;

use oktaa\App\App;
use oktaa\http\Request\Request;
use oktaa\http\Response\Response;
use oktaa\model\UserModel;

class UserApp extends App
{
    public function __construct()
    {
        $this->get("/", function (Request $req, Response $res) {
            $res->Json(["hello"]);
        });
        $this->post('', function (Request $req, Response $res) {
            $user = $req->body ?? $res->status(400);
            if (!isset($user['username'], $user['password'])) {
                $res->status(400);
            }
            try {
                UserModel::insert(['username' => $user['username'], 'password' => $user['password']]);
            } catch (\Throwable $th) {
                $res->Json(ApiResponse([], 'Internal Server Error'))->status(500);
            }
        });
    }
}
