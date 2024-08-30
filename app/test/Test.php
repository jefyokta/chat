<?php

use oktaa\model\MessageModel;
use oktaa\model\UserModel;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client;
use Swoole\Coroutine\Scheduler;
use Swoole\Event;
use Swoole\Timer;

class Test
{

    public function run()
    {

        Coroutine::run(function(){
        //   $exist =  Coroutine::readFile(__DIR__."/okta.php") ?:false;
        //   echo $exist ? "iya" :"ngga";
        // var_dump($exist);
        
        });
    }
}
