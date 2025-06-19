<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

class ReporteProgramadoHistoricoDTO extends AbstractDTO
{
	protected $rephIdHistorico; // int
	protected $rephIdProgramado; // int
	protected $rephIdUsuario; // int
	protected $rephFechaHora; // string
	protected $rephAccion; // string

	protected $usuarioDTO; // UsuarioDTO

	public function getRephIdHistorico()
	{
		return $this->rephIdHistorico;
	}

	public function setRephIdHistorico(int $rephIdHistorico = null)
	{
		$this->rephIdHistorico = $rephIdHistorico;
	}

	public function getRephIdProgramado()
	{
		return $this->rephIdProgramado;
	}

	public function setRephIdProgramado(int $rephIdProgramado = null)
	{
		$this->rephIdProgramado = $rephIdProgramado;
	}

	public function getRephIdUsuario()
	{
		return $this->rephIdUsuario;
	}

	public function setRephIdUsuario(int $rephIdUsuario = null)
	{
		$this->rephIdUsuario = $rephIdUsuario;
	}

	public function getRephFechaHora()
	{
		return $this->rephFechaHora;
	}

	public function setRephFechaHora(string $rephFechaHora = null)
	{
		$this->rephFechaHora = $rephFechaHora;
	}

	public function getRephAccion()
	{
		return $this->rephAccion;
	}

	public function setRephAccion(string $rephAccion = null)
	{
		$this->rephAccion = $rephAccion;
	}

	public function getUsuarioDTO()
	{
		return $this->usuarioDTO;
	}

	public function setUsuarioDTO(UsuarioDTO $usuarioDTO = null)
	{
		$this->usuarioDTO = $usuarioDTO;
	}
}
