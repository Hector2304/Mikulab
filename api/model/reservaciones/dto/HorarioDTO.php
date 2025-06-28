<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class HorarioDTO extends AbstractDTO
{
	protected $horaIdHorario = "hora_id_horario"; // int
	protected $horaIni = "hora_ini"; // string
	protected $horaFin = "hora_fin"; // string
	protected $horaTipo = "hora_tipo"; // string

	public function getHoraIdHorario()
	{
		return $this->horaIdHorario;
	}

	public function setHoraIdHorario(int $horaIdHorario = null)
	{
		$this->horaIdHorario = $horaIdHorario;
	}

	public function getHoraIni()
	{
		return $this->horaIni;
	}

	public function setHoraIni(string $horaIni = null)
	{
		$this->horaIni = $horaIni;
	}

	public function getHoraFin()
	{
		return $this->horaFin;
	}

	public function setHoraFin(string $horaFin = null)
	{
		$this->horaFin = $horaFin;
	}

	public function getHoraTipo()
	{
		return $this->horaTipo;
	}

	public function setHoraTipo(string $horaTipo = null)
	{
		$this->horaTipo = $horaTipo;
	}
}
