<?php namespace GCore\Leftaro\Middleware;

use GCore\Leftaro\Middleware\MiddlewareInterface;
use Psr\Http\Message\MessageInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

interface RequestResponseMiddlewareInterface extends MiddlewareInterface
{
    /**
     * Handle the middleware call for request and response approach
     *
     * @param  \Zend\Diactoros\Request   $request   Request instance
     * @param  \Zend\Diactoros\Response  $response  Response instance
     * @param  callable                  $next      Next callable Middleware
     *
     * @return \Psr\Http\Message\MessageInterface
     */
	public function __invoke(Request $request, Response $response, callable $next = null) : MessageInterface;
}