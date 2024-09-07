<?php

use oktaa\model\UserModel;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client;



class Test
{
    public function run()
    {
        $mid = ['mid1', 'mid2'];

        $mids = array_merge($mid, [
            function () {
                echo "test";
            }
        ]);

        var_dump(array_shift($mids));
        var_dump($mids);
        
        
    }
}
