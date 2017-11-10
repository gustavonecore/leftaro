<?php namespace Leftaro\Core\Exception;

use Leftaro\Core\Exception\LeftaroException;
use Psr\Http\Message\RequestInterface;

class NotFoundException extends LeftaroException
{
	public function __construct(RequestInterface $request)
	{
		parent::__construct($request);
	}

	public function getPath() : string
	{
		return $this->request->getUri()->getPath();
	}
}