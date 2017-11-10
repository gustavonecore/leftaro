<?php namespace Leftaro\App\Middleware;

use Leftaro\Core\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class LoggerMiddleware implements MiddlewareInterface
{
	/**
	 * @var  \Psr\Log\LoggerInterface  $logger  Logger instance
	 */
	protected $logger;

	/**
	 * Constructs the middleware
	 * @param  \Psr\Log\LoggerInterface  $logger  Logger instance
	 */
	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
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
		$this->logger->info("Request " . $request->getMethod() . " " . (string)$request->getUri() . " in: " . (string)$request->getBody());
		$this->logger->info("Response " . (string)$response->getBody());

		return $next($request, $response);
	}
}