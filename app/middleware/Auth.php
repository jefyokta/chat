<?php

use oktaa\model\Usermodel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use oktaa\http\Request\Request;
use oktaa\http\Response\Response;

class Auth
{
    private static string  $accesstokenName =  "X-ChatAppAccessToken";
    private static string $tokenName = "X-ChatAppSess";

    private static function Model(): UserModel
    {
        return new UserModel();
    }

    public static function TokenVerify($token)
    {
        $accesstoken = $_COOKIE[self::$accesstokenName] ?? $token;
        $jwt = $_COOKIE[self::$tokenName] ?? null;


        if ($accesstoken) {
            try {
                $dec = JWT::decode($accesstoken, new Key(env('ACCESS_KEY'), 'HS512'));

                $data = ["username" => $dec->username, "id" => $dec->id];
                // $next($data);
                return $data;
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        if ($jwt) {
            try {
                $dec = JWT::decode($jwt, new Key(env('SERVERKEY'), 'HS512'));
                $IsInDb = self::Model()->VerifyToken($dec->username, $jwt);
                if ($IsInDb) {

                    $newAccessToken = self::GenerateAccessToken(["id" => $dec->id, "username" => $dec->username]);


                    setcookie(self::$accesstokenName, $newAccessToken, [
                        "expires" => time() + 3600,
                        "path" => "/",
                    ]);

                    $data = ["username" => $dec->username];
                    // $next($data);
                    return $data;
                }
            } catch (\Exception $e) {
                echo $e->getMessage();

                // $res->redirect(config('app.url') . "/login");
                return false;
            }
        }
        // $res->redirect(config('app.url') . "/login");
        return false;
    }

    private static function GenerateAccessToken($user): string
    {
        $accesstokenxp = time() + 3600;
        $payload = [
            "id" => $user['id'],
            "username" => $user["username"],
            "id" => $user["id"],
            "iat" => time(),
            "exp" => $accesstokenxp
        ];
        return JWT::encode($payload, env('ACCESS_KEY'), 'HS512');
    }

    public static function Login(Request $req, Response $res)
    {
        try {
            $tokenexp = time() + 3600 * 24 * 3;
            $accesstokenxp = time() + 3600;

            $r = $req->body;
            $user =  UserModel::select("*")->where("username", '=', $r['username'])->first();
            
            if (!is_array($user)) {
                $res->status(401);
            }
            
            
            if (count($user) < 1) {
                $res->status(404);
            }
            if ($user['password'] !== $r['password']) $res->status(401);

            $payload = [
                "username" => $user["username"],
                "id" => $user["id"],
                "iat" => time(),
                "exp" => $tokenexp
            ];

            $token = JWT::encode($payload, env('SERVERKEY'), 'HS512');
            self::Model()->UpdateToken($token, $req->body['username']);
            $accesstoken = self::GenerateAccessToken($user);

            setcookie(self::$tokenName, $token, [
                "expires" => $tokenexp,
                "httponly" => true,
                "path" => "/",
            ]);

            setcookie(self::$accesstokenName, $accesstoken, [
                "expires" => $accesstokenxp,
                "path" => "/",
            ]);
            $res->redirectSameHost('/');
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    public static function LogOut(Request $req, Response $res)
    {
        self::TokenVerify($req, $res, function ($data) {
            setcookie(self::$tokenName, '', [
                "expires" => time() - 3600,
                "httponly" => true,
                "path" => "/",
            ]);
            self::Model()->DeleteToken($data['username']);
        });

        self::TokenVerify($req, $res, function () {
            setcookie(self::$accesstokenName, '', [
                "expires" => time() - 3600,
                "path" => "/",
            ]);
        });

        $res->redirect(config('app.url') . "/login");
    }
    public static function getMyAccessToken(): ?string
    {

        $accesstoken = $_COOKIE[self::$accesstokenName] ?? null;
        return $accesstoken;
    }
}
