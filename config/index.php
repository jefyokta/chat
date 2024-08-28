<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}


function config(string $key)
{
    $keys = explode('.', $key) ?? '';
    $arr =  [
        "app" => [
            "dbhost" => env('APP_URL', "localhost"),
            "dbname" => "chat",
            "dbuser" => "root",
            "dbpass" => "root",
            "url" => env('APP_URL', 'localhost:8000'),
            "name" => env('APP_NAME', 'OktaApp'),
            "timezone" => env("APP_TIMEZONE", "Asia/Jakarta"),
            "host" => env('APP_URL', 'localhost:8000'),
        ],
        "db" => [
            "host" => env('DB_HOST', '127.0.0.1'),
            "name" => env('DB_NAME', 'okta'),
            "user" => env('DB_USERNAME', "root"),
            "password" => env('DB_PASSWORD', ""),
            "port" => env('DB_PORT', 3306),
            "connection" => env('DB_CONNECTION', 'mysql'),
            "async" => env("DB_ASYNC", false)
        ],
        "ws" => [
            "host" => env('WEBSOCKET_HOST', "0.0.0.0"),
            "port" => env('WEBSOCKET_PORT', 9502),
            "url" => env('WEBSOCKET_HOST', '0.0.0.0') . ":" . env('WEBSOCKET_PORT', 9502)
        ]
    ];

    return $arr[$keys[0]][$keys[1]];
}
function ApiResponse(array $data = [], ?string $error = null): array
{


    return [
        "error" => $error,
        "data" => $data
    ];
}
function WebsocketResponse(array $data = [], ?string $type = null, ?string $message = null): array
{

    return [
        'type' => $type,
        'data' => $data,
        'message' => $message
    ];
}
function ResourcePath(string $res): string
{
    return __DIR__ . '/../resources/' . $res;
}
