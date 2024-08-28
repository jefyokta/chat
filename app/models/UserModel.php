<?php

namespace oktaa\model;

use oktaa\Database\Database as Database;

class UserModel extends Database
{
    protected string $table = 'users';
    public $name = 'users';
    protected  array $definition = [
        "id" => "INT PRIMARY KEY AUTO_INCREMENT",
        "username" => "VARCHAR(255) UNIQUE",
        "password" => "VARCHAR(255)",
        "token" => "TEXT NULL",
    ];
    protected  $searchable = ['username'];
    protected string $findableColumn = 'username';
    protected array $fillable = [
        "username",
        "password"
    ];
    public static function getMyMessage($userid)
    {
        $users = self::selectDistinct('users.*')
            ->join('messages', 'users.id = messages.from OR users.id = messages.to')
            ->where('messages.from', '=', $userid)
            ->orWhere('messages.to', '=', $userid)
            ->andWhere('users.username', '!=', $userid)
            ->get();

        if (!is_array([$users])) {
            return [];
        }
        $filteredUsers = array_filter($users, function ($user) use ($userid) {
            return $user['id'] !== $userid;
        });
        return $filteredUsers ? $filteredUsers : [];
    }

    public  function UpdateToken(string $token, string $username): void
    {
        UserModel::update(['token' => $token], ['username' => $username])->run();
    }
    public function DeleteToken(string $username)
    {
        UserModel::update(['token' => null], ['username' => $username])->run();
    }
    public function VerifyToken($token, $username): bool
    {

        $user = UserModel::select("*")->where("token", "=", $token)->andWhere("username", "=", $username)->get();
        if (!is_array($user)) {
            return false;
        }
        if (count($user) < 1) {
            return false;
        }

        return true;
    }


}
