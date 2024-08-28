<?php


require_once __DIR__ . "/../app/init.php";

use oktaa\http\Request\Request;
use oktaa\http\Response\Response;

use oktaa\model\UserModel;

use oktaa\App\App;
use oktaa\App\ApiApp;
use oktaa\App\UserApp;

$app = new App();



$app->get('/', function (Request $req, Response $res) {
    $res->render('index', ['username' => 'jefyokta']);
});

$app->path('/kon', UserApp::class);
$app->path('/api', ApiApp::class);



$app->get('/login', fn(Request $req, Response $res) => $res->render('login'));
$app->post('/login', function (Request $req, Response $res) {
    $islogin =  Auth::Login($req, $res);

    // $res->Json([$req]);


});
$app->delete('/logout', function ($rq, $res) {
    // Auth::LogOut($rq, $res);

});

$app->run();
