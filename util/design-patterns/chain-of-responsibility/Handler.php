<?php

interface Handler
{
	public function setNext(Handler $next = null);//: Handler
	public function handle();
}
