<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class InstructorExternoDTO extends AbstractDTO
{
	protected $inexIdInstructorExterno; // int
	protected $inexNombre; // string
	protected $inexApaterno; // string
	protected $inexAmaterno; // string
	protected $inexTelefono; // string
	protected $inexCorreo; // string

	public function getInexIdInstructorExterno() //: int
	{
		return $this->inexIdInstructorExterno;
	}

	public function setInexIdInstructorExterno(int $inexIdInstructorExterno = null)
	{
		$this->inexIdInstructorExterno = $inexIdInstructorExterno;
	}

	public function getInexNombre() //: string
	{
		return $this->inexNombre;
	}

	public function setInexNombre(string $inexNombre = null)
	{
		$this->inexNombre = $inexNombre;
	}

	public function getInexApaterno() //: string
	{
		return $this->inexApaterno;
	}

	public function setInexApaterno(string $inexApaterno = null)
	{
		$this->inexApaterno = $inexApaterno;
	}

	public function getInexAmaterno() //: string
	{
		return $this->inexAmaterno;
	}

	public function setInexAmaterno(string $inexAmaterno = null)
	{
		$this->inexAmaterno = $inexAmaterno;
	}

	public function getInexTelefono() //: string
	{
		return $this->inexTelefono;
	}

	public function setInexTelefono(string $inexTelefono = null)
	{
		$this->inexTelefono = $inexTelefono;
	}

	public function getInexCorreo() //: string
	{
		return $this->inexCorreo;
	}

	public function setInexCorreo(string $inexCorreo = null)
	{
		$this->inexCorreo = $inexCorreo;
	}
}
