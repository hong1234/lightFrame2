<?php
$loader = require 'vendor/autoload.php';

//require 'lib/Framework/Core.php';
use Framework\Core;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Framework\Event\RequestEvent;


// function home() { return new Response('This is the home page'); }
// function about() { return new Response('This is the about page');}

$request = Request::createFromGlobals();

// Our framework is now handling itself the request
$app = new Framework\Core();

//$app->map('/', 'home');
//$app->map('/about', 'about');

$app->map('/', function () { return new Response('This is the home page');});
$app->map('/about', function () { return new Response('This is the about page');});

$app->map('/hello/{name}', function ($name) {
	return new Response('Hello '.$name);
});

$app->map('/admin', function () {
    return new Response('Admin');
});

$app->on('request', function (RequestEvent $event) {
    // let's assume a proper check here
    if ('/admin' == $event->getRequest()->getPathInfo()) {
        echo 'Access Denied!';
        exit;
    }
});

$response = $app->handle($request);
$response->send();
