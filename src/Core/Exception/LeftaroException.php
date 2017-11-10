<?php namespace Leftaro\Core\Exception;

use Exception;
use Psr\Http\Message\RequestInterface;

class LeftaroException extends Exception
{
	protected $request;

	public function __construct(RequestInterface $request)
	{
		$this->request = $request;
	}

	public function getRequest() : RequestInterface
	{
		return $this->request;
	}
}