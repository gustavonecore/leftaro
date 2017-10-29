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
		return $response;
	}

	public function setType(string $type)
	{
		$this->routeType = $type;
	}

	public function run(RequestInterface $request) : ResponseInterface
	{
		$response = $this->runMiddlewares($request);
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
		$this->addMiddlewares($this->container->get('config')->get('middlewares.before'));
		$this->middlewareQueue->add($this);
		$this->addMiddlewares($this->container->get('config')->get('middlewares.after'));
	}

	private function addMiddlewares(array $middlewareNames)
	{
		foreach ($middlewareNames as $middlewareClassName)
		{
			$reflector = new ReflectionClass($middlewareClassName);

			$middlewareInstance = $reflector->newInstanceArgs();

			if ($middlewareInstance instanceof MiddlewareInterface === false)
			{
				throw new InvalidArgumentException('Invalid middleware ' . $middleware);
			}

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
		return $next($request, $this->processRequest($request, $response));
	}
}