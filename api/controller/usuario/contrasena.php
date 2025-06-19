<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/UsuarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class UsuarioContrasena extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->status = trim($jsonData->status);

			$errors = array();

			if (empty($jsonData->contrasena)) {
				$errors[] = "CONTRASENA_EMPTY";
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

// Verifica si la contraseña ya es un hash (bcrypt empieza con $2y$)
            $contrasena = trim($jsonData->contrasena);
            if (!preg_match('/^\$2y\$/', $contrasena)) {
                $contrasena = password_hash($contrasena, PASSWORD_BCRYPT);
            }

            $usuarioDTO->setUsuaContrasena($contrasena);

			$usuarioDAO->setContrasena($usuarioDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new UsuarioContrasena);
