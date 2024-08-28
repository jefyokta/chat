<?php

class Style
{



    public function box(string $boxcolor, string $text): string
    {
        return "$boxcolor$text\033[0m";
    }
}

function style(): Style
{
    return new Style();
}
class Cli
{
    public static function error(string $msg): void
    {
        fwrite(STDERR, "\033[41m error \033[0m " . $msg . "\n");
    }
    public static function warning(string $msg): void
    {
        fwrite(STDOUT, "\033[43m\033[30m warning \033[0m $msg \n");
    }
    public static function log(string $msg): void
    {
        fwrite(STDOUT, "\033[42m Log \033[0m " . $msg . "\n");
    }

    public static function info(string $msg): void
    {
        fwrite(STDOUT, "\033[44m\033[30m info \033[0m \033[95m$msg\033[0m\n");
    }

    public static function input(string $msg): void
    {
        fwrite(STDIN, " \033[93m$msg\033[0m\n");
    }

    public static function success(string $msg): void
    {
        fwrite(STDOUT, "\033[42m\033[30m success \033[0m \033[95m$msg\033[0m\n");
    }

    public static function listCommands(array $commands): void
    {
        foreach ($commands as $command => $description) {
            fwrite(STDOUT, "\033[92m`$command`\033[0m \n \033[95m$description\033[0m\n\n");
        }
    }
}




