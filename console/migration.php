<?php

try {
    require_once __DIR__ . '/../config/index.php';
    require_once __DIR__ . '/../database/index.php';
    foreach (glob(__DIR__ . '/../app/models/*.php') as $filename) {
        require_once $filename;

        $class = basename($filename, '.php');
        $namespace = 'oktaa\\model\\' . $class . '\\' . $class;

        if (class_exists($namespace)) {
            $c = new $namespace;
            $c::migrate();
            $name = $c->name ?? 'undeclaredtable';
            fwrite(STDOUT, "\033[44m\033[30m info \033[0m: \033[95mcreating table $name....\033[0m\n");
        } else {
            fwrite(STDOUT, "\033[41m\033[37m error \033[0m: \033[95mclass $namespace does not exist.\033[0m\n");
        }
    }

    fwrite(STDOUT, "\n\033[42m\033[37m Migration Completed \033[0m\n");
} catch (\Throwable $th) {
    fwrite(STDERR, "\033[41m\033[37m error \033[0m: " . $th->getMessage() . "\n");
}
