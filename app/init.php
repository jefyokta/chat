<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/index.php";
require_once __DIR__ . "/../database/index.php";
require_once __DIR__ . "/app/App.php";

foreach (glob(__DIR__ . "/app/apps/*.php") as $filename) {
    require_once $filename;
}
require_once __DIR__ . "/http/Request.php";
require_once __DIR__ . "/http/Response.php";
require_once __DIR__ . "/middleware/Auth.php";

foreach (glob(__DIR__ . "/models/*.php") as $filename) {
    require_once $filename;
}
