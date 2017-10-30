<?php namespace Leftaro\Core;

use InvalidArgumentException;
use FastRoute\RouteCollector;
use Leftaro\Core\Middleware\MiddlewareInterface;
use Leftaro\Core\Middleware\MiddlewareQueue;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use ReflectionClass;

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

		$this->setupMiddlewares();
	}

	public function processRequest(RequestInterface $request, ResponseInterface $response) : ResponseInterface
	{
		$response->getBody()->write('I\'m the application..');

		return $response;
	}

	public function setType(string $type)
	{
		$this->routeType = $type;
	}

	public function run(RequestInterface $request)
	{
		$response = $this->runMiddlewares($request);

		$this->renderResponse($response);
	}

	/**
	 * Renders an HTTP response.
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response Response to be rendered.
	 */
	protected function renderResponse(ResponseInterface $response)
	{
		http_response_code($response->getStatusCode());

		foreach ($response->getHeaders() as $key => $values)
		{
			foreach ($values as $i => $value)
			{
				header("$key: $value", $i === 0);
			}
		}

		echo (string)$response->getBody();
	}

	public function executeRoute(RequestInterface $request) : ResponseInterface
	{
		// Detect automatic routes and load the controller class on the fly here
	}

	/**
	 * Configure the existing middlewares
	 */
	private function setupMiddlewares()
	{
		$this->addMiddlewares($this->container->get('config')->get('middlewares.after'));
		$this->middlewareQueue->add($this);
		$this->addMiddlewares($this->container->get('config')->get('middlewares.before'));
	}

	private function addMiddlewares(array $middlewareNames)
	{
		foreach ($middlewareNames as $middlewareClassName)
		{
			$middlewareInstance = $this->container->make($middlewareClassName);

			$this->middlewareQueue->add($middlewareInstance);
		}
	}

	/**
	 * Run the middleware stack with the received request
	 * @param  ServerRequestInterface $request   Request
	 *
	 * @return ResponseInterface Response object
	 */
	private function runMiddlewares(RequestInterface $request) : ResponseInterface
	{
		return $this->middlewareQueue->process($request, new Response);
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
	public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next = null) : ResponseInterface
	{
		$response = $this->processRequest($request, $response);

		return $next($request, $response);
	}
}