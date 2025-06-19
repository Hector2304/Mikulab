<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/UsuarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class UsuarioModificacion extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->nombre = trim($jsonData->nombre);
			$jsonData->aPaterno = trim($jsonData->aPaterno);
			$jsonData->aMaterno = trim($jsonData->aMaterno);

			$errors = array();

			if (empty($jsonData->nombre)) {
				$errors[] = "NOMBRE_EMPTY";
			}
			if (empty($jsonData->aPaterno)) {
				$errors[] = "APATERNO_EMPTY";
			}
			if (empty($jsonData->aMaterno)) {
				$errors[] = "AMATERNO_EMPTY";
			}
			if (!$jsonData->tipoUsuario) {
				$errors[] = "TIPO_USUARIO_EMPTY";
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
			$usuarioDTO->setUsuaNombre($jsonData->nombre);
			$usuarioDTO->setUsuaApaterno($jsonData->aPaterno);
			$usuarioDTO->setUsuaAmaterno($jsonData->aMaterno);
			$usuarioDTO->setUsuaIdTipoUsuario($jsonData->tipoUsuario);

			$usuarioDAO->modificacion($usuarioDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new UsuarioModificacion);
