<?php namespace Leftaro\Core\Controller;

use DI\Container;
use Zend\Diactoros\{Response, ServerRequest};
use Zend\Diactoros\Response\{
	JsonResponse,
	TextResponse,
	HtmlResponse
};

/**
 * Class to handle controllers context
 */
class AbstractController
{
	protected $container;

	/**
	 * Constructs the base class
	 *
	 * @param \DI\Container $container  Contasiner
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * First function that is executed by the controller
	 *
	 * @param ServerRequest $request
	 * @param Response $response
	 * @return Response
	 */
	public function before(ServerRequest $request, Response $response) : Response
	{
		return $response;
	}

	/**
	 * Last function that is executed by the controller
	 *
	 * @param ServerRequest $request
	 * @param Response $response
	 * @return Response
	 */
	public function after(ServerRequest $request, Response $response) : Response
	{
		return $response;
	}

	/**
	 * Create a Json response
	 *
	 * @param array $data    Data to be processed
	 * @param int   $status  HTTP status code
	 * @return Response
	 */
	public function json(array $data, int $status = 200) : Response
	{
		return new JsonResponse($data, $status);
	}

	/**
	 * Create a text response
	 *
	 * @param string $data   Data to be processed
	 * @param int    $status HTTP status code
	 * @return Response
	 */
	public function text(string $data, int $status = 200) : Response
	{
		return new TextResponse($data, $status);
	}

	/**
	 * Render an html response
	 *
	 * @param string $data   Data to be processed
	 * @param int    $status HTTP status code
	 * @return Response
	 */
	public function html(string $data, int $status = 200) : Response
	{
		return new HtmlResponse($data, $status);
	}

	/**
	 * Create a rendered view using the twig template machine
	 *
	 * @param string $template  Name of the template
	 * @param array  $data      Data to merge into the view
	 * @return Response
	 */
	public function twig(string $template, array $data = []) : Response
	{
		return $this->html($this->container->get('twig')->render($template, $data));
	}
}