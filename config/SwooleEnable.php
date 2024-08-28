<?php

use Swoole\Runtime;
if (env('db.async')) {
    Runtime::enableCoroutine(true,SWOOLE_HOOK_ALL);
}