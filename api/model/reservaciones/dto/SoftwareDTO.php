<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class SoftwareDTO extends AbstractDTO
{
	protected $softIdSoftware; // int
	protected $softNombre; // string

	public function getSoftIdSoftware()//: int
	{
		return $this->softIdSoftware;
	}

	public function setSoftIdSoftware(int $softIdSoftware = null)
	{
		$this->softIdSoftware = $softIdSoftware;
	}

	public function getSoftNombre()//: string
	{
		return $this->softNombre;
	}

	public function setSoftNombre(string $softNombre = null)
	{
		$this->softNombre = $softNombre;
	}
}
