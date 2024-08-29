<?php

namespace oktaa\App;

use Auth;
use oktaa\App\App;
use oktaa\http\Request\Request;
use oktaa\http\Response\Response;


class AuthApp extends App
{
    public function __construct()
    {
        $this->get("/", function (Request $req, Response $res) {
            $res->Json(["hello"]);
        });
        $this->get("/token", function (Request $req, Response $res) {
           Auth::TokenVerify($req, $res,function($dec) use($res){
            $res->Json(ApiResponse(["userid"=>$dec->id]));

           });
            
        });
    }
}
