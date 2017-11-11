<?php namespace Leftaro\Core\Middleware;

use Exception;
use Leftaro\Core\Middleware\MiddlewareInterface;
use Leftaro\Core\Exception\NotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class SmartRouteMiddleware implements MiddlewareInterface
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
		$prefix = '/';
		$path = $request->getUri()->getPath();

		if (substr($path, 0, strlen($prefix)) !== $prefix)
		{
			throw new NotFoundException($request);
		}

		$path = substr($path, strlen($prefix));

		$controller = [];
		$params = [];
		$action = null;

		foreach (array_chunk(explode('/', $path), 2) as $piece)
		{
			$name = explode('-', $piece[0]);
			if ($name !== [])
			{
				$controller[] = implode('', array_map(function($value)
				{
					return ucfirst($value);
				}, $name));
			}
			else
			{
				$controller[] = ucfirst($piece[0]);
			}

			if (isset($piece[1]))
			{
				$piece[0] = str_replace('-', '_', $piece[0]);
				$params[$piece[0] . '_id'] = $piece[1];
				$action = 'resource';
			}
			else
			{
				$action = 'collection';
			}
		}

		foreach ($params as $key => $value)
		{
			$request = $request->withAttribute($key, $value);
		}

		$controller = 'Leftaro\\App\\Controller\\' . implode('\\', $controller) . 'Controller';
		$action = strtolower($request->getMethod()) . ucfirst($action) . 'Action';

		try
		{
			$controllerInstance = $this->container->make($controller);
		}
		catch (Exception $e)
		{
			throw new NotFoundException($request);
		}

		$response = $controllerInstance->before($request, $response);

		$response = $controllerInstance->$action($request, $response);

		$response = $controllerInstance->after($request, $response);

		return $next($request, $response);
	}
}