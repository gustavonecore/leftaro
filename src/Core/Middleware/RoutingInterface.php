<?php namespace Leftaro\Core\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

interface RoutingInterface
{
	/**
	 * Handle the middleware call for request and response approach
	 *
	 * @param  \Psr\Http\Message\RequestInterface    $request   Request instance
	 * @param  \Psr\Http\Message\ResponseInterface   $response  Response instance
	 * @param  \Psr\Container\ContainerInterface   $contsainer Container
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public static function getResponse(RequestInterface $request, ResponseInterface $response, ContainerInterface $container) : ResponseInterface;
}