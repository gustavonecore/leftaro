<?php namespace Leftaro\App\Controller;

use Leftaro\Core\Controller\AbstractController;
use Zend\Diactoros\{Response, ServerRequest};

/**
 * Welcome controller
 */
class WelcomeController extends AbstractController
{
	/**
	 * Handle index endpoint
	 *
	 * @param ServerRequest $request
	 * @return Response
	 */
	public function textAction(ServerRequest $request, Response $response) : Response
	{
		return $this->text("Welcome to Leftaro Microframework!");
	}

	/**
	 * Handle json response
	 *
	 * @param ServerRequest $request
	 * @return Response
	 */
	public function jsonAction(ServerRequest $request, Response $response) : Response
	{
		return $this->json([
			'status' => true,
			'message' => 'Example json response',
		]);
	}

	/**
	 * Handle an html view request
	 *
	 * @param ServerRequest $request
	 * @return Response
	 */
	public function htmlAction(ServerRequest $request, Response $response) : Response
	{
		return $this->twig('welcome.twig', [
			'title' => 'Welcome to Leftaro Microframework',
			'description' => 'This is a simple framework in construction',
		]);
	}

	/**
	 * Handle an html view request
	 *
	 * @param ServerRequest $request
	 * @return Response
	 */
	public function htmlResourceAction(ServerRequest $request, Response $response) : Response
	{
		return $this->twig('welcome.twig', [
			'title' => 'Welcome to Leftaro Microframework',
			'description' => 'This is a simple framework in construction. Parameter id: ' . $request->getAttribute('id'),
		]);
	}
}