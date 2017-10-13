<?php namespace GCore\Leftaro\Middleware;

use GCore\Leftaro\Middleware\MiddlewareInterface;
use GCore\Leftaro\Middleware\RequestMiddlewareInterface;
use GCore\Leftaro\Middleware\RequestResponseMiddlewareInterface;
use Psr\Http\Message\MessageInterface;
use SplQueue;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

/**
 * Class to handle the middlewares execution as a FIFO queue
 */
class MiddlewareList
{
    /**
     * @var  \SplQueue  Queue to store the middlewares
     */
    protected $queue;

    /**
     * Constructs the moddleware queue
     */
	public function __construct()
	{
		$this->queue = new SplQueue;
		$this->queue->setIteratorMode(SplQueue::IT_MODE_FIFO);
	}

    /**
     * Add a new middleware to the queue
     *
     * @param  \GCore\Leftaro\Middleware\MiddlewareInterface   $middleware  Middleware instance
     */
	public function add(MiddlewareInterface $middleware)
	{
		$next = $this->queue->count() === 0 ? null : $this->queue->dequeue();

		$this->queue->enqueue(function(Request $request, Response $response) use ($middleware, $next)
		{
            return ($middleware instanceof RequestMiddlewareInterface) ? $middleware($request, $next) : $middleware($request, $response, $next);
		});
	}

    /**
     * Process the queue
     *
     * @param  \Zend\Diactoros\Request    $request   Request instance
     * @param  \Zend\Diactoros\Response   $response  Response instance
     *
     * @return \Psr\Http\Message\MessageInterface
     */
	public function process(Request $request, Response $response = null) : MessageInterface
	{
        $middleware = $this->queue->dequeue();

        $response = $response === null ? new Response : $response;

        return ($middleware instanceof RequestMiddlewareInterface) ? $middleware($request) : $middleware($request, $response);
	}
}