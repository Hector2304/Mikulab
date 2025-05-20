<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

class ReporteProgramadoObservacionDTO extends AbstractDTO
{
	protected $repoIdObservacion; // int
	protected $repoIdDetalle; // int
	protected $repoIdUsuario; // int
	protected $repoObservacion; // string
	protected $repoFechaHora; // string

	protected $usuarioDTO; // UsuarioDTO

	public function getRepoIdObservacion()
	{
		return $this->repoIdObservacion;
	}

	public function setRepoIdObservacion(int $repoIdObservacion = null)
	{
		$this->repoIdObservacion = $repoIdObservacion;
	}

	public function getRepoIdDetalle()
	{
		return $this->repoIdDetalle;
	}

	public function setRepoIdDetalle(int $repoIdDetalle = null)
	{
		$this->repoIdDetalle = $repoIdDetalle;
	}

	public function getRepoIdUsuario()
	{
		return $this->repoIdUsuario;
	}

	public function setRepoIdUsuario(int $repoIdUsuario = null)
	{
		$this->repoIdUsuario = $repoIdUsuario;
	}

	public function getRepoObservacion()
	{
		return $this->repoObservacion;
	}

	public function setRepoObservacion(string $repoObservacion = null)
	{
		$this->repoObservacion = $repoObservacion;
	}

	public function getRepoFechaHora()
	{
		return $this->repoFechaHora;
	}

	public function setRepoFechaHora(string $repoFechaHora = null)
	{
		$this->repoFechaHora = $repoFechaHora;
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
