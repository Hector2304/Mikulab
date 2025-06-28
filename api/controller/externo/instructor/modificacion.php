<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/InstructorExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/InstructorExternoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class ExternoInstructorModificacion extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->nombre = trim($jsonData->nombre);
			$jsonData->aPaterno = trim($jsonData->aPaterno);
			$jsonData->aMaterno = trim($jsonData->aMaterno);
			$jsonData->telefono = trim($jsonData->telefono);
			$jsonData->correo = trim($jsonData->correo);

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
			if (empty($jsonData->telefono)) {
				$errors[] = "TELEFONO_EMPTY";
			}
			if (empty($jsonData->correo)) {
				$errors[] = "CORREO_EMPTY";
			}
			if (!$jsonData->id) {
				$errors[] = "ID_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$instructorDAO = new InstructorExternoDAO(ReservacionesBD::getInstance());

			$instructorDTO = new InstructorExternoDTO;
			$instructorDTO->setInexIdInstructorExterno($jsonData->id);
			$instructorDTO->setInexNombre($jsonData->nombre);
			$instructorDTO->setInexApaterno($jsonData->aPaterno);
			$instructorDTO->setInexAmaterno($jsonData->aMaterno);
			$instructorDTO->setInexTelefono($jsonData->telefono);
			$instructorDTO->setInexCorreo($jsonData->correo);

			$instructorDAO->modificacion($instructorDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new ExternoInstructorModificacion);
