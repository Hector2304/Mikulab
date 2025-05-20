<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/Handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/BaseHandler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/handlers/CORSHandler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/handlers/AllowedMethodsHandler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/handlers/AuthHandler.php";

class RESTEndpointClient
{
	private $firstHandler;
	private $lastHandler;

	public function __construct(array $allowedMethods = array(), array $allowedUserTypes = null)
	{
		$corsHandler = new CORSHandler(true); // false en producción
		$this->firstHandler = $corsHandler;

		$allowedMethodsHandler = new AllowedMethodsHandler($allowedMethods);

		$this->lastHandler = $corsHandler
			->setNext($allowedMethodsHandler);

		if ($allowedUserTypes != null) {
			$authHandler = new AuthHandler($allowedUserTypes);

			$this->lastHandler = $this->lastHandler
				->setNext($authHandler);
		}
	}

	public function execute(Handler $endpointHandler)
	{
		$this->lastHandler->setNext($endpointHandler);

		$this->firstHandler->handle();
	}
}
