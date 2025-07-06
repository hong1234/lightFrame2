<?php
namespace Framework;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Framework\Event\RequestEvent;

class Core implements HttpKernelInterface {

	protected $routes = array();
    protected $dispatcher;

    public function __construct() {
        $this->routes = new RouteCollection();
        $this->dispatcher = new EventDispatcher();
    }

	// Associates an URL with a callback function
	public function map(string $path, callable $controller) {
		$route = new Route($path, array('controller' => $controller));
		$this->routes->add($path, $route);
	}

	// register handler (with dispatcher) for event(-name)
	public function on(string $event, callable $callback) {
        $this->dispatcher->addListener($event, $callback);
    }

	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        
		$event = new RequestEvent();
        $event->setRequest($request);
		// trigge handler for event 'request'
        $this->dispatcher->dispatch('request', $event);

		// create a context of the current request
		$context = new RequestContext();
		$context->fromRequest($request);

		// set matcher
		$matcher = new UrlMatcher($this->routes, $context);

        try {
			// let matching
			$attributes = $matcher->match($request->getPathInfo());

			$controller = $attributes['controller'];
            unset($attributes['controller']);
			// call handler function with parameters
			$response = call_user_func_array(
				$controller, 
				array_values($attributes) // php 8+
				// $attributes            // php 7+
			);

		} catch (ResourceNotFoundException $e) {
			$response = new Response('Not found!', Response::HTTP_NOT_FOUND);
		}
		return $response;
	}

}
