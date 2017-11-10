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
use ReflectionClass;

class Application
{
	const ROUTING_AUTO = 'auto';
	const ROUTING_FIXED = 'fixed';

	protected $container;

	protected $middlewareQueue;

	protected $routingPolicy;

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
	 * Entry point of the application for processing requests
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
    protected function handleException(Exception $e, ServerRequestInterface $request)
    {
		if ($e instanceof MethodNotAllowedException)
		{
            return new Response('Method not allowed', 405);
		}
		elseif ($e instanceof NotFoundException)
		{
			return new Response('Resource not found', 404);
		}
		elseif ($e instanceof LeftaroException)
		{
			// TODO
            return new Response($e->getMessage(), 501);
		}
		else
		{
            return new Response('Unhandled error', 500);
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

		$body = $response->getBody();

		if ($body->isSeekable())
		{
			$body->rewind();
		}

		$contentLength  = $response->getHeaderLine('Content-Length');

		if (!$contentLength)
		{
			$contentLength = $body->getSize();
		}

		if (isset($contentLength))
		{
			$amountToRead = $contentLength;

			while ($amountToRead > 0 && !$body->eof())
			{
				$data = $body->read(min($chunkSize, $amountToRead));
				echo $data;
				$amountToRead -= strlen($data);
				if (connection_status() != CONNECTION_NORMAL)
				{
					break;
				}
			}
		}
		else
		{
			while (!$body->eof())
			{
				echo $body->read($chunkSize);

				if (connection_status() != CONNECTION_NORMAL)
				{
					break;
				}
			}
		}
	}

	public function executeRoute(RequestInterface $request) : ResponseInterface
	{
		// Detect automatic routes and load the controller class on the fly here
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