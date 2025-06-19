<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/TipoUsuarioEnum.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/BaseHandler.php";

class AuthHandler extends BaseHandler
{
	private $allowedUserTypes;

	public function __construct(array $allowedUserTypes = array())
	{
		$this->allowedUserTypes = $allowedUserTypes;
	}

	public function handle()
	{
		RestCommons::startSession();
		$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);

		if ($usuarioSesionDTO == null) {
			RestCommons::respondWithStatus(401);
			exit;
		} else if (in_array(TipoUsuarioEnum::ALL, $this->allowedUserTypes)) {
			// Permite pasar
		} else if (!in_array($usuarioSesionDTO->getTipoUsuario(), $this->allowedUserTypes)) {
			RestCommons::respondWithStatus(403);
			exit;
		}

		parent::handle();
	}
}
