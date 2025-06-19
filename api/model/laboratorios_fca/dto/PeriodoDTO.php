<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class PeriodoDTO extends AbstractDTO
{
	protected $periIdPeriodo; // string

	public function getPeriIdPeriodo()
	{
		return $this->periIdPeriodo;
	}

	public function setPeriIdPeriodo(string $periIdPeriodo = null)
	{
		$this->periIdPeriodo = $periIdPeriodo;
	}
}
