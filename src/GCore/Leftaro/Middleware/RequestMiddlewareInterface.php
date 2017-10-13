<?php namespace GCore\Leftaro\Middleware;

use GCore\Leftaro\Middleware\MiddlewareInterface;
use Psr\Http\Message\MessageInterface;
use Zend\Diactoros\Request;

interface RequestMiddlewareInterface extends MiddlewareInterface
{
    /**
     * Handle the middleware call for request approach
     *
     * @param  \Zend\Diactoros\Request   $request  Request instance
     * @param  callable                  $next     Next callable Middleware
     *
     * @return \Psr\Http\Message\MessageInterface
     */
	public function __invoke(Request $request, callable $next = null) : MessageInterface;
}