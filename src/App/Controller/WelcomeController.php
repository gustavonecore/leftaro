<?php namespace Leftaro\App\Controller;

use Leftaro\Core\Controller\AbstractController;
use UnexpectedValueException;
use Zend\Diactoros\{Response, ServerRequest};

/**
 * Welcome controller
 */
class WelcomeController extends AbstractController
{
	public function __construct()
	{
	}

	public function indexAction(ServerRequest $request) : Response
	{
		return new Response("Welcome to Leftaro Microframework! wmnd wmd wmd wmnd nwmd wmn dmwn dmwn dmwne dmw", 200);
	}
}