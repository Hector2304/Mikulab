<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class UsuarioDTO extends AbstractDTO
{

	protected $usuaIdUsuario; // int
	protected $usuaIdTipoUsuario; // int
	protected $usuaUsuario; // string
	protected $usuaContrasena; // string
	protected $usuaNombre; // string
	protected $usuaApaterno; // string
	protected $usuaAmaterno; // string
	protected $usuaStatus; // string

	protected $tiusNombre; // string

	public function getUsuaIdUsuario() //: int
	{
		return $this->usuaIdUsuario;
	}

	public function setUsuaIdUsuario(int $usuaIdUsuario = null)
	{
		$this->usuaIdUsuario = $usuaIdUsuario;
	}

	public function getUsuaIdTipoUsuario() //: int
	{
		return $this->usuaIdTipoUsuario;
	}

	public function setUsuaIdTipoUsuario(int $usuaIdTipoUsuario = null)
	{
		$this->usuaIdTipoUsuario = $usuaIdTipoUsuario;
	}

	public function getUsuaUsuario() //: string
	{
		return $this->usuaUsuario;
	}

	public function setUsuaUsuario(string $usuaUsuario = null)
	{
		$this->usuaUsuario = $usuaUsuario;
	}

	public function getUsuaContrasena() //: string
	{
		return $this->usuaContrasena;
	}

	public function setUsuaContrasena(string $usuaContrasena = null)
	{
		$this->usuaContrasena = $usuaContrasena;
	}

	public function getUsuaNombre() //: string
	{
		return $this->usuaNombre;
	}

	public function setUsuaNombre(string $usuaNombre = null)
	{
		$this->usuaNombre = $usuaNombre;
	}

	public function getUsuaApaterno() //: string
	{
		return $this->usuaApaterno;
	}

	public function setUsuaApaterno(string $usuaApaterno = null)
	{
		$this->usuaApaterno = $usuaApaterno;
	}

	public function getUsuaAmaterno() //: string
	{
		return $this->usuaAmaterno;
	}

	public function setUsuaAmaterno(string $usuaAmaterno = null)
	{
		$this->usuaAmaterno = $usuaAmaterno;
	}

	public function getTiusNombre() //: string
	{
		return $this->tiusNombre;
	}

	public function setTiusNombre(string $tiusNombre = null)
	{
		$this->tiusNombre = $tiusNombre;
	}

	public function getUsuaStatus() //: string
	{
		return $this->usuaStatus;
	}

	public function setUsuaStatus(string $usuaStatus = null)
	{
		$this->usuaStatus = $usuaStatus;
	}
}
