<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class LaboratorioDTO extends AbstractDTO
{
	protected $saloIdSalon; // int
	protected $saloClave; // string
	protected $saloUbicacion; // string
	protected $saloCupo; // int

	public function getSaloIdSalon()//: int
	{
		return $this->saloIdSalon;
	}

	public function setSaloIdSalon(int $saloIdSalon = null)
	{
		$this->saloIdSalon = $saloIdSalon;
	}

	public function getSaloClave()//: string
	{
		return $this->saloClave;
	}

	public function setSaloClave(string $saloClave = null)
	{
		$this->saloClave = $saloClave;
	}

	public function getSaloUbicacion()//: string
	{
		return $this->saloUbicacion;
	}

	public function setSaloUbicacion(string $saloUbicacion = null)
	{
		$this->saloUbicacion = $saloUbicacion;
	}

	public function getSaloCupo()//: int
	{
		return $this->saloCupo;
	}

	public function setSaloCupo(int $saloCupo = null)
	{
		$this->saloCupo = $saloCupo;
	}
}
