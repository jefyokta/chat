<?php

use oktaa\model\Usermodel\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use oktaa\http\Request\Request;
use oktaa\http\Response\Response;

class Auth
{
    private static function Model(): UserModel
    {
        return new UserModel();
    }

    public static function TokenVerify(Request $req, Response $res, callable $next): void
    {
        $jwt = $_COOKIE['X-ChatAppSess'] ?? null;
        if (!isset($jwt)) {
            $res->redirect(config('app.url') . "/login");
        }

        try {
            $dec = JWT::decode($jwt, new Key(config('SERVERKEY'), 'HS512'));
        } catch (\Exception $e) {
            $res->redirect(config('app.url') . "/login");
        }
        $IsInDb = self::Model()->VerifyToken($dec->username, $jwt);
        if (!$IsInDb) {
            $res->redirect(config('app.url') . "/login");
        }

        $data = ["username" => $dec->username];
        $next($data);
    }

    public static function Login($data)
    {

        $payload =
            [
                "username" => $data["username"],
                "iat" => time(),
                "exp" => time() + 3600 * 24
            ];
        $token =  JWT::encode($payload, env('SERVERKEY'), 'HS512');
        self::Model()->UpdateToken($data['username'], $token);
        setcookie("X-ChatAppSess", $token, [
            "expires" => time() + 3600 * 24,
            "httponly" => true,
            "path" => "/",
            // "samesite" => "Strict",
            // "secure" => true
        ]);
    }
    public static function LogOut(Request $req, Response $res)
    {
        self::TokenVerify($req, $res, function ($data) {
            setcookie('X-ChatAppSess', '', [
                "expires" => time() - 3600,
                "httponly" => true,
                "path" => "/",
               
            ]);
            self::Model()->DeleteToken($data['username']);
        });
        $res->redirect(config('app.url') . "/admin/login");
    }
}
