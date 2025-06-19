<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/TipoUsuarioEnum.php";

class UsuarioSesionDTO extends AbstractDTO
{
	protected $objetoUsuario; // any/mixed
	protected $tipoUsuario; // string

	public function getObjetoUsuario()//: any/mixed
	{
		return $this->objetoUsuario;
	}

	public function setObjetoUsuario($objetoUsuario = null)
	{
		$this->objetoUsuario = $objetoUsuario;
	}

	public function getTipoUsuario()//: string
	{
		return $this->tipoUsuario;
	}

	public function setTipoUsuario(string $tipoUsuario = null)
	{
		$this->tipoUsuario = $tipoUsuario;
	}
}
