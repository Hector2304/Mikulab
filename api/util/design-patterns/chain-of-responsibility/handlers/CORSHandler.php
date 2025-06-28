<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/BaseHandler.php";

class CORSHandler extends BaseHandler
{
	private $isDevelopment;

	public function __construct(bool $isDevelopment = false)
	{
		$this->isDevelopment = $isDevelopment;
	}

	public function handle()
	{
		if ($this->isDevelopment) {
			header('Access-Control-Allow-Origin: http://localhost:3000');
			header('Access-Control-Allow-Headers: Content-Type, Accept');
			header('Access-Control-Allow-Methods: GET,HEAD,POST,PUT,DELETE,CONNECT,OPTIONS,TRACE,PATCH');
			header('Access-Control-Allow-Credentials: true');

			if (RestCommons::getRequestMethod() == 'OPTIONS') {
				RestCommons::respondWithStatus(200);
				exit;
			}
		}

		parent::handle();
	}
}
