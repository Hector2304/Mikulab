<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class ProfesorDTO extends AbstractDTO
{
	protected $profIdProfesor; // int
	protected $profNumUnam; // string
	protected $nombre; // string

	public function getProfIdProfesor()//: int
	{
		return $this->profIdProfesor;
	}

	public function setProfIdProfesor(int $profIdProfesor = null)
	{
		$this->profIdProfesor = $profIdProfesor;
	}

	public function getProfNumUnam()//: string
	{
		return $this->profNumUnam;
	}

	public function setProfNumUnam(string $profNumUnam = null)
	{
		$this->profNumUnam = $profNumUnam;
	}

	public function getNombre()//: string
	{
		return $this->nombre;
	}

	public function setNombre(string $nombre = null)
	{
		$this->nombre = $nombre;
	}
}
