<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class ReporteProgramadoDTO extends AbstractDTO
{
	protected $reprIdReporteProgramado; // int
	protected $reprFecha; // string

	public function getReprIdReporteProgramado()
	{
		return $this->reprIdReporteProgramado;
	}

	public function setReprIdReporteProgramado(int $reprIdReporteProgramado = null)
	{
		$this->reprIdReporteProgramado = $reprIdReporteProgramado;
	}

	public function getReprFecha()
	{
		return $this->reprFecha;
	}

	public function setReprFecha(string $reprFecha = null)
	{
		$this->reprFecha = $reprFecha;
	}
}
