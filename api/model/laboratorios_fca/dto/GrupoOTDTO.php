<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class GrupoOTDTO extends AbstractDTO
{
	protected $grotIdGrupoOt; // int
	protected $grotClave; // string
	protected $grotCupo; // int
	protected $grotIdProfesor; // int

	protected $mootClave; // string
	protected $mootNombre; // string

	public function getGrotIdGrupoOt() //: int
	{
		return $this->grotIdGrupoOt;
	}

	public function setGrotIdGrupoOt(int $grotIdGrupoOt = null)
	{
		$this->grotIdGrupoOt = $grotIdGrupoOt;
	}

	public function getGrotClave() //: string
	{
		return $this->grotClave;
	}

	public function setGrotClave(string $grotClave = null)
	{
		$this->grotClave = $grotClave;
	}

	public function getGrotCupo() //: int
	{
		return $this->grotCupo;
	}

	public function setGrotCupo(int $grotCupo = null)
	{
		$this->grotCupo = $grotCupo;
	}

	public function getMootClave() //: string
	{
		return $this->mootClave;
	}

	public function setMootClave(string $mootClave = null)
	{
		$this->mootClave = $mootClave;
	}

	public function getMootNombre() //: string
	{
		return $this->mootNombre;
	}

	public function setMootNombre(string $mootNombre = null)
	{
		$this->mootNombre = $mootNombre;
	}

	public function getGrotIdProfesor() //: int
	{
		return $this->grotIdProfesor;
	}

	public function setGrotIdProfesor(int $grotIdProfesor = null)
	{
		$this->grotIdProfesor = $grotIdProfesor;
	}
}
