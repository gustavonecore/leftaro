<?php namespace Leftaro\App\Controller\Smart\Discover;

use Leftaro\Core\Controller\AbstractController;
use Zend\Diactoros\{Response, ServerRequest};

/**
 * Resource controller
 */
class ResourceController extends AbstractController
{
	/**
	 * Handle index endpoint
	 *
	 * @param ServerRequest $request
	 * @return Response
	 */
	public function getResourceAction(ServerRequest $request, Response $response) : Response
	{
		return $this->html("<strong>Auto discovered endpoint</strong> for <br>" .
			"smart: " .  $request->getAttribute('smart_id') . '<br>' .
			"discover: " .  $request->getAttribute('discover_id') . '<br>' .
			"resource: " .  $request->getAttribute('resource_id'));
	}

	/**
	 * Handle json response
	 *
	 * @param ServerRequest $request
	 * @return Response
	 */
	public function getCollection(ServerRequest $request, Response $response) : Response
	{
		return $this->text("Auto discovered endpoint for collection resource");
	}
}