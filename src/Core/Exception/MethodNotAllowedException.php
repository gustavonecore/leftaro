<?php namespace Leftaro\Core\Exception;

use Leftaro\Core\Exception\LeftaroException;
use Psr\Http\Message\RequestInterface;

class MethodNotAllowedException extends LeftaroException
{
	protected $request;

	protected $allowedMethods;

	public function __construct(RequestInterface $request, array $allowedMethods = [])
	{
		parent::__construct($request);

		$this->allowedMethods = $allowedMethods;
	}

	public function getAllowedMethods() : array
	{
		return $this->allowedMethods;
	}
}