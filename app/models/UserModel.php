<?php

namespace oktaa\model\Usermodel;

use oktaa\Database\Database;

class UserModel extends Database
{
    protected string $table = 'users';
    public $name = 'users';
    protected  array $definition = [
        "id" => "INT PRIMARY KEY AUTO_INCREMENT",
        "username" => "VARCHAR(255)",
        "password" => "VARCHAR(255)",
        "token" => "TEXT",
    ];
    protected array $fillable = [
        "username",
        "password"
    ];


    public  function UpdateToken(string $token, string $username): void
    {
        UserModel::update(['token' => $token], ['username' => $username])->run();
    }
    public function DeleteToken(string $username)
    {
        UserModel::update(['token' => null], ['username' => $username])->run();
    }
    public function VerifyToken() {}
}
