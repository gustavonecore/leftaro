<?php namespace Leftaro\Core;

use Exception;
use InvalidArgumentException;
use FastRoute\RouteCollector;
use Leftaro\Core\Middleware\MiddlewareInterface;
use Leftaro\Core\Middleware\MiddlewareQueue;
use Leftaro\Core\Exception\MethodNotAllowedException;
use Leftaro\Core\Exception\NotFoundException;
use Leftaro\Core\Exception\LeftaroException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\TextResponse;
use ReflectionClass;

class Application
{
	const ROUTING_AUTO = 'auto';
	const ROUTING_FIXED = 'fixed';

	/**
	 * @var \Psr\Container\ContainerInterface  Container
	 */
	protected $container;

	/**
	 * @var \Leftaro\Core\Middleware\MiddlewareQueue  MiddlewareQueue
	 */
	protected $middlewareQueue;

	/**
	 * @var string Routing policy
	 */
	protected $routingPolicy;

	/**
	 * Constructs the main application
	 *
	 * @param \Psr\Container\ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;

		$this->middlewareQueue = new MiddlewareQueue;

		$this->setupMiddlewares();
	}

	public function setRoutingPoligy(string $type)
	{
		$this->routingPolicy = $type;
	}

	/**
	 * Run the full request processing
	 *
	 * @param \Psr\Http\Message\RequestInterface  $request  Request to be handled
	 * @return void
	 */
	public function run(RequestInterface $request)
	{
		try
		{
			$response = $this->runMiddlewares($request);
		}
		catch (Exception $e)
		{
			$response = $this->handleException($e, $request);
		}

		$this->renderResponse($response);
	}

	/**
     * Call relevant handler from the Container if needed. If it doesn't exist,
     * then just re-throw.
     *
     * @param  Exception $e
     * @param  ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws Exception if a handler is needed and not found
     */
    protected function handleException(Exception $e, RequestInterface $request) : ResponseInterface
    {
		if ($e instanceof MethodNotAllowedException)
		{
            return new TextResponse('Method not allowed', 405);
		}
		elseif ($e instanceof NotFoundException)
		{
			return new TextResponse('Resource not found for ' . $e->getRequest()->getUri()->getPAth(), 404);
		}
		elseif ($e instanceof LeftaroException)
		{
			// TODO
            return new TextResponse($e->getMessage(), 501);
		}
		else
		{
			$this->container->get('logger')->error('Unhandled exception. Detail: {0}', [$e->getMessage()]);

            return new TextResponse('Unhandled error', 500);
		}

        throw $e;
    }

	/**
	 * Renders an HTTP response.
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response Response to be rendered.
	 */
	protected function renderResponse(ResponseInterface $response)
	{
		$body = $response->getBody();

		if (!headers_sent())
		{
            // Headers
			foreach ($response->getHeaders() as $name => $values)
			{
				foreach ($values as $value)
				{
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
            // Set the status _after_ the headers, because of PHP's "helpful" behavior with location headers.
            // See https://github.com/slimphp/Slim/issues/1730
            // Status
            header(sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
		}

		http_response_code($response->getStatusCode());

		if ($body->isSeekable())
		{
			$body->rewind();
		}

		echo $body->getContents();
	}

	/**
	 * Configure the existing middlewares
	 */
	private function setupMiddlewares()
	{
		$this->addMiddlewares($this->container->get('config')->get('middlewares.after'));
		$this->addMiddlewares($this->container->get('config')->get('middlewares.before'));
	}

	private function addMiddlewares(array $middlewareNames)
	{
		foreach ($middlewareNames as $middlewareClassName)
		{
			$middlewareInstance = $this->container->make($middlewareClassName);

			$this->middlewareQueue->add($middlewareInstance);
		}
	}

	/**
	 * Run the middleware stack with the received request
	 * @param  ServerRequestInterface $request   Request
	 *
	 * @return ResponseInterface Response object
	 */
	private function runMiddlewares(RequestInterface $request) : ResponseInterface
	{
		return $this->middlewareQueue->process($request, new Response);
	}
}