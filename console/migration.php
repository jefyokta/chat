<?php

use Swoole\Coroutine;

if (config('db.async')) {
    $coroutines[] = Coroutine::create(function () {
        try {
            foreach (glob(__DIR__ . '/../app/models/*.php') as $filename) {
                require_once $filename;
            }

            $coroutines = [];
            foreach (glob(__DIR__ . '/../app/models/*.php') as $filename) {
                $start = microtime(true);
                $class = basename($filename, '.php');
                $namespace = 'oktaa\\model\\' . $class;

                if (class_exists($namespace)) {
                    $c = new $namespace;
                    try {
                        $c::migrate();
                    } catch (\Throwable $th) {
                        Cli::error("Migration failed for " . $c->getTableName() . ": " . $th->getMessage() . "\n");
                    }



                    $end = microtime(true);
                    $time = number_format($end - $start, 4);
                    $name = $c->getTableName() ?? 'undeclaredtable';
                    Cli::info("Creating Table $name....... Took $time second(s)");
                } else {
                    Cli::error("Class $namespace does not exist.\n");
                }
            }



          
            fwrite(STDOUT, "\n\033[42m\033[37m Async Migration Completed \033[0m\n\n");
        } catch (\Throwable $th) {
            fwrite(STDERR, "\033[41m\033[37m error \033[0m: " . $th->getMessage() . "\n");
        }
    });
} else {
    try {
        foreach (glob(__DIR__ . '/../app/models/*.php') as $filename) {
            require_once $filename;
        }

        $coroutines = [];
        foreach (glob(__DIR__ . '/../app/models/*.php') as $filename) {
            $start = microtime(true);
            $class = basename($filename, '.php');
            $namespace = 'oktaa\\model\\' . $class;

            if (class_exists($namespace)) {
                $c = new $namespace;
                try {
                    $c::migrate();
                } catch (\Throwable $th) {
                    Cli::error("Migration failed for " . $c->getTableName() . ": " . $th->getMessage() . "\n");
                }



                $end = microtime(true);
                $time = number_format($end - $start, 4);
                $name = $c->getTableName() ?? 'undeclaredtable';
                Cli::info("Creating Table $name....... Took $time second(s)");
            } else {
                Cli::error("Class $namespace does not exist.\n");
            }
        }



        fwrite(STDOUT, "\n\033[42m\033[37m Migration Completed \033[0m\n\n");
    } catch (\Throwable $th) {
        fwrite(STDERR, "\033[41m\033[37m error \033[0m: " . $th->getMessage() . "\n");
    }
}
