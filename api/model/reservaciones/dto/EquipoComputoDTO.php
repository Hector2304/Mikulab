<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class EquipoComputoDTO extends AbstractDTO
{
	protected $eqcoIdEquipo; // int
	protected $eqcoIdSalon; // int
	protected $eqcoNombre; // string
	protected $eqcoDescripcion; // string
	protected $eqcoNumeroInventario; // string
	protected $eqcoStatus; // string

	public function getEqcoIdEquipo()//: int
	{
		return $this->eqcoIdEquipo;
	}

	public function setEqcoIdEquipo(int $eqcoIdEquipo = null)
	{
		$this->eqcoIdEquipo = $eqcoIdEquipo;
	}

	public function getEqcoIdSalon()//: int
	{
		return $this->eqcoIdSalon;
	}

	public function setEqcoIdSalon(int $eqcoIdSalon = null)
	{
		$this->eqcoIdSalon = $eqcoIdSalon;
	}

	public function getEqcoNombre()//: string
	{
		return $this->eqcoNombre;
	}

	public function setEqcoNombre(string $eqcoNombre = null)
	{
		$this->eqcoNombre = $eqcoNombre;
	}

	public function getEqcoDescripcion()//: string
	{
		return $this->eqcoDescripcion;
	}

	public function setEqcoDescripcion(string $eqcoDescripcion = null)
	{
		$this->eqcoDescripcion = $eqcoDescripcion;
	}

	public function getEqcoNumeroInventario()//: string
	{
		return $this->eqcoNumeroInventario;
	}

	public function setEqcoNumeroInventario(string $eqcoNumeroInventario = null)
	{
		$this->eqcoNumeroInventario = $eqcoNumeroInventario;
	}

	public function getEqcoStatus()//: string
	{
		return $this->eqcoStatus;
	}

	public function setEqcoStatus(string $eqcoStatus = null)
	{
		$this->eqcoStatus = $eqcoStatus;
	}
}
