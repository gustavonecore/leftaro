<?php namespace GCore\Leftaro;

use GCore\Leftaro\Middleware\MiddlewareInterface;
use GCore\Leftaro\Middleware\MiddlewareQueue;
use GCore\Leftaro\Middleware\RequestMiddlewareInterface;
use GCore\Leftaro\Middleware\RequestResponseMiddlewareInterface;
use Pimple\Container;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

class Application
{
    protected $container;

    protected $beforeMiddleware;

    protected $afterMiddleware;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->beforeMiddleware = new MiddlewareQueue;
        $this->afterMiddleware = new MiddlewareQueue;
    }

    public function run(Request $request) : Response
    {

    }

    public function addBeforeMiddleware(MiddlewareInterface $middleware)
    {
        if (!($middleware instanceof RequestMiddlewareInterface))
        {
            throw new RuntimeException('Invalid Middleware. You must provide an instance of RequestMiddlewareInterface');
        }

        $this->beforeMiddleware->add($middleware);
    }

    public function addAfterMiddleware(MiddlewareInterface $middleware)
    {
        if (!($middleware instanceof RequestResponseMiddlewareInterface))
        {
            throw new RuntimeException('Invalid Middleware. You must provide an instance of RequestResponseMiddlewareInterface');
        }

        $this->afterMiddleware->add($middleware);
    }
}