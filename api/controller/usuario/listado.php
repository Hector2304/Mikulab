<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/UsuarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class UsuarioListado extends BaseHandler
{
	public function handle()
	{
		try {
			$usuarioDAO = new UsuarioDAO(ReservacionesBD::getInstance());

			$usuarios = array();

			foreach ($usuarioDAO->consultaAll() as $usuarioDTO) {
				$usuarioDTO->setUsuaContrasena(null);
				$usuarios[] = $usuarioDTO->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $usuarios);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new UsuarioListado);
