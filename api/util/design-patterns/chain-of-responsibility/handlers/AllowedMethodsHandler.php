<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/BaseHandler.php";

class AllowedMethodsHandler extends BaseHandler
{
	private $methods;

	public function __construct(array $methods = array())
	{
		$this->methods = $methods;
	}

	public function handle()
	{
		if (!in_array(RestCommons::getRequestMethod(), $this->methods)) {
			RestCommons::respondWithStatus(405);
			exit;
		}

		parent::handle();
	}
}
