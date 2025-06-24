<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class GrupoPDTO extends AbstractDTO
{
	protected $gruoIdGrupoP; // int
	protected $gruoClave; // string
	protected $gruoIdPeriodo; // string
	protected $gruoAlumnosInscritos; // int
	protected $gruoIdProfesor; // int

	protected $asigIdAsignaturaP; // string
	protected $asigNombreP; // string

	protected $coorNombre; // string

	public function getGruoIdGrupoP() //: int
	{
		return $this->gruoIdGrupoP;
	}

	public function setGruoIdGrupoP(int $gruoIdGrupoP = null)
	{
		$this->gruoIdGrupoP = $gruoIdGrupoP;
	}

	public function getGruoClave() //: string
	{
		return $this->gruoClave;
	}

	public function setGruoClave(string $gruoClave = null)
	{
		$this->gruoClave = $gruoClave;
	}

	public function getIdPeriodo() //: string
	{
		return $this->gruoIdPeriodo;
	}

	public function setGruoIdPeriodo(string $gruoIdPeriodo = null)
	{
		$this->gruoIdPeriodo = $gruoIdPeriodo;
	}

	public function getGruoAlumnosInscritos() //: int
	{
		return $this->gruoAlumnosInscritos;
	}

	public function setGruoAlumnosInscritos(int $gruoAlumnosInscritos = null)
	{
		$this->gruoAlumnosInscritos = $gruoAlumnosInscritos;
	}

	public function getAsigIdAsignaturaP() //: string
	{
		return $this->asigIdAsignaturaP;
	}

	public function setAsigIdAsignaturaP(string $asigIdAsignaturaP = null)
	{
		$this->asigIdAsignaturaP = $asigIdAsignaturaP;
	}

	public function getAsigNombreP() //: string
	{
		return $this->asigNombreP;
	}

	public function setAsigNombreP(string $asigNombreP = null)
	{
		$this->asigNombreP = $asigNombreP;
	}

	public function getCoorNombre() //: string
	{
		return $this->coorNombre;
	}

	public function setCoorNombre(string $coorNombre = null)
	{
		$this->coorNombre = $coorNombre;
	}

	public function getGruoIdProfesor() //: int
	{
		return $this->gruoIdProfesor;
	}

	public function setGruoIdProfesor(int $gruoIdProfesor = null)
	{
		$this->gruoIdProfesor = $gruoIdProfesor;
	}
}
