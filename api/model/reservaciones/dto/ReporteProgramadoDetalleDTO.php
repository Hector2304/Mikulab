<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class ReporteProgramadoDetalleDTO extends AbstractDTO
{
	protected $repdIdDetalle; // int
	protected $repdIdReporteProgramado; // int
	protected $repdIdLaboratorio; // int
	protected $repdIdGrupo; // int
	protected $repdTipoGrupo; // string
	protected $repdIdHorario; // int
	protected $repdHoraApertura; // string
	protected $repdAsistencia; // bool

	public function getRepdIdDetalle()
	{
		return $this->repdIdDetalle;
	}

	public function setRepdIdDetalle(int $repdIdDetalle = null)
	{
		$this->repdIdDetalle = $repdIdDetalle;
	}

	public function getRepdIdReporteProgramado()
	{
		return $this->repdIdReporteProgramado;
	}

	public function setRepdIdReporteProgramado(int $repdIdReporteProgramado = null)
	{
		$this->repdIdReporteProgramado = $repdIdReporteProgramado;
	}

	public function getRepdIdLaboratorio()
	{
		return $this->repdIdLaboratorio;
	}

	public function setRepdIdLaboratorio(int $repdIdLaboratorio = null)
	{
		$this->repdIdLaboratorio = $repdIdLaboratorio;
	}

	public function getRepdIdGrupo()
	{
		return $this->repdIdGrupo;
	}

	public function setRepdIdGrupo(int $repdIdGrupo = null)
	{
		$this->repdIdGrupo = $repdIdGrupo;
	}

	public function getRepdTipoGrupo()
	{
		return $this->repdTipoGrupo;
	}

	public function setRepdTipoGrupo(string $repdTipoGrupo = null)
	{
		$this->repdTipoGrupo = $repdTipoGrupo;
	}

	public function getRepdIdHorario()
	{
		return $this->repdIdHorario;
	}

	public function setRepdIdHorario(int $repdIdHorario = null)
	{
		$this->repdIdHorario = $repdIdHorario;
	}

	public function getRepdHoraApertura()
	{
		return $this->repdHoraApertura;
	}

	public function setRepdHoraApertura(string $repdHoraApertura = null)
	{
		$this->repdHoraApertura = $repdHoraApertura;
	}

	public function getRepdAsistencia()
	{
		return $this->repdAsistencia;
	}

	public function setRepdAsistencia(bool $repdAsistencia = null)
	{
		$this->repdAsistencia = $repdAsistencia;
	}
}
