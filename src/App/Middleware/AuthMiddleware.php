<?php namespace Leftaro\App\Middleware;

use Leftaro\Core\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthMiddleware implements MiddlewareInterface
{
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
		$accessToken = null;

		if (isset($request->getQueryParams()['secure_token']) === true)
		{
			$accessToken = $request->getQueryParams()['secure_token'];
		}
		else if ($request->hasHeader('x-secure-token') === true)
		{
			$accessToken = $request->getHeader('x-secure-token')[0];
		}

		$request = $request->withAttribute('secure_token', $accessToken);

		return $next($request, $response);
	}
}