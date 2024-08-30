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
            // ["username" => "jefyokta", "password" => "123"],
            // ["username" => "jefyokta2", "password" => "123"],
            ["username" => "jefyokta3", "password" => "123"],
            ["username" => "jefyokta4", "password" => "123"],
            ["username" => "jefyokta5", "password" => "123"],
            ["username" => "jefyokta6", "password" => "123"],
            ["username" => "jefyokta7", "password" => "123"],
            ["username" => "jefyokta8", "password" => "123"],
            ["username" => "jefyokta9", "password" => "123"],
            ["username" => "jefyokta10", "password" => "123"],
            ["username" => "jefyokta11", "password" => "123"],
            ["username" => "jefyokta12", "password" => "123"],
            ["username" => "jefyokta13", "password" => "123"],
            ["username" => "jefyokta14", "password" => "123"],
            ["username" => "jefyokta15", "password" => "123"],
            ["username" => "jefyokta16", "password" => "123"],
            ["username" => "jefyokta17", "password" => "123"],
            ["username" => "jefyokta18", "password" => "123"],
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
