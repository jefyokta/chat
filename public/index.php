<?php


require_once __DIR__ . "/../app/init.php";

use oktaa\http\Request\Request;
use oktaa\http\Response\Response;
use oktaa\model\Usermodel\UserModel;
use oktaa\App\App;

use oktaa\App\UserApp;

$app = new App();



$app->get('/', function (Request $req, Response $res) {
    $res->render('index', ['username' => 'auth()->username']);
});

$app->use('/kon', UserApp::class);

$app->get('/login', fn(Request $req, Response $res) => $res->render('login'));
$app->post('/login', function (Request $req, Response $res) {

    $username = $req->data['username'];
    $password = $req->data['password'];
    $user =   UserModel::select('*')->where("username", '=', $username)->first();


    if (count($user) > 0) {
        if ($user['password'] === $password) {
            session_start();
            $_SESSION['login'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['id'] = $user['id'];
            $res->redirect('/');
            exit;
        }
    } else {
        return $res->Json(['msg' => 'invalid'])->status(401);
    }
});
$app->get('/logout', function ($rq, $res) {
    session_start();
    session_destroy();
});

$app->run();
