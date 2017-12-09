<?php namespace Leftaro\Core\Middleware;

use FastRoute\Dispatcher;
use Leftaro\Core\Middleware\MiddlewareInterface;
use Leftaro\Core\Exception\MethodNotAllowedException;
use Leftaro\Core\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;

class RouteFixedMiddleware implements MiddlewareInterface, RoutingInterface
{
	/**
	 * @var \Psr\Container\ContainerInterface  Container
	 */
	protected $container;

	/**
	 * Creates the middleware
	 *
	 * @param \Psr\Container\ContainerInterface   $contsainer Container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
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
		$response = self::getResponse($request, $response);

		return $next($request, $response);
	}

	/**
	 * Handle the middleware call for request and response approach
	 *
	 * @param  \Psr\Http\Message\RequestInterface    $request   Request instance
	 * @param  \Psr\Http\Message\ResponseInterface   $response  Response instance
	 * @param  \Psr\Container\ContainerInterface   $contsainer Container
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public static function getResponse(RequestInterface $request, ResponseInterface $response, ContainerInterface $container) : ResponseInterface
	{
		$rootPath = (new Uri($container->get('config')->get('host')))->getPath();

		$path = $request->getUri()->getPath();

		if ($rootPath !== '/')
		{
			$path = str_replace($rootPath, '', $request->getUri()->getPath());
			$path = $path !== '' ? '/' . $path : $path;
		}

		$routeInfo = $container->get('dispatcher')->dispatch($request->getMethod(), $path);

		switch ($routeInfo[0])
		{
			case Dispatcher::NOT_FOUND:
				throw new NotFoundException($request);
			case Dispatcher::METHOD_NOT_ALLOWED:
				throw new MethodNotAllowedException($request);
			case Dispatcher::FOUND:

				list($controller, $action) = explode('::', $routeInfo[1]);

				// Add url parameters as request attributes
				foreach ($routeInfo[2] as $key => $value)
				{
					$request = $request->withAttribute($key, $value);
				}

				$controllerInstance = $container->make($controller);

				$controllerInstance->setRequest($request);

				$response = $controllerInstance->before($request, $response);

				// This execution method 'action' is ugly AF, use a better way. Check the container options
				$response = $controllerInstance->$action($request, $response);

				$response = $controllerInstance->after($request, $response);

			break;
		}

		return $response;
	}
}