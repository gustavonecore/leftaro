<?php namespace Leftaro\Core;

use FastRoute\RouteCollector;
use Leftaro\Core\Middleware\MiddlewareInterface;
use Leftaro\Core\Middleware\MiddlewareQueue;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class Application implements MiddlewareInterface
{
	const ROUTE_TYPE_AUTO = 'reverse';
	const ROUTE_TYPE_FIXED = 'fixed';

	protected $container;

	protected $middlewareQueue;

	protected $routeType;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		$this->middlewareQueue = new MiddlewareQueue;
	}

	public function processRequest(RequestInterface $request, ResponseInterface $response) : ResponseInterface
	{
		if ($this->routeType === self::ROUTE_TYPE_AUTO)
		{
			return $this->executeRoute($request);
		}
		else
		{
			$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r)
			{
				/*
				$r->addRoute('GET', '/users', 'get_all_users_handler');
				// {id} must be a number (\d+)
				$r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
				// The /{title} suffix is optional
				$r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
				*/
			});
		}
	}

	public function setType(string $type)
	{
		$this->routeType = $type;
	}

	/**
	 * Method to use queue the application logic as a middleware
	 *
	 * @param  ServerRequestInterface $request   Request
	 * @param  ResponseInterface      $response  Response
	 * @param  callable|null          $next      Next callable
	 *
	 * @return ResponseInterface                 Response object
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
	{
		return $next($request, $this->processRequest($request, $response));
	}

	public function run(RequestInterface $request) : ResponseInterface
	{
		$this->routeType = !$this->routeType ? self::ROUTE_TYPE_AUTO : $this->routeType;

		$this->middlewareQueue->add(/*TODO Get before middlewares from somewhere*/);

		$this->middlewareQueue->add($this);

		$this->middlewareQueue->add(/*TODO Get after middlewares from somewhere*/);

		return $this->middlewareQueue->process($request, new Response);
	}

	public function executeRoute(RequestInterface $request) : ResponseInterface
	{
		// Detect automatic routes and load the controller class on the fly here
	}
}