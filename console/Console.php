<?php

namespace oktaa\console;


class Console
{

    public static function error($msg): void
    {
        fwrite(STDERR, "\033[41m error \033[0m " . $msg . "\n");
    }
    public static function info($msg): void
    {
        fwrite(STDOUT, "\n");
        fwrite(STDOUT, "\033[44m\033[30m info \033[0m \033[95m$msg\033[0m\n");
        fwrite(STDOUT, "\n");
    }
    public static function log($msg): void
    {
        fwrite(STDOUT, "\n");
        fwrite(STDOUT, "\033[43m\033[30m Log \033[0m \033[95m$msg\033[0m\n");
        fwrite(STDOUT, "\n");
    }

    public static function warning($msg): void
    {
        fwrite(STDOUT, "\n");
        fwrite(STDOUT, "\033[43m\033[30m Log \033[0m \033[95m$msg\033[0m\n");
        fwrite(STDOUT, "\n");
    }
}
