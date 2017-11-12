<?php namespace Leftaro\Core\Middleware;

use Leftaro\Core\Middleware\MiddlewareInterface;
use Leftaro\Core\Exception\LeftaroException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class RouteMiddleware implements MiddlewareInterface, RoutingInterface
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
		$response = self::getResponse($request, $response, $this->container);

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
		try
		{
			$response = RouteFixedMiddleware::getResponse($request, $response, $container);
		}
		catch (LeftaroException $e)
		{
			$response = RouteSmartMiddleware::getResponse($request, $response, $container);
		}

		return $response;
	}
}