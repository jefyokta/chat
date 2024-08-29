<?php

namespace oktaa\App;

use Auth;
use oktaa\App\App;
use oktaa\http\Request\Request;
use oktaa\http\Response\Response;
use oktaa\model\UserModel;

class ApiApp extends App
{
    public function __construct()
    {
        $this->get("/", function (Request $req, Response $res) {
            $res->Json(["req"=>$req->cookies]);
        });
        $this->get('/js', function (Request $request, Response $response) {
            $js = $request->query('n');
            $js ?? $response->Json(['msg' => "`n` query is required"])->status(402);
            if ($js === '') $response->Json(['msg' => "`n` query is required"])->status(402);
            if (file_exists(__DIR__ . "/../../../resources/js/$js")) {
                $response->File(__DIR__ . "/../../../resources/js/$js");
            } else {
                $response->status(404);
            }
        });
        $this->get(
            '/message',
            function (Request $req, Response $res) {
                $decoded = Auth::TokenVerify($req,$res,fn()=>"o");
                if (!$decoded) $res->redirectSameHost('/login');
                UserModel::getMyMessage($decoded['id']);
            }
        );
    }
}
