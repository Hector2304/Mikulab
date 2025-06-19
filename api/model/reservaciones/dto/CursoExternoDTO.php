<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/InstructorExternoDTO.php";

class CursoExternoDTO extends AbstractDTO
{
	protected $cuexIdCursoExterno; // int
	protected $cuexClave; // string
	protected $cuexNombre; // string
	protected $cuexAlumnosInscritos; // int
	protected $cuexIdInstructorExterno; // int
	protected $instructorExternoDTO; // InstructorExternoDTO

	public function getCuexIdCursoExterno() //: int
	{
		return $this->cuexIdCursoExterno;
	}

	public function setCuexIdCursoExterno(int $cuexIdCursoExterno = null)
	{
		$this->cuexIdCursoExterno = $cuexIdCursoExterno;
	}

	public function getCuexClave() //: string
	{
		return $this->cuexClave;
	}

	public function setCuexClave(string $cuexClave = null)
	{
		$this->cuexClave = $cuexClave;
	}

	public function getCuexNombre() //: string
	{
		return $this->cuexNombre;
	}

	public function setCuexNombre(string $cuexNombre = null)
	{
		$this->cuexNombre = $cuexNombre;
	}

	public function getCuexAlumnosInscritos() //: int
	{
		return $this->cuexAlumnosInscritos;
	}

	public function setCuexAlumnosInscritos(int $cuexAlumnosInscritos = null)
	{
		$this->cuexAlumnosInscritos = $cuexAlumnosInscritos;
	}

	public function getCuexIdInstructorExterno() //: int
	{
		return $this->cuexIdInstructorExterno;
	}

	public function setCuexIdInstructorExterno(int $cuexIdInstructorExterno = null)
	{
		$this->cuexIdInstructorExterno = $cuexIdInstructorExterno;
	}

	public function getInstructorExternoDTO() //: InstructorExternoDTO
	{
		return $this->instructorExternoDTO;
	}

	public function setInstructorExternoDTO(InstructorExternoDTO $instructorExternoDTO = null)
	{
		$this->instructorExternoDTO = $instructorExternoDTO;
	}

	public function getObjectVars()
	{
		$gov = get_object_vars($this);

		if ($this->instructorExternoDTO != null) {
			$gov['instructorExternoDTO'] = $this->instructorExternoDTO->getObjectVars();
		}

		return $gov;
	}
}
