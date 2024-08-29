<?php 

namespace oktaa\console;


class Console{

    public static function error(string $msg): void
    {
        fwrite(STDERR, "\033[41m error \033[0m " . $msg . "\n");
    }
    public static function info(string $msg): void
    {
        fwrite(STDOUT, "\n");
        fwrite(STDOUT, "\033[44m\033[30m info \033[0m \033[95m$msg\033[0m\n");
        fwrite(STDOUT, "\n");
    }
    public static function log(string $msg): void
    {
        fwrite(STDOUT, "\n");
        fwrite(STDOUT, "\033[43m\033[30m Log \033[0m \033[95m$msg\033[0m\n");
        fwrite(STDOUT, "\n");
    }

}