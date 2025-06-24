<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/UsuarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class UsuarioStatus extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$errors = array();

			if (empty($jsonData->status)) {
				$errors[] = "STATUS_EMPTY";
			}
			if (!$jsonData->id) {
				$errors[] = "ID_USUARIO_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$usuarioDAO = new UsuarioDAO(ReservacionesBD::getInstance());

			$usuarioDTO = new UsuarioDTO;
			$usuarioDTO->setUsuaIdUsuario($jsonData->id);
			$usuarioDTO->setUsuaStatus($jsonData->status);

			$usuarioDAO->setStatus($usuarioDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new UsuarioStatus);
