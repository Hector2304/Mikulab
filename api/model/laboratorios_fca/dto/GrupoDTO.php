<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class GrupoDTO extends AbstractDTO
{
	protected $grupIdGrupo; // int
	protected $grupClave; // string
	protected $grupIdPeriodo; // string
	protected $grupAlumnosInscritos; // int
	protected $grupSemestre; // string
	protected $grupIdProfesor; // int

	protected $carrNombre; // string

	protected $asigIdAsignatura; // string
	protected $asigNombre; // string

	public function getGrupIdGrupo() //: int
	{
		return $this->grupIdGrupo;
	}

	public function setGrupIdGrupo(int $grupIdGrupo = null)
	{
		$this->grupIdGrupo = $grupIdGrupo;
	}

	public function getGrupClave() //: string
	{
		return $this->grupClave;
	}

	public function setGrupClave(string $grupClave = null)
	{
		$this->grupClave = $grupClave;
	}

	public function getGrupIdPeriodo() //: string
	{
		return $this->grupIdPeriodo;
	}

	public function setGrupIdPeriodo(string $grupIdPeriodo = null)
	{
		$this->grupIdPeriodo = $grupIdPeriodo;
	}

	public function getGrupAlumnosInscritos() //: int
	{
		return $this->grupAlumnosInscritos;
	}

	public function setGrupAlumnosInscritos(int $grupAlumnosInscritos = null)
	{
		$this->grupAlumnosInscritos = $grupAlumnosInscritos;
	}

	public function getGrupSemestre() //: string
	{
		return $this->grupSemestre;
	}

	public function setGrupSemestre(string $grupSemestre = null)
	{
		$this->grupSemestre = $grupSemestre;
	}

	public function getCarrNombre() //: string
	{
		return $this->carrNombre;
	}

	public function setCarrNombre(string $carrNombre = null)
	{
		$this->carrNombre = $carrNombre;
	}

	public function getAsigIdAsignatura() //: string
	{
		return $this->asigIdAsignatura;
	}

	public function setAsigIdAsignatura(string $asigIdAsignatura = null)
	{
		$this->asigIdAsignatura = $asigIdAsignatura;
	}

	public function getAsigNombre() //: string
	{
		return $this->asigNombre;
	}

	public function setAsigNombre(string $asigNombre = null)
	{
		$this->asigNombre = $asigNombre;
	}

	public function getGrupIdProfesor() //: int
	{
		return $this->grupIdProfesor;
	}

	public function setGrupIdProfesor(int $grupIdProfesor = null)
	{
		$this->grupIdProfesor = $grupIdProfesor;
	}
}
