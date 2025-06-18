<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class HorarioBloqueadoDTO extends AbstractDTO
{
	protected $hoblFecha; // string
	protected $hoblIdHorario; // int
	protected $hoblIdLaboratorio; // int
	protected $hoblMotivo; // string

	public function getHoblFecha() //: string
	{
		return $this->hoblFecha;
	}

	public function setHoblFecha(string $hoblFecha = null)
	{
		$this->hoblFecha = $hoblFecha;
	}

	public function getHoblIdHorario() //: int
	{
		return $this->hoblIdHorario;
	}

	public function setHoblIdHorario(int $hoblIdHorario = null)
	{
		$this->hoblIdHorario = $hoblIdHorario;
	}

	public function getHoblIdLaboratorio() //: int
	{
		return $this->hoblIdLaboratorio;
	}

	public function setHoblIdLaboratorio(int $hoblIdLaboratorio = null)
	{
		$this->hoblIdLaboratorio = $hoblIdLaboratorio;
	}

	public function getHoblMotivo() //: string
	{
		return $this->hoblMotivo;
	}

	public function setHoblMotivo(string $hoblMotivo = null)
	{
		$this->hoblMotivo = $hoblMotivo;
	}
}
