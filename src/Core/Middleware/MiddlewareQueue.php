<?php namespace Leftaro\Core\Middleware;

use Leftaro\Core\Middleware\MiddlewareInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SplQueue;
use UnexpectedValueException;

/**
 * Class to handle the middlewares execution as a FIFO queue
 */
class MiddlewareQueue
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
	 * @param  \GCore\Leftaro\Core\Middleware\MiddlewareInterface   $middleware  Middleware instance
	 */
	public function add(MiddlewareInterface $middleware)
	{
		$next = $this->queue->count() === 0 ?
			function(RequestInterface $request, ResponseInterface $response) { return $response; } :
			$this->queue->dequeue();

		$this->queue->enqueue(function(RequestInterface $request, ResponseInterface $response) use ($middleware, $next)
		{
			$result = $middleware($request, $response, $next);

			if ($result instanceof ResponseInterface === false)
			{
				throw new UnexpectedValueException('Response must implement \Psr\Http\Message\ResponseInterface');
			}

			return $result;
		});
	}

	/**
	 * Process the queue
	 *
	 * @param  \Psr\Http\Message\RequestInterface    $request   Request instance
	 * @param  \Psr\Http\Message\ResponseInterface   $response  Response instance
	 *
	 * @return \Psr\Http\Message\MessageInterface
	 */
	public function process(RequestInterface $request, ResponseInterface $response = null) : MessageInterface
	{
		$middleware = $this->queue->dequeue();

		return $middleware($request, $response);
	}
}