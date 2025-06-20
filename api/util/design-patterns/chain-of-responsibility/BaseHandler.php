<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/Handler.php";

abstract class BaseHandler implements Handler
{
	private $next = null; // Handler

	public function setNext(Handler $next = null)//: Handler
	{
		$this->next = $next;

		return $this->next;
	}

	public function handle()
	{
		if ($this->next != null) {
			$this->next->handle();
		}
	}
}
