<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/UsuarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class UsuarioAlta extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->usuario = trim($jsonData->usuario);
			$jsonData->nombre = trim($jsonData->nombre);
			$jsonData->aPaterno = trim($jsonData->aPaterno);
			$jsonData->aMaterno = trim($jsonData->aMaterno);

			$errors = array();

			if (empty($jsonData->usuario)) {
				$errors[] = "USUARIO_EMPTY";
			}
			if (empty($jsonData->contrasena)) {
				$errors[] = "CONTRASENA_EMPTY";
			}
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

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$usuarioDAO = new UsuarioDAO(ReservacionesBD::getInstance());

			$usuarioDTO = new UsuarioDTO;
			$usuarioDTO->setUsuaUsuario($jsonData->usuario);
			$usuarioDTO->setUsuaContrasena(password_hash($jsonData->contrasena, PASSWORD_BCRYPT));
			$usuarioDTO->setUsuaNombre($jsonData->nombre);
			$usuarioDTO->setUsuaApaterno($jsonData->aPaterno);
			$usuarioDTO->setUsuaAmaterno($jsonData->aMaterno);
			$usuarioDTO->setUsuaIdTipoUsuario($jsonData->tipoUsuario);
			$usuarioDTO->setUsuaStatus('A');
			// TODO TiusNombre
			// $usuarioDTO->setTiusNombre();

			$usuarioDTO->setUsuaIdUsuario($usuarioDAO->alta($usuarioDTO));

			$usuarioDTO->setUsuaContrasena(null);

			RestCommons::respondWithStatus(200, $usuarioDTO->getObjectVars());
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new UsuarioAlta);
