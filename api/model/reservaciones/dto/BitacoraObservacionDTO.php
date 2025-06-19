<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

class BitacoraObservacionDTO extends AbstractDTO
{
	protected $biobIdObservacion; // int
	protected $biobIdDetalle; // int
	protected $biobIdUsuario; // int
	protected $biobObservacion; // string
	protected $biobFechaHora; // string

	protected $usuarioDTO; // UsuarioDTO

	public function getBiobIdObservacion()
	{
		return $this->biobIdObservacion;
	}

	public function setBiobIdObservacion(int $biobIdObservacion = null)
	{
		$this->biobIdObservacion = $biobIdObservacion;
	}

	public function getBiobIdDetalle()
	{
		return $this->biobIdDetalle;
	}

	public function setBiobIdDetalle(int $biobIdDetalle = null)
	{
		$this->biobIdDetalle = $biobIdDetalle;
	}

	public function getBiobIdUsuario()
	{
		return $this->biobIdUsuario;
	}

	public function setBiobIdUsuario(int $biobIdUsuario = null)
	{
		$this->biobIdUsuario = $biobIdUsuario;
	}

	public function getBiobObservacion()
	{
		return $this->biobObservacion;
	}

	public function setBiobObservacion(string $biobObservacion = null)
	{
		$this->biobObservacion = $biobObservacion;
	}

	public function getBiobFechaHora()
	{
		return $this->biobFechaHora;
	}

	public function setBiobFechaHora(string $biobFechaHora = null)
	{
		$this->biobFechaHora = $biobFechaHora;
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
