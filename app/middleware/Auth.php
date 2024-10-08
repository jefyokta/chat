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
    public static function decodeToken($accesstoken)
    {
        if ($accesstoken) {
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

    public static function TokenVerify(Request $req, Response $res, callable $next)
    {
        $accesstoken = isset($_COOKIE[self::$accesstokenName]) ? $_COOKIE[self::$accesstokenName] : null;
        if (!is_null($accesstoken)) {
            $res->redirect('/login');
        }


        if ($accesstoken) {
            try {
                $dec = JWT::decode($accesstoken, new Key(env('ACCESS_KEY'), 'HS512'));

                $data = ["username" => $dec->username, "id" => $dec->id];
                $next($data);
            } catch (\Exception $e) {


                try {
                    $jwt = isset($req->cookies[self::$tokenName]) ? $req->cookies[self::$tokenName] : false;
                    if (!$jwt) {
                        $res->redirect("/");
                    }

                    $dec = JWT::decode("", new Key(env('SERVERKEY'), 'HS512'));
                    $IsInDb = self::Model()->VerifyToken($dec->username, $jwt);
                    if ($IsInDb) {

                        $newAccessToken = self::GenerateAccessToken(["id" => $dec->id, "username" => $dec->username]);
                        setcookie(self::$accesstokenName, $newAccessToken, [
                            "expires" => time() + 3600,
                            "path" => "/",
                        ]);
                        $data = ["username" => $dec->username];
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    $res->redirect("/login");
                }
            }
        }
        $res->redirect("/login");
    }

    private static function GenerateAccessToken($user): string
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

            $r = $req->body;

            // $res->Json(ApiResponse([$r['username']], null))->status();

            $user =  UserModel::select("*")->where("username", '=', $r['username'])->first();

            if (isset($user->scalar) && !$user->scalar) {
                $res->Json([ApiResponse([], 'unautorized')])->status(401);
            }



            if ($user->password !== $r['password']) {
                $res->Json(ApiResponse([], 'poiohiuhiuhui'))->status(401);
            }

            $payload = [
                "username" => $user->username,
                "id" => $user->id,
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
            $res->Json(ApiResponse([], $th->getMessage().PHP_EOL.$th->getFile().$th->getLine()));
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
