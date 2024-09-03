<?php

use oktaa\model\UserModel;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client;



class Test
{
    public function run()
    {
        Coroutine::run(function () {

            go(function () {
                echo "1\n";
                Coroutine::sleep(1);
                echo "2\n";
            });
            
            echo "1.5\n";
        });
    }
}
