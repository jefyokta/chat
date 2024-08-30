<?php

namespace oktaa\middlewareSwoole;

use Cli;
use oktaa\model\Usermodel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Auth
{
    private static string  $accesstokenName =  "X-ChatAppAccessToken";
    private static string $tokenName = "X-ChatAppSess";

    private static function Model(): UserModel
    {
        return new UserModel();
    }
    public static function decodeToken($accesstoken)
    {
        if (!empty($accesstoken)) {
            try {
                $dec = JWT::decode($accesstoken, new Key(env('ACCESS_KEY'), 'HS512'));

                $data = ["username" => $dec->username, "id" => $dec->id];
                return $data;
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }
    public static function decodeRefreshToken($token)
    {
        if (!empty($token)) {
            try {
                $dec = JWT::decode($token, new Key(env('SERVERKEY'), 'HS512'));
                $IsInDb = self::Model()->VerifyToken($dec->username, $token);
                if (!$IsInDb) {
                    return false;
                }

                $data = ["username" => $dec->username, "id" => $dec->id];
                return $data;
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }

    public static function TokenVerify(Request $req, Response $res, callable $next)
    {
        $accesstoken = isset($req->cookie[self::$accesstokenName]) ? $req->cookie[self::$accesstokenName] : null;
        if (is_null($accesstoken)) {
            $res->redirect('/login');
            return;
        }

        $dec = JWT::decode($accesstoken, new Key(env('ACCESS_KEY'), 'HS512'));
        if ($dec) {
            $data = (object) ["username" => $dec->username, "id" => $dec->id];
            Cli::info("ok");
            $next($data);
        } else {

            try {
                $jwt = isset($req->cookie[self::$tokenName]) ? $req->cookie[self::$tokenName] : false;
                if (!$jwt) {
                    Cli::error(" no token");
                    $res->redirect("/login");
                }

                $dec = JWT::decode($jwt, new Key(env('SERVERKEY'), 'HS512'));
                if (!$jwt) {
                    $res->status(401);
                    # code...
                }
                $IsInDb = self::Model()->VerifyToken($dec->username, $jwt);
                if ($IsInDb) {

                    $newAccessToken = self::GenerateAccessToken(["id" => $dec->id, "username" => $dec->username]);
                    $res->cookie(self::$accesstokenName, $newAccessToken, time() + 3600, '/');
                    $data = (object) ["username" => $dec->username, "id" => $dec->id];
                    $next($data);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
                $res->redirect("/login");
            }
        }
    }

    public static function GenerateAccessToken($user): string
    {
        $accesstokenxp = time() + 3600;
        $payload = [
            "id" => $user->id,
            "username" => $user->username,
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

            $r = json_decode($req->rawContent(), true);

            // Validasi pengguna
            $user = UserModel::select("*")->where("username", '=', $r['username'])->first();

            if (isset($user->scalar) && !$user->scalar) {
                $res->header('content-type', 'application/json');
                $res->end(json_encode(['error' => 'Unauthorized']));
                return;
            }

            if ($user->password !== $r['password']) {
                $res->header('content-type', 'application/json');
                $res->end(json_encode(['error' => 'Invalid credentials']));
                return;
            }

            $payload = [
                "username" => $user->username,
                "id" => $user->id,
                "iat" => time(),
                "exp" => $tokenexp
            ];

            $token = JWT::encode($payload, env('SERVERKEY'), 'HS512');
            self::Model()->UpdateToken($token, $r['username']);
            $accesstoken = self::GenerateAccessToken($user);

            $res->cookie(self::$tokenName, $token, $tokenexp, '/', '', false, true);
            $res->cookie(self::$accesstokenName, $accesstoken, $accesstokenxp, '/', '', false, false);

            $res->redirect('/');
        } catch (\Throwable $th) {
            $res->header('content-type', 'application/json');
            $res->end(json_encode([
                'error' => $th->getMessage() . PHP_EOL . $th->getFile() . $th->getLine()
            ]));
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
$auth = (object) [
    "tokenVerify" => function (Request $req, Response $res, callable $next) {
        $model = new Usermodel();
        $accesstoken = isset($req->cookie['X-ChatAppAccessToken']) ? $req->cookie['X-ChatAppAccessToken'] : null;
        if (is_null($accesstoken)) {
            $res->redirect('/login');
            $res->end();
            return;
        }
        try {
            $dec = JWT::decode($accesstoken, new Key(env('ACCESS_KEY'), 'HS512'));
        } catch (\Throwable $th) {
           $res->redirect("/login");
        }
        if ($dec) {
            $data = (object) ["username" => $dec->username, "id" => $dec->id];
            Cli::info("ok");
            $next($data);
        } else {

            try {
                $jwt = isset($req->cookie["X-ChatAppSess"]) ? $req->cookie["X-ChatAppSess"] : null;
                if (is_null($jwt)) {
                    Cli::error(" no token");
                    $res->redirect("/login");
                }

                $dec = JWT::decode($jwt, new Key(env('SERVERKEY'), 'HS512'));
                if (!$jwt) {
                    $res->redirect("/login");
                }
                $IsInDb = $model->VerifyToken($dec->username, $jwt);
                if ($IsInDb) {

                    $newAccessToken = Auth::GenerateAccessToken(["id" => $dec->id, "username" => $dec->username]);
                    $res->cookie('X-ChatAppAccessToken', $newAccessToken, time() + 3600, '/');
                    $data = (object) ["username" => $dec->username, "id" => $dec->id];
                    $next($data);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
                $res->redirect("/login");
            }
        }
    },
    "guestVerify" => function (Request $req, Response $res, callable $next) {
        $acc =  Auth::decodeToken($req->cookie['X-ChatAppAccessToken'] ?? null);
        $ref = Auth::decodeRefreshToken($req->cookie['X-ChatAppSess'] ?? null);
        if ($acc) {
            $res->redirect("/");
        }
        if ($ref) {
            $res->redirect("/");
        }
        $next();
    },
    "logout" => function (Request $req, Response $res, callable $next) {
        $res->cookie("X-ChatAppSess", 'null', time() - 3600, '/',);
        $res->cookie("X-ChatAppAccessToken", 'null', time() - 3600, '/');
        $next();
    }
];
// $tokenVerify = function (Request $req, Response $res, callable $next) {
//     $accesstoken = isset($req->cookie['X-ChatAppAccessToken']) ? $req->cookie['X-ChatAppAccessToken'] : null;
//     if (is_null($accesstoken)) {
//         $res->redirect('/login');
//         return;
//     }

//     $dec = JWT::decode($accesstoken, new Key(env('ACCESS_KEY'), 'HS512'));
//     if ($dec) {
//         $data = (object) ["username" => $dec->username, "id" => $dec->id];
//         $next($data);
//     } else {

//         $jwt = isset($req->cookie["X-ChatAppSess"]) ? $req->cookie["X-ChatAppSess"] : null;
//         if (is_null($jwt)) {
//             Cli::error(" no token");
//             $res->redirect("/login");
//         }

//         $dec = JWT::decode($jwt, new Key(env('SERVERKEY'), 'HS512'));
//         if (!$dec) {
//             $res->redirect("/");
//             return;
//         }
//         $model = new Usermodel();

//         $IsInDb = $model->VerifyToken($dec->username, $jwt);
//         if ($IsInDb) {

//             $newAccessToken = Auth::GenerateAccessToken(["id" => $dec->id, "username" => $dec->username]);
//             $res->cookie('X-ChatAppAccessToken', $newAccessToken, time() + 3600, '/');
//             $data = (object) ["username" => $dec->username, "id" => $dec->id];
//             $next($data);
//         }
//     }
// };
