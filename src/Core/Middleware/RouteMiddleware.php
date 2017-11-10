<?php namespace Leftaro\Core\Middleware;

use FastRoute\Dispatcher;
use Leftaro\Core\Middleware\MiddlewareInterface;
use Leftaro\Core\Exception\MethodNotAllowedException;
use Leftaro\Core\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class RouteMiddleware implements MiddlewareInterface
{
	/**
	 * @var \Psr\Container\ContainerInterface  Container
	 */
	protected $container;

	/**
	 * @var \FastRoute\Dispatcher  Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Creates the middleware
	 *
	 * @param \Psr\Container\ContainerInterface   $contsainer Container
	 * @param \FastRoute\Dispatcher               $dispatcher Dispatcher
	 */
	public function __construct(ContainerInterface $container, Dispatcher $dispatcher)
	{
		$this->container = $container;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Handle the middleware call for request and response approach
	 *
	 * @param  \Psr\Http\Message\RequestInterface    $request   Request instance
	 * @param  \Psr\Http\Message\ResponseInterface   $response  Response instance
	 * @param  callable                              $next      Next callable Middleware
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next = null) : ResponseInterface
	{
		$routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

		switch ($routeInfo[0])
		{
			case Dispatcher::NOT_FOUND:
				throw new NotFoundException($request);
			case Dispatcher::METHOD_NOT_ALLOWED:
				throw new MethodNotAllowedException($request);
			case Dispatcher::FOUND:

				list($controller, $action) = explode('::', $routeInfo[1]);

				$vars = $routeInfo[2];

				$controllerInstance = $this->container->make($controller);

				// This execution method 'action' is ugly AF, use a better way. Check the container options
				$response = $controllerInstance->$action($request, $response);

				break;
		}

		return $next($request, $response);
	}
}