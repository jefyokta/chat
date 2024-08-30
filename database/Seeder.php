<?php

namespace oktaa\Seeder;

use oktaa\model\MessageModel;
use oktaa\model\Usermodel;
use Swoole\Coroutine;

class Seeder
{
    public function run()
    {
        // Coroutine::create(function () {
        $user = UserModel::insertMany([
            ["username" => "jefyokta", "password" => "123"],
            ["username" => "jefyokta2", "password" => "123"],
         
        ]);
        // $user =  UserModel::insert()->run(true);
        // $msg = MessageModel::insert(["message" => "hi", "from" => 2, "to" => 1, "id" => uniqid()])->run(true);
        // // var_dump($user);
        // // var_dump($msg);
        // // echo "async\n";
        // echo "Inserted " . ($msg) . " row(s)\n";
        // });
        // echo "non async\n";
    }
}
