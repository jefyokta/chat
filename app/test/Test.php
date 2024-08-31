<?php

use Swoole\Coroutine;
use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Timer;

class Test
{
    public function run()
    {
        Coroutine::run(function () {

            go(function () {
                $i = 1;
                echo "helo {$i}x\n";
                Timer::tick(1000, function () use (&$i) {
                    $i += 1;
                    echo "helo ".$i."x".PHP_EOL;
                });
            });
            go(function(){
                echo "ntah".PHP_EOL;
            });
        });
    }
}
