<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/BaseHandler.php";

class SessionHandler extends BaseHandler
{
	public function handle()
	{
		echo "Session\n";
		parent::handle();
	}
}
