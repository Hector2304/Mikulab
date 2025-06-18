<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

class BitacoraHistoricoDTO extends AbstractDTO
{
	protected $bihiIdHistorico; // int
	protected $bihiIdBitacora; // int
	protected $bihiIdUsuario; // int
	protected $biobFechaHora; // string
	protected $biobAccion; // string

	protected $usuarioDTO; // UsuarioDTO

	public function getBihiIdHistorico()
	{
		return $this->bihiIdHistorico;
	}

	public function setBihiIdHistorico(int $bihiIdHistorico = null)
	{
		$this->bihiIdHistorico = $bihiIdHistorico;
	}

	public function getBihiIdBitacora()
	{
		return $this->bihiIdBitacora;
	}

	public function setBihiIdBitacora(int $bihiIdBitacora = null)
	{
		$this->bihiIdBitacora = $bihiIdBitacora;
	}

	public function getBihiIdUsuario()
	{
		return $this->bihiIdUsuario;
	}

	public function setBihiIdUsuario(int $bihiIdUsuario = null)
	{
		$this->bihiIdUsuario = $bihiIdUsuario;
	}

	public function getBiobFechaHora()
	{
		return $this->biobFechaHora;
	}

	public function setBiobFechaHora(string $biobFechaHora = null)
	{
		$this->biobFechaHora = $biobFechaHora;
	}

	public function getBiobAccion()
	{
		return $this->biobAccion;
	}

	public function setBiobAccion(string $biobAccion = null)
	{
		$this->biobAccion = $biobAccion;
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
