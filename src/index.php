<?php

require_once __DIR__ . '/../vendor/autoload.php';

interface MessageInterface
{
}

class Request implements MessageInterface
{
	public $body;
	public $token;
	protected  $isLogged;
}

class Response implements MessageInterface
{
	public $body;
}

class Application
{
	protected $before;
	protected $after;

	public function __construct(MiddlewareList $before, MiddlewareList $after)
	{
		$this->before = $before;
		$this->after = $after;
	}

	public function getResponse(Request $request)
	{
		$response = new Response();
		$response->body = "App body";
		return $response;
	}

	public function run(Request $request)
	{
        $result = $this->before->process($request);

		if ($result instanceof Response)
		{
			return $result;
        }

		return $this->after->process($request, $this->getResponse($result));
	}
}

interface MiddlewareInterface
{
}

interface RequestMiddlewareInterface extends MiddlewareInterface
{
	public function __invoke(Request $request, callable $next = null);
}

interface RequestResponseMiddlewareInterface extends MiddlewareInterface
{
	public function __invoke(Request $request, Response $response, callable $next = null);
}

class MiddlewareLog  implements RequestResponseMiddlewareInterface
{
	public function __invoke(Request $request, Response $response = null, callable $next = null)
	{
        $response->body .= "\nLogging some stuff";

		return $next !== null ? $next($request, $response) : $response;
	}
}

class MiddlewareEmail  implements RequestResponseMiddlewareInterface
{
	public function __invoke(Request $request, Response $response = null, callable $next = null)
	{
		if ($response)
		{
			$response->body .= "\nNotify by email to the admin";
        }

        return $next !== null ? $next($request, $response) : $response;
	}
}

class MiddlewareAuth  implements RequestMiddlewareInterface
{
	public function __invoke(Request $request, callable $next = null)
	{
		if (!$request->token)
		{
			$response = new Response;
			$response->body = 'Authenticated error';
			return $response;
		}

        return $next !== null ? $next($request) : $request;
	}
}

class MiddlewareList
{
	protected $queue;
	protected $currMiddleware;

	public function __construct()
	{
		$this->queue = new SplQueue;
		$this->queue->setIteratorMode(SplQueue::IT_MODE_FIFO);
	}

	public function add(MiddlewareInterface $middleware)
	{
		$next = $this->queue->count() === 0 ? null : $this->queue->dequeue();

		$this->queue->enqueue(function(Request $request, Response $response) use ($middleware, $next)
		{
            return ($middleware instanceof RequestMiddlewareInterface) ? $middleware($request, $next) : $middleware($request, $response, $next);
		});
	}

	public function process(Request $request, Response $response = null)
	{
        $middleware = $this->queue->dequeue();

        $response = $response === null ? new Response : $response;

        return ($middleware instanceof RequestMiddlewareInterface) ? $middleware($request) : $middleware($request, $response);
	}
}


$beforeMiddleware = new MiddlewareList;
$afterMiddleware = new MiddlewareList;

$beforeMiddleware->add(new MiddlewareAuth);
$afterMiddleware->add(new MiddlewareLog);
$afterMiddleware->add(new MiddlewareEmail);

$app = new Application($beforeMiddleware, $afterMiddleware);

$r1 = new Request;
$r1->token = 'token';
error_log('response: ' . print_r($app->run($r1), true));