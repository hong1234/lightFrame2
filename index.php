<?php
$loader = require 'vendor/autoload.php';

//require 'lib/Framework/Core.php';
use Framework\Core;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Framework\Event\RequestEvent;

// $app = new Framework\Core();
$app = new Core();

// routes init ---

// function home() { return new Response('This is the home page'); }
// function about() { return new Response('This is the about page');}
// $app->map('/', 'home');
// $app->map('/about', 'about');

$app->map('/', function () { 
    return new Response('This is the home page');
});

$app->map('/about', function () { 
    return new Response('This is the about page');
});

$app->map('/hello/{name}', function ($name) {
	return new Response('Hello '.$name);
});

$app->map('/admin', function () {
    return new Response('Admin');
});

// register a handler for the event 'request' ---
$app->on(
    'request', // eventName
    function (RequestEvent $event) { // event-handler (callback function) with parameter $event
        // let's assume a proper check here
        if ('/admin' == $event->getRequest()->getPathInfo()) { 
            echo 'Access Denied!'; exit;
        }
    }
);

// handling request ---

$request = Request::createFromGlobals();

$response = $app->handle($request);

$response->send();
